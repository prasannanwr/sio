<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitializePaymentRequest;
use App\Models\Transaction;
use App\Services\MonnifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(protected MonnifyService $monnify)
    {
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

        // Always generate the reference server-side so the client can't reuse one.
        $paymentReference = 'PAY-' . strtoupper(Str::random(10)) . '-' . now()->timestamp;

        $transaction = Transaction::create([
            'payment_reference'   => $paymentReference,
            'user_id'             => $request->user()?->id,
            'customer_name'       => $data['customer_name'],
            'customer_email'      => $data['customer_email'],
            'customer_phone'      => $data['customer_phone'] ?? null,
            'amount'              => $data['amount'],
            'currency'            => $data['currency'] ?? config('monnify.currency'),
            'payment_description' => $data['payment_description'] ?? 'Mobile app payment',
            'metadata'            => $data['metadata'] ?? null,
            'status'              => Transaction::STATUS_PENDING,
        ]);

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
                'checkout_url'          => $transaction->checkout_url,
                'amount'                => $transaction->amount,
                'currency'              => $transaction->currency,

                // The mobile app needs these to launch the Monnify Android SDK directly.
                'sdk_credentials'       => [
                    'api_key'       => config('monnify.api_key'),
                    'contract_code' => config('monnify.contract_code'),
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
    public function verify(string $paymentReference): JsonResponse
    {
        $transaction = Transaction::where('payment_reference', $paymentReference)->first();

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
            ],
        ]);
    }

    /**
     * GET /api/payments/{paymentReference}
     *
     * Returns whatever we currently have on file - useful for polling from the
     * mobile app while a bank transfer is pending.
     */
    public function show(string $paymentReference): JsonResponse
    {
        $transaction = Transaction::where('payment_reference', $paymentReference)->first();

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

        return response()->json([
            'status' => true,
            'data'   => $query->paginate($request->integer('per_page', 15)),
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

        // We only care about successful collections. Add other events as needed.
        if (in_array($eventType, ['SUCCESSFUL_TRANSACTION', 'SUCCESSFUL_DISBURSEMENT'], true)) {
            $reference = $eventData['paymentReference'] ?? null;

            if ($reference && $transaction = Transaction::where('payment_reference', $reference)->first()) {
                $this->syncTransactionFromGateway($transaction, $eventData);
            }
        }

        // Always 200 - Monnify will retry non-2xx responses.
        return response()->json(['status' => true]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

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
}
