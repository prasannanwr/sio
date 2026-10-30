<?php

namespace App\Services;

use App\Models\DeliveryManPayout;
use App\Models\DeliveryManPayoutItem;
use App\Models\DeliveryManPayoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class DeliveryPayoutPaymentService
{
    public function __construct(protected MonnifyService $monnify)
    {
    }

    public function initiatePayout(DeliveryManPayout $payout, ?Request $request = null): DeliveryManPayout
    {
        try {
            $payout->loadMissing('deliveryMan.bankDetail');
            $transferPayload = $this->transferPayload($payout);

            $payout->update([
                'status' => DeliveryManPayout::STATUS_PROCESSING,
                'payout_method' => 'monnify',
                'payment_reference' => $payout->reference,
                'metadata' => $this->withMonnifyMetadata($payout, [
                    'request' => $transferPayload,
                ]),
            ]);

            DeliveryManPayoutItem::where('delivery_man_payout_id', $payout->id)->update([
                'status' => DeliveryManPayoutItem::STATUS_PROCESSING,
            ]);

            $response = $this->monnify->initiateSingleTransfer($transferPayload);

            return $this->applyMonnifyResponse($payout->fresh(), $response, $request, 'monnify_initiated');
        } catch (Throwable $e) {
            $payout = $payout->fresh();
            $this->markFailed($payout, $request, 'Monnify payout initiation failed.', [
                'error' => $e->getMessage(),
            ]);

            return $payout->fresh();
        }
    }

    public function authorizeOtp(DeliveryManPayout $payout, string $authorizationCode, ?Request $request = null): DeliveryManPayout
    {
        try {
            $response = $this->monnify->authorizeSingleTransfer($payout->reference, $authorizationCode);

            return $this->applyMonnifyResponse($payout, $response, $request, 'monnify_otp_authorized');
        } catch (Throwable $e) {
            $this->log($payout, 'monnify_otp_failed', $request, $payout->status, $payout->status, 'Monnify OTP authorization failed.', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function resendOtp(DeliveryManPayout $payout, ?Request $request = null): array
    {
        $response = $this->monnify->resendSingleTransferOtp($payout->reference);

        $payout->update([
            'metadata' => $this->withMonnifyMetadata($payout, [
                'last_otp_resend_response' => $response,
                'last_otp_resend_at' => now()->toIso8601String(),
            ]),
        ]);

        $this->log($payout->fresh(), 'monnify_otp_resent', $request, $payout->status, $payout->status, 'Monnify payout OTP resent.', $response);

        return $response;
    }

    public function refreshStatus(DeliveryManPayout $payout, ?Request $request = null): DeliveryManPayout
    {
        $response = $this->monnify->getSingleTransferStatus($payout->reference);

        return $this->applyMonnifyResponse($payout, $response, $request, 'monnify_refreshed');
    }

    public function walletBalance(): array
    {
        return $this->monnify->getDisbursementWalletBalance();
    }

    public function syncWebhook(array $eventData, ?string $eventType = null): ?DeliveryManPayout
    {
        $reference = $eventData['reference'] ?? null;

        if (! $reference) {
            return null;
        }

        $payout = DeliveryManPayout::where('reference', $reference)->first();

        if (! $payout) {
            return null;
        }

        return $this->applyMonnifyResponse($payout, $eventData, null, strtolower((string) $eventType ?: 'monnify_webhook'));
    }

    public function applyMonnifyResponse(DeliveryManPayout $payout, array $response, ?Request $request = null, string $action = 'monnify_updated'): DeliveryManPayout
    {
        $oldStatus = $payout->status;
        $newStatus = $this->localStatus($response);

        if ($oldStatus === DeliveryManPayout::STATUS_PAID && $newStatus === DeliveryManPayout::STATUS_PROCESSING) {
            $newStatus = DeliveryManPayout::STATUS_PAID;
        }

        $paidAt = $newStatus === DeliveryManPayout::STATUS_PAID
            ? ($payout->paid_at ?: now())
            : $payout->paid_at;

        $payout->update([
            'status' => $newStatus,
            'payout_method' => 'monnify',
            'payment_reference' => $response['reference'] ?? $payout->payment_reference ?? $payout->reference,
            'metadata' => $this->withMonnifyMetadata($payout, [
                'last_response' => $response,
                'last_status' => $response['status'] ?? $response['batchStatus'] ?? null,
                'transaction_reference' => $response['transactionReference'] ?? Arr::get($payout->metadata ?? [], 'monnify.transaction_reference'),
                'fee' => $response['fee'] ?? $response['totalFee'] ?? Arr::get($payout->metadata ?? [], 'monnify.fee'),
                'updated_at' => now()->toIso8601String(),
            ]),
            'paid_at' => $paidAt,
        ]);

        $this->syncItemStatuses($payout->fresh(), $newStatus);

        if ($newStatus === DeliveryManPayout::STATUS_PAID) {
            $this->recordWalletPayout($payout->fresh());
        }

        $this->log($payout->fresh(), $action, $request, $oldStatus, $newStatus, 'Monnify payout status updated.', $response);

        return $payout->fresh(['deliveryMan.bankDetail', 'processedBy', 'items']);
    }

    protected function transferPayload(DeliveryManPayout $payout): array
    {
        $deliveryMan = $payout->deliveryMan;
        $bankDetail = $deliveryMan ? $deliveryMan->bankDetail : null;

        if (! $deliveryMan || ! $bankDetail) {
            throw new RuntimeException('Delivery man bank details are required before payout.');
        }

        $accountNumber = (string) ($bankDetail->acount_number ?: $bankDetail->iban);
        $bankCode = (string) ($bankDetail->bank_code ?: $bankDetail->routing_number);

        if ($accountNumber === '' || $bankCode === '') {
            throw new RuntimeException('Delivery man account number and Monnify bank code are required before payout.');
        }

        $currency = strtoupper((string) $payout->currency);

        if ($currency !== 'NGN') {
            throw new RuntimeException('Monnify disbursements support NGN payouts only.');
        }

        $accountName = (string) ($bankDetail->account_name ?: $deliveryMan->name);
        $bankName = $bankDetail->bank_name;

        if (config('monnify.disbursement.verify_account_before_transfer', true)) {
            $nameEnquiry = $this->monnify->validateBankAccount($accountNumber, $bankCode);
            $accountName = (string) ($nameEnquiry['accountName'] ?? $accountName);
            $bankName = $nameEnquiry['bankName'] ?? $bankName;
            $bankCode = (string) ($nameEnquiry['bankCode'] ?? $bankCode);
        }

        if ($accountName === '') {
            throw new RuntimeException('Destination account name is required before Monnify payout.');
        }

        return [
            'amount' => (float) $payout->total_amount,
            'reference' => $payout->reference,
            'narration' => Str::limit('Delivery man payout ' . $payout->reference, 100, ''),
            'destinationBankCode' => $bankCode,
            'destinationAccountNumber' => $accountNumber,
            'destinationAccountName' => $accountName,
            'currency' => $currency,
            'sourceAccountNumber' => config('monnify.disbursement.source_account_number'),
            'async' => (bool) config('monnify.disbursement.async', true),
            'metadata' => [
                'destination_bank_name' => $bankName,
                'delivery_man_id' => $deliveryMan->id,
            ],
        ];
    }

    protected function localStatus(array $response): string
    {
        $status = strtoupper((string) ($response['status'] ?? $response['batchStatus'] ?? ''));

        if (in_array($status, ['SUCCESS', 'COMPLETED'], true)) {
            return DeliveryManPayout::STATUS_PAID;
        }

        if (in_array($status, ['FAILED', 'REVERSED', 'EXPIRED'], true)) {
            return DeliveryManPayout::STATUS_FAILED;
        }

        return DeliveryManPayout::STATUS_PROCESSING;
    }

    protected function markFailed(DeliveryManPayout $payout, ?Request $request, string $message, array $payload): void
    {
        $oldStatus = $payout->status;

        $payout->update([
            'status' => DeliveryManPayout::STATUS_FAILED,
            'payout_method' => 'monnify',
            'metadata' => $this->withMonnifyMetadata($payout, [
                'last_error' => $payload,
                'updated_at' => now()->toIso8601String(),
            ]),
        ]);

        $this->syncItemStatuses($payout->fresh(), DeliveryManPayout::STATUS_FAILED);
        $this->log($payout->fresh(), 'monnify_failed', $request, $oldStatus, DeliveryManPayout::STATUS_FAILED, $message, $payload);
    }

    protected function syncItemStatuses(DeliveryManPayout $payout, string $status): void
    {
        $itemStatus = match ($status) {
            DeliveryManPayout::STATUS_PAID => DeliveryManPayoutItem::STATUS_PAID,
            DeliveryManPayout::STATUS_FAILED => DeliveryManPayoutItem::STATUS_FAILED,
            DeliveryManPayout::STATUS_CANCELLED => DeliveryManPayoutItem::STATUS_CANCELLED,
            default => DeliveryManPayoutItem::STATUS_PROCESSING,
        };

        DeliveryManPayoutItem::where('delivery_man_payout_id', $payout->id)->update([
            'status' => $itemStatus,
            'paid_at' => $itemStatus === DeliveryManPayoutItem::STATUS_PAID ? now() : null,
        ]);
    }

    protected function withMonnifyMetadata(DeliveryManPayout $payout, array $data): array
    {
        $metadata = $payout->metadata ?: [];
        $metadata['monnify'] = array_merge($metadata['monnify'] ?? [], $data);

        return $metadata;
    }

    protected function recordWalletPayout(DeliveryManPayout $payout): void
    {
        if (function_exists('accountTxExists') && accountTxExists($payout->reference)) {
            return;
        }

        if (function_exists('updateAccountBalance')) {
            updateAccountBalance(
                $payout->delivery_man_id,
                $payout->total_amount,
                $payout->reference,
                'credit',
                'Delivery man payout ' . $payout->reference,
                'payout',
                $payout->currency
            );
        }
    }

    protected function log(?DeliveryManPayout $payout, string $action, ?Request $request, ?string $oldStatus, ?string $newStatus, string $message, array $payload = []): void
    {
        DeliveryManPayoutLog::create([
            'delivery_man_payout_id' => $payout ? $payout->id : null,
            'payout_reference' => $payout ? $payout->reference : null,
            'action' => $action,
            'performed_by' => $request && $request->user() ? $request->user()->id : null,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'message' => $message,
            'payload' => $payload,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? Str::limit((string) $request->userAgent(), 255, '') : null,
        ]);
    }
}
