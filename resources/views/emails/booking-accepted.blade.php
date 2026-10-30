<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Accepted</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family: Arial, Helvetica, sans-serif; color:#333;">
    <div style="max-width:560px; margin:0 auto; padding:32px 24px;">
        <div style="background:#ffffff; border-radius:8px; padding:32px;">
            <h2 style="margin-top:0; color:#111;">Your booking has been accepted</h2>

            <p style="font-size:15px; line-height:1.6;">
                Hello{{ $customerName ? ' ' . $customerName : '' }},
            </p>

            <p style="font-size:15px; line-height:1.6;">
                Good news — your delivery man{{ $deliveryManName ? ' (' . $deliveryManName . ')' : '' }}
                has <strong>accepted</strong> your booking request and will handle your delivery.
            </p>

            <table style="width:100%; border-collapse:collapse; margin:24px 0; font-size:14px;">
                <tr>
                    <td style="padding:8px 0; color:#666;">Tracking ID</td>
                    <td style="padding:8px 0; text-align:right;"><strong>{{ $order->tracking_id }}</strong></td>
                </tr>
                @if ($deliveryManName)
                    <tr>
                        <td style="padding:8px 0; color:#666;">Delivery Man</td>
                        <td style="padding:8px 0; text-align:right;">{{ $deliveryManName }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:8px 0; color:#666;">Pickup</td>
                    <td style="padding:8px 0; text-align:right;">{{ $order->pickup_name }} — {{ $order->pickup_address1 }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#666;">Delivery</td>
                    <td style="padding:8px 0; text-align:right;">{{ $order->delivery_name }} — {{ $order->delivery_address1 }}</td>
                </tr>
                @if ($order->shipping_cost)
                    <tr>
                        <td style="padding:8px 0; color:#666;">Amount</td>
                        <td style="padding:8px 0; text-align:right;">{{ $order->shipping_cost }} {{ $order->shipping_currency }}</td>
                    </tr>
                @endif
            </table>

            <p style="font-size:14px; line-height:1.6; color:#555;">
                You can track your delivery in the app using your tracking ID.
            </p>

            <hr style="border:none; border-top:1px solid #eee; margin:28px 0;">

            <p style="font-size:12px; color:#999; margin:0;">
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
