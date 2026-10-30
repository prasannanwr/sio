# Delivery Man Payout API

This module adds admin-only API endpoints for delivery man payout management:

- View delivery men earnings
- See unpaid delivered orders
- See pending payout batches
- Create a single payout
- Create bulk payouts
- Mark selected deliveries as paid
- View payout history and audit logs
- Send delivery man payouts through Monnify disbursements

Delivery men are read from the existing `orders.dispatcher_id` assignment. A delivery becomes payout-eligible when it is assigned and either:

- `delivery_man_status = Delivered`
- or `status` is one of `delivered`, `Customer Received`, `Accepted By Agent`

Already paid or pending payout deliveries are excluded from new payouts.

## Setup

Run migrations:

```bash
php artisan migrate
```

Optional `.env` values:

```env
DELIVERY_PAYOUT_CURRENCY=NGN
DELIVERY_PAYOUT_DEFAULT_COMMISSION_PERCENTAGE=1
DELIVERY_PAYOUT_PAYMENT_PROVIDER=monnify
MONNIFY_DISBURSEMENT_SOURCE_ACCOUNT_NUMBER=your-monnify-wallet-account-number
MONNIFY_DISBURSEMENT_ASYNC=true
MONNIFY_VERIFY_ACCOUNT_BEFORE_TRANSFER=true
```

For Monnify payouts, each delivery man must have bank details with:

```text
bank_details.acount_number = Nigerian account number
bank_details.bank_code     = Monnify/NIP bank code
bank_details.account_name  = beneficiary name, or use name enquiry
```

The old `routing_number` field is also accepted as a fallback for `bank_code`.

Each delivery payout amount is calculated from:

```text
order.shipping_cost * delivery_man.pickup_comission_percentage / 100
```

If the delivery man has no commission percentage, `DELIVERY_PAYOUT_DEFAULT_COMMISSION_PERCENTAGE` is used.

## Auth

All endpoints require the JWT token from `/api/auth/login`.

The logged-in user must have:

```text
user_type=admin
```

Header:

```http
Authorization: Bearer <access_token>
Accept: application/json
Content-Type: application/json
```

## Endpoints

### Dashboard

```http
GET /api/admin/delivery-payouts/dashboard
```

Returns pending delivery totals, pending payout totals, paid payout totals, and recent payouts.

### Delivery Men Earnings

```http
GET /api/admin/delivery-payouts/delivery-men
```

Optional filters:

```http
GET /api/admin/delivery-payouts/delivery-men?from=2026-05-01&to=2026-05-04
```

### Pending Deliveries

```http
GET /api/admin/delivery-payouts/pending-deliveries
```

Optional filters:

```http
GET /api/admin/delivery-payouts/pending-deliveries?delivery_man_id=12&search=SIO1735146084&per_page=20
```

### Pending Payout Batches

```http
GET /api/admin/delivery-payouts/pending
```

### Make Single Payout

Creates a payout for selected orders. If `order_ids` is omitted, all eligible unpaid deliveries for that delivery man are included.

```http
POST /api/admin/delivery-payouts/make
```

```json
{
  "delivery_man_id": 12,
  "order_ids": [26, 27],
  "payout_method": "monnify",
  "notes": "May payout"
}
```

With Monnify enabled, the payout starts as `PROCESSING`. If MFA is enabled on
your Monnify account, authorize the payout with the OTP endpoint below. Webhooks
or the refresh endpoint finalize the payout as `PAID` or `FAILED`.

### Bulk Payout

```http
POST /api/admin/delivery-payouts/bulk
```

```json
{
  "payout_method": "monnify",
  "payouts": [
    {
      "delivery_man_id": 12,
      "order_ids": [26, 27]
    },
    {
      "delivery_man_id": 13,
      "order_ids": [31, 32]
    }
  ]
}
```

### Mark Deliveries Paid

Useful when the admin selects deliveries from a pending-deliveries screen. The API groups them by delivery man and creates paid payout batches.

```http
POST /api/admin/delivery-payouts/mark-deliveries-paid
```

```json
{
  "order_ids": [26, 27, 31],
  "payout_method": "monnify"
}
```

### Payout History

```http
GET /api/admin/delivery-payouts/history
```

Filters:

```http
GET /api/admin/delivery-payouts/history?status=PAID&delivery_man_id=12&from=2026-05-01&to=2026-05-04
```

### Payout Detail

```http
GET /api/admin/delivery-payouts/{reference}
```

### Mark Pending Payout Paid

```http
POST /api/admin/delivery-payouts/{reference}/mark-paid
```

Manual mark-paid is still available for exceptional offline reconciliation.
Normal payouts should be completed by Monnify webhook, OTP authorization, or
status refresh.

### Monnify Payout Operations

```http
GET /api/admin/delivery-payouts/monnify/wallet-balance
POST /api/admin/delivery-payouts/{reference}/authorize-monnify
POST /api/admin/delivery-payouts/{reference}/resend-monnify-otp
POST /api/admin/delivery-payouts/{reference}/refresh-monnify
```

Authorize OTP:

```json
{
  "authorization_code": "123456"
}
```

```json
{
  "payment_reference": "BANK-REF-003",
  "payout_method": "bank_transfer"
}
```

### Mark Failed or Cancelled

```http
POST /api/admin/delivery-payouts/{reference}/mark-failed
POST /api/admin/delivery-payouts/{reference}/cancel
```

### Logs

```http
GET /api/admin/delivery-payouts/{reference}/logs
```

## Wallet Ledger

When a payout is marked `PAID`, the API records a `user_funds` entry:

```text
flag=credit
status=payout
transId=<payout_reference>
description=Delivery man payout <payout_reference>
```

This keeps the existing wallet balance logic aligned with payout history.
