<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Paypal - Arab Apps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            height: 100vh;
            justify-content: center;
            align-items: center;
            display: flex;
            flex-direction: column;
            background-color: #eee
        }

        .launch {
            height: 50px
        }

        .close {
            font-size: 21px;
            cursor: pointer;
            float: right;
        }

        .modal-body {
            height: 450px
        }

        .nav-tabs {
            border: none !important
        }

        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #ffffff #ffffff #fff;
            border-top: 3px solid blue !important
        }

        .nav-tabs .nav-link {
            margin-bottom: -1px;
            border: 1px solid transparent;
            border-top-left-radius: 0rem;
            border-top-right-radius: 0rem;
            border-top: 3px solid #eee;
            font-size: 20px
        }

        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #ffffff #ffffff
        }

        .nav-tabs {
            display: table !important;
            width: 100%
        }

        .nav-item {
            display: table-cell
        }

        .form-control {
            border-bottom: 1px solid #eee !important;
            border: none;
            font-weight: 600
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #8bbafe;
            outline: 0;
            box-shadow: none
        }

        .inputbox {
            position: relative;
            margin-bottom: 20px;
            width: 100%
        }

        .inputbox span {
            position: absolute;
            top: 7px;
            left: 11px;
            transition: 0.5s
        }

        .inputbox i {
            position: absolute;
            top: 13px;
            right: 8px;
            transition: 0.5s;
            color: #3F51B5
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0
        }

        .inputbox input:focus~span {
            transform: translateX(-0px) translateY(-15px);
            font-size: 12px
        }

        .inputbox input:valid~span {
            transform: translateX(-0px) translateY(-15px);
            font-size: 12px
        }

        .pay button {
            height: 47px;
            border-radius: 37px
        }
    </style>
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}&components=buttons"></script>
</head>

<body>
    <button type="button" class="btn btn-primary launch" data-toggle="modal" data-target="#staticBackdrop"> <i
            class="fa fa-rocket"></i> Pay Now
    </button>
    @if (Session::has('error') || request()->has('error'))
        <div class="alert alert-danger mt-3">{{ Session::get('error') ?? request()->get('error') }}</div>
        {{ Session::forget('error') }}
    @endif
    @if (Session::has('success'))
        <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
        {{ Session::forget('success') }}
    @endif
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-right"> <i class="fa fa-close close" data-dismiss="modal"></i> </div>
                    <div class="tabs mt-3">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation"> <a class="nav-link active"
                                    id="paypal-redirect-tab" data-toggle="tab" href="#paypal-redirect" role="tab"
                                    aria-controls="paypal-redirect" aria-selected="true"> <img
                                        src="https://i.imgur.com/yK7EDD1.png" width="80">
                                </a> </li>
                            <li class="nav-item" role="presentation"> <a class="nav-link" id="paypal-tab"
                                    data-toggle="tab" href="#paypal" role="tab" aria-controls="paypal"
                                    aria-selected="false"> <img src="https://i.imgur.com/yK7EDD1.png" width="80">
                                </a> </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="paypal-redirect" role="tabpanel"
                                aria-labelledby="paypal-redirect-tab">
                                <div id="paypal-button-container" class="my-5"></div>

                            </div>
                            <div class="tab-pane fade" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
                                <div class="px-5 mt-5">
                                    <div class="pay px-5">
                                        <form action="{{ route('processTransactionRedirect') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="price" value="50.00">
                                            <input type="hidden" name="product_name" value="Laptop">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary">Checkout with
                                                PayPal</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>





</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

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

                window.location.href = "{{ route('success') }}";

                // Replace the above to show a success message within this page, e.g.
                // const element = document.getElementById('paypal-button-container');
                // element.innerHTML = '';
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
            });
        },
        onCancel: function(data) {
            window.location.href = "{{ route('createTransaction') }}?error=Transaction Canceled.";
        }

    }).render('#paypal-button-container');
</script>

</html>


{{-- <script src="https://www.paypal.com/sdk/js?client-id=test&currency=USD"></script> --}}
