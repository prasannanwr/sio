<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Product Inquiry - {{env('APP_NAME')}}</title>
</head>
<body>
    <h1>New Product Inquiry</h1>
    <p>You have received a new inquiry for the product: <strong>{{ $product->name }}</strong>.</p>
    <p>Here are the customer details:</p>
    <ul>
        <li><strong>Customer Email:</strong> {{ $inquiryData['email'] }}</li>
        <li><strong>Customer Desired Shipping Location/Port:</strong> {{ $inquiryData['shipping_location'] }}</li>
        <li><strong>Customer Organization Name:</strong> {{ $inquiryData['organization'] }}</li>
    </ul>
    <h5>User Note</h5>
    <p>{{$inquiryData['note']}}</p>
</body>
</html>
