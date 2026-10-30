<?php

namespace App\Http\Controllers\Api;

use App\Constraints\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InitializePaymentRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\VtuTransaction;
use App\Services\DeliveryPayoutPaymentService;
use App\Services\MonnifyService;
use App\Services\VtuPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        protected MonnifyService $monnify,
        protected VtuPaymentService $vtuPayments,
        protected DeliveryPayoutPaymentService $deliveryPayoutPayments
    ) {
    }

    /**
     * POST /api/payments/initialize
     *
     * The Android app calls this first. We create a local transaction record,
     * ask Monnify to initialize the transaction and hand the checkout URL back
     * to the app. The app then loads the URL in a WebView (or uses the Monnify
     * Android SDK directly with the same paymentReference).
     */
    public function initialize(InitializePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();

        // When paying for a specific order, take the authoritative amount/currency from the order
        // (so the client can't tamper with what gets charged) and link the payment to it.
        $order = null;
        if (! empty($data['order_tracking_id'])) {
            $order = Order::with('customer')->where('tracking_id', $data['order_tracking_id'])->first();

            if (! $order) {
                return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
            }

            $isOwner = $order->customer && (int) $order->customer->user_id === (int) $request->user()->id;
            if (! $isOwner) {
                return response()->json(['status' => false, 'message' => 'You can only pay for your own order.'], 403);
            }

            if ($order->status !== OrderStatus::UNPAID) {
                return response()->json(['status' => false, 'message' => 'This order is not awaiting payment.'], 422);
            }

            if (! $order->shipping_cost || (float) $order->shipping_cost <= 0) {
                return response()->json(['status' => false, 'message' => 'This order has no payable amount.'], 422);
            }

            $data['amount'] = (float) $order->shipping_cost;
            $data['currency'] = $order->shipping_currency ?: ($data['currency'] ?? config('monnify.currency'));
            $data['payment_description'] = $data['payment_description'] ?? ('Cargo booking ' . $order->tracking_id);
        }

        // Monnify accepts at most 2 decimal places — normalize whatever the client sent / the order holds.
        $data['amount'] = round((float) $data['amount'], 2);

        // Always generate the reference server-side so the client can't reuse one.
        $paymentReference = 'PAY-' . strtoupper(Str::random(10)) . '-' . now()->timestamp;

        $transaction = Transaction::create([
            'payment_reference'   => $paymentReference,
            'user_id'             => $request->user()?->id,
            'order_id'            => $order?->id,
            'customer_name'       => $data['customer_name'],
            'customer_email'      => $data['customer_email'],
            'customer_phone'      => $data['customer_phone'] ?? null,
            'amount'              => $data['amount'],
            'currency'            => $data['currency'] ?? config('monnify.currency'),
            'payment_description' => $data['payment_description'] ?? 'Mobile app payment',
            'metadata'            => $data['metadata'] ?? null,
            'status'              => Transaction::STATUS_PENDING,
        ]);

        // SDK flow: the Monnify mobile SDK initializes the transaction with Monnify itself.
        // We must NOT pre-initialize it server-side here, or the SDK fails with
        // "Duplicate payment reference" (response code 99). Just hand back the reference + payload.
        if ($request->boolean('use_sdk')) {
            return response()->json([
                'status'  => true,
                'message' => 'Payment initialized successfully',
                'data'    => [
                    'payment_reference' => $transaction->payment_reference,
                    'amount'            => (float) $transaction->amount,
                    'currency'          => $transaction->currency,
                    'sdk_credentials'   => [
                        'api_key'       => config('monnify.api_key'),
                        'contract_code' => config('monnify.contract_code'),
                    ],
                    'sdk_payload'       => [
                        'apiKey'             => config('monnify.api_key'),
                        'contractCode'       => config('monnify.contract_code'),
                        'amount'             => (float) $transaction->amount,
                        'currencyCode'       => $transaction->currency,
                        'paymentReference'   => $transaction->payment_reference,
                        'paymentDescription' => $transaction->payment_description,
                        'customerName'       => $transaction->customer_name,
                        'customerEmail'      => $transaction->customer_email,
                        'customerPhone'      => $transaction->customer_phone,
                        'paymentMethods'     => $data['payment_methods'] ?? ['CARD', 'ACCOUNT_TRANSFER'],
                    ],
                ],
            ], 201);
        }

        try {
            $response = $this->monnify->initializeTransaction([
                'amount'             => (float) $transaction->amount,
                'customerName'       => $transaction->customer_name,
                'customerEmail'      => $transaction->customer_email,
                'paymentReference'   => $transaction->payment_reference,
                'paymentDescription' => $transaction->payment_description,
                'currencyCode'       => $transaction->currency,
                'paymentMethods'     => $data['payment_methods'] ?? null,
                'metadata'           => $data['metadata'] ?? null,
            ]);
        } catch (Throwable $e) {
            $transaction->update([
                'status'           => Transaction::STATUS_FAILED,
                'gateway_response' => ['error' => $e->getMessage()],
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Could not initialize payment',
                'error'   => $e->getMessage(),
            ], 502);
        }

        $transaction->update([
            'transaction_reference' => $response['transactionReference'] ?? null,
            'checkout_url'          => $response['checkoutUrl'] ?? null,
            'gateway_response'      => $response,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment initialized successfully',
            'data'    => [
                'payment_reference'     => $transaction->payment_reference,
                'transaction_reference' => $transaction->transaction_reference,
                'checkout_url'          => $transaction->checkout_url, // fallback (WebView) if not using the SDK
                'amount'                => (float) $transaction->amount,
                'currency'              => $transaction->currency,

                // The mobile app needs these to launch the Monnify Android SDK directly.
                'sdk_credentials'       => [
                    'api_key'       => config('monnify.api_key'),
                    'contract_code' => config('monnify.contract_code'),
                ],

                // Everything the Monnify Android/iOS SDK needs to render the in-app payment
                // sheet (card entry, transfer, USSD) — pass these straight into the SDK,
                // then call GET /api/payments/verify/{payment_reference} on completion.
                'sdk_payload'           => [
                    'apiKey'             => config('monnify.api_key'),
                    'contractCode'       => config('monnify.contract_code'),
                    'amount'             => (float) $transaction->amount,
                    'currencyCode'       => $transaction->currency,
                    'paymentReference'   => $transaction->payment_reference,
                    'paymentDescription' => $transaction->payment_description,
                    'customerName'       => $transaction->customer_name,
                    'customerEmail'      => $transaction->customer_email,
                    'customerPhone'      => $transaction->customer_phone,
                    'paymentMethods'     => $data['payment_methods'] ?? ['CARD', 'ACCOUNT_TRANSFER'],
                ],
            ],
        ], 201);
    }

    /**
     * GET /api/payments/verify/{paymentReference}
     *
     * After the SDK's onComplete fires on the Android side, the app calls this
     * to confirm with the server (server -> Monnify) before treating the order
     * as paid. Never trust the SDK's onComplete callback alone.
     */
    public function verify(Request $request, string $paymentReference): JsonResponse
    {
        $transaction = Transaction::where('payment_reference', $paymentReference)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $transaction) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        try {
            $response = $this->monnify->verifyTransactionByReference($paymentReference);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Could not verify payment',
                'error'   => $e->getMessage(),
            ], 502);
        }

        $this->syncTransactionFromGateway($transaction, $response);
        $transaction->refresh();
        $this->fulfillOrderPayment($transaction);

        $vtuTransaction = $this->vtuPayments->fulfillPaidPayment($transaction)
            ?: VtuTransaction::where('payment_reference', $transaction->payment_reference)->first();

        return response()->json([
            'status'  => true,
            'message' => 'Transaction verified',
            'data'    => [
                'payment_reference'     => $transaction->payment_reference,
                'transaction_reference' => $transaction->transaction_reference,
                'amount'                => $transaction->amount,
                'amount_paid'           => $transaction->amount_paid,
                'currency'              => $transaction->currency,
                'status'                => $transaction->status,
                'payment_method'        => $transaction->payment_method,
                'paid_at'               => $transaction->paid_at,
                'is_successful'         => $transaction->isSuccessful(),
                'vtu_transaction'       => $this->vtuTransactionPayload($vtuTransaction),
            ],
        ]);
    }

    /**
     * GET /api/payments/{paymentReference}
     *
     * Returns whatever we currently have on file - useful for polling from the
     * mobile app while a bank transfer is pending.
     */
    public function show(Request $request, string $paymentReference): JsonResponse
    {
        $transaction = Transaction::where('payment_reference', $paymentReference)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $transaction) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $transaction,
        ]);
    }

    /**
     * GET /api/payments
     *
     * Paginated list of the authenticated user's transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::query()->latest();

        if ($user = $request->user()) {
            $query->where('user_id', $user->id);
        }

        $perPage = max(1, min((int) $request->query('per_page', 15), 100));

        return response()->json([
            'status' => true,
            'data'   => $query->paginate($perPage),
        ]);
    }

    // ---------------------------------------------------------------------
    // Webhook
    // ---------------------------------------------------------------------

    /**
     * POST /api/payments/webhook
     *
     * Monnify will hit this URL when a payment completes (or any other event).
     * We validate the SHA-512 signature with our secret key, then update the
     * transaction. This is the source of truth for payment status.
     */
    public function webhook(Request $request): JsonResponse
    {
        $signatureHeader = $request->header('monnify-signature');
        $rawBody         = $request->getContent();
        $secret          = (string) config('monnify.webhook_secret');

        if (! $signatureHeader || $secret === '') {
            Log::warning('Monnify webhook missing signature or secret');
            return response()->json(['status' => false], 400);
        }

        $expected = hash_hmac('sha512', $rawBody, $secret);

        if (! hash_equals($expected, $signatureHeader)) {
            Log::warning('Monnify webhook signature mismatch', [
                'expected' => $expected,
                'received' => $signatureHeader,
            ]);
            return response()->json(['status' => false, 'message' => 'Invalid signature'], 401);
        }

        $payload   = $request->json()->all();
        $eventType = $payload['eventType'] ?? null;
        $eventData = $payload['eventData'] ?? [];

        Log::info('Monnify webhook received', ['event' => $eventType]);

        if (in_array($eventType, ['SUCCESSFUL_DISBURSEMENT', 'FAILED_DISBURSEMENT', 'REVERSED_DISBURSEMENT'], true)) {
            $this->deliveryPayoutPayments->syncWebhook($eventData, $eventType);
        }

        // We only care about successful collections. Add other events as needed.
        if ($eventType === 'SUCCESSFUL_TRANSACTION') {
            $reference = $eventData['paymentReference'] ?? null;

            if ($reference && $transaction = Transaction::where('payment_reference', $reference)->first()) {
                $this->syncTransactionFromGateway($transaction, $eventData);
                $transaction->refresh();
                $this->fulfillOrderPayment($transaction);
                $this->vtuPayments->fulfillPaidPayment($transaction);
            }
        }

        // Always 200 - Monnify will retry non-2xx responses.
        return response()->json(['status' => true]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * Mark a linked order as paid once its transaction is successful (idempotent).
     */
    protected function fulfillOrderPayment(Transaction $transaction): void
    {
        if (! $transaction->order_id || ! $transaction->isSuccessful()) {
            return;
        }

        $order = Order::find($transaction->order_id);

        if ($order && $order->status === OrderStatus::UNPAID) {
            $order->forceFill(['status' => OrderStatus::PLACED])->save();
        }
    }

    /**
     * Apply a Monnify payload to our local Transaction record.
     *
     * @param  array<string,mixed>  $payload
     */
    protected function syncTransactionFromGateway(Transaction $transaction, array $payload): void
    {
        $status = $payload['paymentStatus']
            ?? $payload['status']
            ?? $transaction->status;

        $update = [
            'status'           => $status,
            'amount_paid'      => $payload['amountPaid']  ?? $transaction->amount_paid,
            'fee'              => $payload['fee']         ?? $transaction->fee,
            'payment_method'   => $payload['paymentMethod'] ?? $transaction->payment_method,
            'gateway_response' => $payload,
        ];

        if (! empty($payload['transactionReference'])) {
            $update['transaction_reference'] = $payload['transactionReference'];
        }

        if (in_array($status, [Transaction::STATUS_PAID, Transaction::STATUS_OVERPAID], true)
            && ! $transaction->paid_at) {
            $update['paid_at'] = $payload['completedOn'] ?? $payload['paidOn'] ?? now();
        }

        $transaction->update($update);
    }

    protected function vtuTransactionPayload(?VtuTransaction $transaction): ?array
    {
        if (! $transaction) {
            return null;
        }

        return [
            'reference' => $transaction->reference,
            'payment_reference' => $transaction->payment_reference,
            'provider_reference' => $transaction->provider_reference,
            'service_type' => $transaction->service_type,
            'provider_code' => $transaction->provider_code,
            'product_code' => $transaction->product_code,
            'customer_reference' => $transaction->customer_reference,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'purchase_details' => $transaction->purchaseDetails(),
            'fulfilled_at' => optional($transaction->fulfilled_at)->toIso8601String(),
        ];
    }
}
