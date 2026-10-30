# VTU Services API

This module exposes JWT-authenticated endpoints for the mobile app VTU services:

- Airtime
- Data
- Cable TV
- Electricity
- Bills

It saves each VTU transaction in `vtu_transactions`, returns user history, and provides a dashboard summary.

## Setup

Run the migration:

```bash
php artisan migrate
```

The service works in mock mode by default so the mobile app can integrate immediately:

```env
VTU_PROVIDER_DRIVER=mock
VTU_CURRENCY=NGN
VTU_REQUIRE_SUCCESSFUL_PAYMENT=true
```

To connect a real provider:

```env
VTU_PROVIDER_DRIVER=http
VTU_PROVIDER_BASE_URL=https://provider.example.com
VTU_PROVIDER_API_KEY=your-provider-api-key
VTU_PROVIDER_SECRET_KEY=your-provider-secret-if-needed
VTU_PROVIDER_AUTH_SCHEME=Bearer
VTU_PROVIDER_API_KEY_HEADER=Authorization

VTU_PROVIDER_PROVIDERS_PATH=/vtu/{service_type}/providers
VTU_PROVIDER_PRODUCTS_PATH=/vtu/{service_type}/{provider_code}/products
VTU_PROVIDER_VALIDATE_PATH=/vtu/validate-customer
VTU_PROVIDER_PURCHASE_PATH=/vtu/purchase
VTU_PROVIDER_STATUS_PATH=/vtu/transactions/{reference}
```

## Monnify Bills Payment

Airtime, mobile data, cable TV, electricity and education PINs (`bills`) are sold through
Monnify Bills Payment, using the Monnify credentials already configured for payments (`.env` or the admin Monnify configuration). Each purchase is
paid from the merchant's Monnify wallet, so keep the wallet funded.

```env
VTU_PROVIDER_DRIVER=monnify
VTU_MONNIFY_SERVICES=airtime,data,cable_tv,electricity,bills
```

Only list a service once its billers have been checked against the live Monnify account.

Remove `VTU_PROVIDER_BASE_URL`, `VTU_PROVIDER_API_KEY` and `VTU_PROVIDER_SECRET_KEY`;
the Monnify driver does not use them.

- Every provider list (networks, TV providers, electricity companies, exam bodies) comes from
  `vtu.monnify.billers` in `config/vtu.php`, under the codes the app uses. Monnify is not asked
  for it: its biller list ignores the category filter and returns over 5,000 billers. Monnify's
  product catalogue (it ignores the biller filter as well) is fetched once, shared by every
  biller and cached for `VTU_MONNIFY_CACHE_MINUTES` (default 60).
- Airtime uses each network's open-amount "Mobile Top up" product. If Monnify ever
  offers more than one candidate, pin the product code with `VTU_MONNIFY_AIRTIME_MTN`
  (or `_AIRTEL`, `_GLO`, `_9MOBILE`).
- Data bundles have fixed prices. The app sends a bundle `code` from
  `GET /api/vtu/products?service_type=data&provider_code=mtn` as `product_code`, with
  `amount` equal to that bundle's `amount`. Any other amount is rejected before payment.
- A listed biller with nothing to buy on Monnify returns `503` "… is not available right now."
  before the customer pays.
- Each electricity company is listed twice, for example `ikeja-electric-prepaid` and
  `ikeja-electric-postpaid`. The purchase uses that biller's open-amount product, with
  Monnify's minimum and maximum amounts.
- Cable TV bouquets and education PINs have fixed prices: send the product `code` and
  `amount` from `GET /api/vtu/products`. `POST /api/vtu/validate-customer` returns the account
  holder as `data.data.customer_name`.
- Transaction responses (`GET /api/vtu/transactions/{reference}` and `vtu_transaction` in
  `GET /api/payments/verify/{payment_reference}`) include `purchase_details` with `token`,
  `units` or `pins` when Monnify returns them. Monnify does not document these field names,
  so confirm them with the first live purchase of each service.
- The phone number is validated with Monnify before the customer pays
  (`VTU_MONNIFY_VALIDATE_BEFORE_PAYMENT=true`). A rejected number returns `422` with
  Monnify's reason.
- Services not listed in `VTU_MONNIFY_SERVICES` return `503` with
  "<Service> purchases are not available yet.", except for the user IDs in
  `VTU_MONNIFY_TEST_USERS` (for example `VTU_MONNIFY_TEST_USERS=42,57`), who can use every
  service. This lets a service be tested live before customers see it. `GET /api/vtu/services`
  includes `available` for the signed-in user. The check applies when a purchase starts; a
  purchase that was already paid for is completed even if its service is switched off later.
