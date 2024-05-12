<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}&components=buttons"></script>
</head>

<body>

    <div class="container text-center ">
        <div class="my-4">
            <form action="{{ route('processTransactionRedirect') }}" method="post">
                @csrf
                <input type="hidden" name="price" value="50.00">
                <input type="hidden" name="product_name" value="Laptop">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary">Checkout with PayPal</button>
            </form>
            @if (Session::has('error') || request()->has('error'))
                <div class="alert alert-danger mt-3">{{ Session::get('error') ?? request()->get('error') }}</div>
                {{ Session::forget('error') }}
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
                {{ Session::forget('success') }}
            @endif
        </div>

        {{-- Paypal Button --}}
        <div id="paypal-button-container"></div>
    </div>



</body>

<script>
    // Render the PayPal button into #paypal-button-container
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    paypal.Buttons({
        style: {
            layout: 'horizontal'
        },
        // Call your server to set up the transaction
        createOrder: function(data, actions) {

            return fetch('processTransactionButton', {
                method: 'post',
                headers: {
                    'content-type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    value: document.querySelector('input[name=price]').value
                })


            }).then(function(res) {

                return res.json();
            }).then(function(orderData) {
                return orderData.id;
            });
        },

        // Call your server to finalize the transaction
        onApprove: function(data, actions) {
            return fetch('successTransactionButton', {
                method: 'post',
                headers: {
                    'content-type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    orderId: data.orderID,
                })
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                // Three cases to handle:
                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                //   (2) Other non-recoverable errors -> Show a failure message
                //   (3) Successful transaction -> Show confirmation or thank you

                // This example reads a v2/checkout/orders capture response, propagated from the server
                // You could use a different API or structure for your 'orderData'
                var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart(); // Recoverable state, per:
                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                }

                if (errorDetail) {
                    var msg = 'Sorry, your transaction could not be processed.';
                    if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                    if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                    {{-- sessionStorage.setItem("error", msg); --}}

                    return window.location.href = "{{ route('createTransaction') }}?error=" +
                        errorMessage;

                }

                // Successful capture! For demo purposes:


                // Replace the above to show a success message within this page, e.g.
                // const element = document.getElementById('paypal-button-container');
                // element.innerHTML = '';
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
            });
        },
        onCancel: function(data) {
            window.location.href = "{{ route('createTransaction') }}?error=Payment canceled by user";
        }

    }).render('#paypal-button-container');
</script>

</html>


{{-- <script src="https://www.paypal.com/sdk/js?client-id=test&currency=USD"></script> --}}
