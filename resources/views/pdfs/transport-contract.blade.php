<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #222; font-size: 12px; line-height: 1.5; }
        .logo { font-size: 20px; font-weight: bold; color: #1f3b73; }
        .offices { font-size: 10px; color: #555; margin: 6px 0 0; }
        .offices strong { color: #222; }
        .rule { border: 0; border-top: 2px solid #333; margin: 14px 0; }
        h2.subject { font-size: 14px; margin: 6px 0 14px; }
        .warn { color: #b00020; font-weight: bold; }
        .section-title { font-weight: bold; margin: 16px 0 4px; }
        .dashed { border: 0; border-top: 1px dashed #999; margin: 2px 0 8px; }
        table.kv { width: 100%; border-collapse: collapse; }
        table.kv td { padding: 3px 0; vertical-align: top; }
        table.kv td.k { width: 220px; color: #555; }
        .sign-box { border: 1px solid #999; padding: 10px; margin-top: 6px; }
        .footer { margin-top: 18px; font-size: 10px; color: #555; }
    </style>
</head>
<body>
    @if (! empty($logoSrc))
        <img src="{{ $logoSrc }}" alt="SIOSHIPPING" style="height:56px; margin-bottom:6px;">
    @else
        <div class="logo">SIOPAY &middot; SIOSHIPPING</div>
    @endif
    <div class="offices">
        <strong><img src="{{ $flags['it'] }}" width="18" style="vertical-align:middle; margin-right:4px;"> Italy — European Headquarters</strong><br>
        SIOPAY MULTISERVIZI SRL | Piazza Porcelli SNC, 88046 Lamezia Terme (CZ) | P.IVA 03940320793<br><br>
        <strong><img src="{{ $flags['ca'] }}" width="18" style="vertical-align:middle; margin-right:4px;"> Canada — North America Operations</strong><br>
        SIOPAY MULTISERVIZI LTD | 2482 Yonge Street, Toronto, Ontario, M4P 2H5<br><br>
        <strong><img src="{{ $flags['ng'] }}" width="18" style="vertical-align:middle; margin-right:4px;"> Nigeria — Logistics Headquarters &amp; Sourcing</strong><br>
        SIOSHIPPING GLOBAL LOGISTICS SERVICE LTD | Agege, Lagos, Nigeria
    </div>

    <hr class="rule">

    <h2 class="subject">CRITICAL: Transport Contract &amp; Driver Verification — Order #{{ $order->id }}</h2>

    <p>Dear {{ $clientName ?: 'Customer' }},</p>

    <p>
        Payment for Order #{{ $order->id }} has been successfully recorded in our CRM system.
        Driver {{ $driverName ?: 'the assigned driver' }} has accepted the transport contract.
    </p>

    <p class="warn">⚠️ LEGAL &amp; SECURITY VERIFICATION REQUIRED</p>
    <p>
        Before the cargo is loaded, you must verify the information below against your CRM/App dashboard.
        <strong>IF ANY INFORMATION IS WRONG, TRANSIT IS NOT ALLOWED.</strong>
    </p>

    <div class="section-title">[ CRM DATA RECORD ]</div>
    <hr class="dashed">
    <table class="kv">
        <tr><td class="k">Contract ID:</td><td>{{ $contractReference }}</td></tr>
        <tr><td class="k">Client Account:</td><td>{{ $clientName }}</td></tr>
        <tr><td class="k">Pickup Address:</td><td>{{ $pickupAddress }}</td></tr>
    </table>

    <div class="section-title">[ 📍 ROUTE &amp; DELIVERY ADDRESS ]</div>
    <hr class="dashed">
    <table class="kv">
        <tr><td class="k">Delivery Destination:</td><td>{{ $deliveryAddress }}</td></tr>
        <tr><td class="k">Authorized Receiver:</td><td>{{ $receiverName }}</td></tr>
        <tr><td class="k">Authorized Receiver ID Type/No:</td><td>{{ $receiverIdType }} / {{ $receiverIdNumber }}</td></tr>
    </table>

    <div class="section-title">[ 📦 OFFICIAL CARGO &amp; ORDER DETAILS ]</div>
    <hr class="dashed">
    <table class="kv">
        <tr><td class="k">Sourcing &amp; Operations:</td><td>SIOSHIPPING GLOBAL LOGISTICS SERVICE LTD (Nigeria)</td></tr>
        <tr><td class="k">Description of Goods:</td><td>{{ $cargoDescription }}</td></tr>
        <tr><td class="k">Total Weight (KG):</td><td>{{ $cargoWeight }} kg</td></tr>
        <tr><td class="k">Total Pieces/Packages:</td><td>{{ $cargoPieces }} units</td></tr>
    </table>

    <div class="section-title">[ ASSIGNED TRANSPORT UNIT ]</div>
    <hr class="dashed">
    <table class="kv">
        <tr><td class="k">Authorized Driver:</td><td>{{ $driverName }}</td></tr>
        <tr><td class="k">Vehicle Plate No.:</td><td>{{ $vehiclePlate }}</td></tr>
    </table>

    <hr class="dashed">
    <div class="section-title">✍️ CLIENT SIGNATURE COPY (Authorization for Pickup)</div>
    <div class="sign-box">
        <p>
            By signing, I certify that all driver, vehicle, delivery address, and authorized receiver
            information shown above matches my app records and is CORRECT.
        </p>
        <p>Client Digital Signature Token: {{ $signatureToken }}</p>
    </div>

    <p class="footer">
        Issued by SIOPAY &amp; SIOSHIPPING Global Operations Legal Department.
    </p>
</body>
</html>
