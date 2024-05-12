<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    private $provider;
    public function __construct()
    {
        $this->provider = $this->initializePayPalClient();

    }

    /**
     * create transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function createTransaction()
    {
        return response(view('transaction'));
    }


    /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function processTransactionPaypalRedirect(Request $request)
    {
        $order = $this->createPaypalOrder($request);

        if ($order['status'] == 'CREATED') {
            foreach ($order['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    return redirect()->away($link['href']);
                }
            }

        } else {
            return redirect()
                ->route('cancelTransaction');
        }
    }
    public function processTransactionPaypalButton(Request $request)
    {
        $order = $this->createPaypalOrder($request);
        return response()->json($order);
    }


    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function successTransactionButton(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $orderId = $data['orderId'];

        $result = $this->capturePaypalPayment($orderId);
        return response()->json($result);

    }
    public function successTransactionRedirect(Request $request)
    {

        $orderId = $request->token;

        $result = $this->capturePaypalPayment($orderId);
        return redirect()
            ->route('success');
    }
    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelTransaction(Request $request)
    {
        return redirect()
            ->route('createTransaction')
            ->with('error', 'Transaction Canceled.');
    }




    private function initializePayPalClient()
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        return $provider;
    }

    private function createPaypalOrder($request)
    {
        $price = $request->price ?? 20;
        $data = [
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('successTransactionRedirect'),
                "cancel_url" => route('cancelTransaction'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $price
                    ]
                ]
            ]
        ];
        $order = $this->provider->createOrder($data);
        return $order;
    }

    private function capturePaypalPayment($token)
    {
        $response = $this->provider->capturePaymentOrder($token);
        try {
            DB::beginTransaction();

            if ($response['status'] == 'COMPLETED') {
                $payment = new Payment;
                $payment->payment_id = $response['id'];
                $payment->amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                $payment->currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
                $payment->payer_name = $response['payer']['name']['given_name'];
                $payment->payer_email = $response['payer']['email_address'];
                $payment->payment_status = $response['status'];
                $payment->payment_method = "PayPal";
                $payment->save();
                DB::commit();

                return $response;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }


    }
}
