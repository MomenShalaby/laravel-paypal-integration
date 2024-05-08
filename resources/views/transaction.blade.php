<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paypal - Arab Apps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #014c63;
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}"></script>
</head>

<body>
    <div class="container text-center">
        <form action="{{ route('processTransaction') }}" method="post">
            @csrf
            <input type="hidden" name="price" value="20">
            <input type="hidden" name="product_name" value="Laptop">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-primary">Checkout with PayPal</button>
        </form>
        @if (\Session::has('error'))
            <div class="alert alert-danger mt-3">{{ \Session::get('error') }}</div>
            {{ \Session::forget('error') }}
        @endif
        @if (\Session::has('success'))
            <div class="alert alert-success mt-3">{{ \Session::get('success') }}</div>
            {{ \Session::forget('success') }}
        @endif
    </div>
</body>

</html>
