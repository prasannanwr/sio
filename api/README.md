# Laravel Monnify Payment API

A Laravel-based REST API for accepting and completing payments through the [Monnify](https://developers.monnify.com/) payment gateway, designed to be consumed by an **Android mobile application**.

## Architecture / Flow

```
 Android App                Laravel API                Monnify
     │                          │                          │
     │  1. POST /payments/init  │                          │
     ├─────────────────────────►│                          │
     │                          │  2. Auth + init-tx       │
     │                          ├─────────────────────────►│
     │                          │◄─────────────────────────┤
     │  3. checkout_url + ref   │   transactionReference   │
     │◄─────────────────────────┤                          │
     │                                                     │
     │  4. Open Monnify Android SDK / WebView              │
     ├────────────────────────────────────────────────────►│
     │                          User pays                  │
     │◄────────────────────────────────────────────────────┤
     │                          │                          │
     │                          │  5. Webhook (server side)│
     │                          │◄─────────────────────────┤
     │  6. GET /payments/verify │                          │
     ├─────────────────────────►│  7. Verify with Monnify  │
     │                          ├─────────────────────────►│
     │  8. Final status         │◄─────────────────────────┤
     │◄─────────────────────────┤                          │
```

The webhook is the source of truth. The verify endpoint is the safety net used by the app right after the SDK closes.

## Installation

1. **Drop the files into a fresh Laravel 10/11 project.**

   ```bash
   composer create-project laravel/laravel monnify-api
   cd monnify-api
   composer require laravel/sanctum
   php artisan install:api   # Laravel 11+
   ```

2. **Copy these files into the project root**, preserving folder structure:
   - `config/monnify.php`
   - `app/Services/MonnifyService.php`
   - `app/Models/Transaction.php`
   - `app/Http/Requests/InitializePaymentRequest.php`
   - `app/Http/Controllers/Api/PaymentController.php`
   - `database/migrations/2026_04_28_000000_create_transactions_table.php`
   - `routes/api.php` (merge with the existing one)

3. **Append the Monnify variables** from `.env.monnify.example` to your `.env`:

   ```env
   MONNIFY_API_KEY=MK_TEST_XXXXXXXXXX
   MONNIFY_SECRET_KEY=XXXXXXXXXXXXXXXX
   MONNIFY_CONTRACT_CODE=1234567890
   MONNIFY_BASE_URL=https://sandbox.monnify.com
   MONNIFY_CURRENCY=NGN
   MONNIFY_WEBHOOK_SECRET=your-webhook-secret
   MONNIFY_REDIRECT_URL=yourapp://payment/callback
   ```

   For production: `MONNIFY_BASE_URL=https://api.monnify.com` and use your `MK_PROD_*` keys.

4. **Run the migration**:

   ```bash
   php artisan migrate
   ```

5. **Register the webhook URL** in your Monnify dashboard:
   `https://yourdomain.com/api/payments/webhook`

6. **Exclude the webhook from CSRF** in `app/Http/Middleware/VerifyCsrfToken.php`
   (Laravel 10) or `bootstrap/app.php` (Laravel 11):

   ```php
   $middleware->validateCsrfTokens(except: ['api/payments/webhook']);
   ```

## API Endpoints

All authenticated endpoints expect a Sanctum bearer token in `Authorization: Bearer <token>`.

### 1. Initialize a payment

`POST /api/payments/initialize`

**Request:**

```json
{
  "amount": 5000,
  "customer_name": "Adaeze Okafor",
  "customer_email": "[email protected]",
  "customer_phone": "08012345678",
  "payment_description": "Premium subscription",
  "payment_methods": ["CARD", "ACCOUNT_TRANSFER"],
  "metadata": { "order_id": 42 }
}
```

**Response (201):**

```json
{
  "status": true,
  "message": "Payment initialized successfully",
  "data": {
    "payment_reference": "PAY-A1B2C3D4E5-1714291200",
    "transaction_reference": "MNFY|67|20260428115209|000063",
    "checkout_url": "https://sandbox.sdk.monnify.com/checkout/...",
    "amount": 5000,
    "currency": "NGN",
    "sdk_credentials": {
      "api_key": "MK_TEST_XXXXXXXXXX",
      "contract_code": "1234567890"
    }
  }
}
```

### 2. Verify a payment

`GET /api/payments/verify/{paymentReference}`

Call this from the Android app after the SDK's `onTransactionCompleted` fires.

```json
{
  "status": true,
  "message": "Transaction verified",
  "data": {
    "payment_reference": "PAY-A1B2C3D4E5-1714291200",
    "transaction_reference": "MNFY|67|20260428115209|000063",
    "amount": "5000.00",
    "amount_paid": "5000.00",
    "currency": "NGN",
    "status": "PAID",
    "payment_method": "CARD",
    "paid_at": "2026-04-28T10:53:50.000000Z",
    "is_successful": true
  }
}
```

### 3. Get transaction details

`GET /api/payments/{paymentReference}`

### 4. List transactions

`GET /api/payments?per_page=15`

### 5. Webhook (Monnify -> server)

`POST /api/payments/webhook`

Verified by `monnify-signature` header (HMAC-SHA512 of the raw body using your webhook secret).

## Android App Integration

You have two options. **Option A is the recommended one.**

### Option A: Use the Monnify Android SDK with a server-issued reference

1. Call `POST /api/payments/initialize` first.
2. Use the returned `payment_reference`, `api_key`, and `contract_code` to launch the SDK:

   ```kotlin
   // build.gradle (app)
   implementation "com.monnify.android-sdk:monnify-android-sdk:1.1.7"
   ```

   ```kotlin
   val transaction = TransactionDetails.Builder()
       .amount(BigDecimal("5000"))
       .currencyCode("NGN")
       .customerName("Adaeze Okafor")
       .customerEmail("[email protected]")
       .paymentReference(serverResponse.paymentReference) // from your API
       .paymentDescription("Premium subscription")
       .build()

   Monnify.getInstance().apply {
       setApiKey(serverResponse.apiKey)
       setContractCode(serverResponse.contractCode)
       setApplicationMode(ApplicationMode.TEST)
       initializePayment(activity, transaction) { result ->
           when (result) {
               is TransactionResult.Completed -> verifyOnServer(serverResponse.paymentReference)
               is TransactionResult.Cancelled -> showCancelled()
               else -> showError()
           }
       }
   }
   ```

3. In your `verifyOnServer()` callback, hit `GET /api/payments/verify/{ref}`. Only treat the order as paid if `is_successful: true`.

### Option B: Open the checkout URL in a WebView

Just load `data.checkout_url` in a `WebView` and intercept the redirect URL. After redirect, hit `/api/payments/verify/{ref}`.

## Why two confirmations (verify + webhook)?

- **The SDK's `onComplete` is not trustworthy** - the user can lose connection, kill the app, or a bad actor can fake the response.
- **The verify endpoint** confirms server-to-server with Monnify the moment the user returns.
- **The webhook** catches the case where the user's phone died but the payment still went through.

Always make `paid_at` and `status` updates idempotent (the controller already does this via `syncTransactionFromGateway`).

## Going Live

1. Switch `MONNIFY_BASE_URL` to `https://api.monnify.com`.
2. Use your `MK_PROD_*` keys.
3. In the Android SDK, switch `ApplicationMode.TEST` to `ApplicationMode.LIVE`.
4. Re-register your production webhook URL in the Monnify dashboard.

## Test Cards

See [https://developers.monnify.com/docs/test-cards](https://developers.monnify.com/docs/test-cards).