- A purchase whose result Monnify has not confirmed (timeout or server error) stays
  `PROCESSING` instead of failing, so it is never bought twice.
  `php artisan vtu:reconcile` runs every five minutes to requery those purchases, and
  lists paid purchases that failed and need a refund. The server needs the scheduler cron:

  ```
  * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
  ```

The Monnify sandbox rejects every phone number at validation, so confirm the flow with a
small live purchase.

VTU fulfillment requires a successful Monnify payment by default. To allow
direct mock fulfillment during local development only, set:

```env
VTU_REQUIRE_SUCCESSFUL_PAYMENT=false
```

The recommended Monnify flow is now:

1. Call `POST /api/vtu/payments/initialize` with the VTU purchase details.
2. Open the returned `payment.checkout_url` in a WebView, or launch the Monnify SDK with the returned `payment.payment_reference`.
3. After the SDK closes, call `GET /api/payments/verify/{paymentReference}`.
4. The payment webhook or verify endpoint will fulfill the VTU transaction after Monnify reports `PAID` or `OVERPAID`.

The older `/api/vtu/purchase` endpoint still supports direct fulfillment. When `VTU_REQUIRE_SUCCESSFUL_PAYMENT=true`, it requires a successful Monnify `payment_reference` before fulfillment.

## Auth

All endpoints require the JWT token returned from:

```http
POST /api/auth/login
Authorization: Bearer <access_token>
```

## Endpoints

### Dashboard

```http
GET /api/vtu/dashboard
```

Returns summary counts, total successful spend, service breakdown, available services, and recent transactions.

### Services

```http
GET /api/vtu/services
```

### Providers

```http
GET /api/vtu/providers?service_type=airtime
```

### Products

```http
GET /api/vtu/products?service_type=data&provider_code=mtn
```

### Validate Customer

```http
POST /api/vtu/validate-customer
```

```json
{
  "service_type": "electricity",
  "provider_code": "ikeja-electric",
  "customer_reference": "12345678901",
  "amount": 5000
}
```

### Initialize VTU Payment

```http
POST /api/vtu/payments/initialize
```

```json
{
  "service_type": "airtime",
  "provider_code": "mtn",
  "customer_reference": "08012345678",
  "amount": 500,
  "payment_methods": ["CARD", "ACCOUNT_TRANSFER"],
  "metadata": {
    "source": "mobile_app"
  }
}
```

Returns a pending VTU transaction and Monnify payment details:

```json
{
  "status": true,
  "message": "VTU payment initialized successfully.",
  "data": {
    "transaction": {
      "reference": "VTU-20260505120000-ABCDEFGH",
      "payment_reference": "VTU-PAY-A1B2C3D4E5-1777992000",
      "status": "PENDING"
    },
    "payment": {
      "payment_reference": "VTU-PAY-A1B2C3D4E5-1777992000",
      "transaction_reference": "MNFY|67|20260505120001|000001",
      "checkout_url": "https://sandbox.sdk.monnify.com/checkout/...",
      "amount": "500.00",
      "currency": "NGN",
      "status": "PENDING",
      "sdk_credentials": {
        "api_key": "MK_TEST_XXXXXXXXXX",
        "contract_code": "1234567890"
      }
    }
  }
}
```

### Purchase

```http
POST /api/vtu/purchase
```

```json
{
  "service_type": "airtime",
  "provider_code": "mtn",
  "customer_reference": "08012345678",
  "amount": 500,
  "payment_reference": "PAY-OPTIONAL-MONNIFY-REFERENCE",
  "metadata": {
    "source": "mobile_app"
  }
}
```

For data, cable TV, and generic bills, include a `product_code` from `GET /api/vtu/products`.
For fixed-price products, `amount` must equal the product's `amount` (the code below is an
example; live codes differ):

```json
{
  "service_type": "data",
  "provider_code": "mtn",
  "product_code": "19523",
  "customer_reference": "08012345678",
  "amount": 500
}
```

### History

```http
GET /api/vtu/transactions?service_type=airtime&status=SUCCESSFUL&per_page=15
```

Filters: `service_type`, `status`, `search`, `from`, `to`, `per_page`.

### Transaction Detail

```http
GET /api/vtu/transactions/{reference}
```

### Refresh Status

```http
POST /api/vtu/transactions/{reference}/refresh
```

This checks the provider status endpoint and updates the saved VTU transaction.
