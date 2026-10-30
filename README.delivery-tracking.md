# Delivery Tracking API

JWT-authenticated delivery tracking endpoints for users, delivery men, and admins.

## Statuses

The API exposes four tracking statuses:

```text
PENDING
ACCEPTED
ON_THE_WAY
DELIVERED
```

Internally these map to the existing `orders.status`, `orders.dispatcher_id`,
and `orders.delivery_man_status` fields.

## Public Tracking

```http
GET /api/deliveries/track/{tracking_id}
```

Returns public tracking information and timeline without personal phone/email
details.

## User / Delivery Man Tracking

```http
GET /api/deliveries?status=ON_THE_WAY&search=SIO123&per_page=15
POST /api/deliveries/{tracking_id}/status
```

Delivery men can update deliveries assigned to them. Users can list deliveries
linked to their customer profile.

Update status:

```json
{
  "status": "ON_THE_WAY",
  "current_location_country_id": 161,
  "current_location_city_id": 30512,
  "note": "Package left pickup hub"
}
```

## Admin Monitor

```http
GET /api/admin/deliveries
GET /api/admin/deliveries/{tracking_id}
POST /api/admin/deliveries/{tracking_id}/assign-delivery-man
POST /api/admin/deliveries/{tracking_id}/status
```

Assign delivery man:

```json
{
  "delivery_man_id": 12,
  "note": "Assigned from admin console"
}
```

Admin filters:

```http
GET /api/admin/deliveries?status=DELIVERED&delivery_man_id=12&from=2026-05-01&to=2026-05-05
```

Run migrations before using tracking events:

```bash
php artisan migrate
```

## Internal Tracking Integration

Internal tracking systems can sync status events with an API key.

Configure:

```env
INTERNAL_TRACKING_API_KEY=your-shared-secret
```

Send the key as `Authorization: Bearer ...` or `X-Internal-Tracking-Key`.

```http
POST /api/internal/tracking/events
```

Single event:

```json
{
  "tracking_id": "SIOCARGO20260522123456ABC123",
  "status": "ON_THE_WAY",
  "note": "Departed internal logistics hub",
  "source": "warehouse-scanner",
  "event_reference": "SCAN-10001",
  "occurred_at": "2026-05-22T10:30:00Z",
  "current_location_country_id": 161,
  "current_location_city_id": 30512,
  "metadata": {
    "hub": "Main Hub"
  }
}
```

Bulk sync:

```json
{
  "events": [
    {
      "tracking_id": "SIOCARGO20260522123456ABC123",
      "status": "ACCEPTED",
      "note": "Received at hub"
    },
    {
      "tracking_id": "SIOCARGO20260522123456ABC123",
      "status": "ON_THE_WAY",
      "note": "Departed hub"
    }
  ]
}
```

Fetch internal tracking details:

```http
GET /api/internal/tracking/{tracking_id}
```
