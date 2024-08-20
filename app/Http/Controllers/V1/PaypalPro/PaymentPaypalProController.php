<?php

namespace Crater\Http\Controllers\V1\PaypalPro;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\FailedPaymentHistory;
use Crater\Models\PaymentsPaypal;
use Crater\Models\PaypalSetting;
use Illuminate\Http\Request;
use Omnipay\Common\CreditCard;



use Omnipay\Omnipay;

class PaymentPaypalProController extends Controller
{
    public function payment(Request $request)
    {
        $response = self::chargeMetho($request);

        return $response;

    }

    public static function chargeMetho($request)
    {
        $paypal_setting = PaypalSetting::where('deleted_at', null)->first();

        // Create a gateway for the PayPal RestGateway
        // (routes to GatewayFactory::create)
        $gateway = Omnipay::create('PayPal_Rest');

        // Initialise the gateway
        $gateway->initialize([
            'clientId' => $paypal_setting->public_key,
            'secret' => $paypal_setting->private_key,
            'testMode' => $paypal_setting->enviroment == 'sandbox', // Or false when you are ready for live transactions
        ]);


        $date_expire = explode("-", $request['date']);
        // Create a credit card object
        // DO NOT USE THESE CARD VALUES -- substitute your own
        // see the documentation in the class header.
        $card = new CreditCard([
                    'firstName' => $request['first_name'],
                    'lastName' => $request['last_name'],
                    'number' => $request['card_number'],
                    'expiryMonth' => $date_expire[1],
                    'expiryYear' => $date_expire[0],
                    'cvv' => $request['cvv'],
                    'billingAddress1' => $request['address_street_1'],
                    'billingCountry' => $request['country'],
                    'billingCity' => $request['city'],
                    'billingPostcode' => $request['zip'],
                    'billingState' => $request['state'],
                    'transactionId' => $request['payment_number'],
                    'clientIp' => $request['customcode'],
                    'email' => $request['payer_email'],
                    'phone' => $request['phone'],
        ]);

        // Do a purchase transaction on the gateway
        $transaction = $gateway->purchase([
            'amount' => $request['amount'] / 100,
            'currency' => 'USD',
            'card' => $card,
        ])->send();

        if ($transaction->isSuccessful()) {

            $paymentData = $transaction->getData();

            // ultimos 4 digitos de la tarjeta
            $last4CardNumber = substr($request['card_number'], -4);

            $id_transaction = $transaction->getTransactionReference();
            $dataSavePaypal = [
                'transaction_id' => $id_transaction,
                'email_address' => $request['payer_email'],
                'amount' => $request['amount'] / 100,
                'currency' => 'USD',
                'country_code' => $paymentData['payer']['funding_instruments'][0]['credit_card']['billing_address']['country_code'],
                'payment_status' => $paymentData['state'],
                'card_number' => $last4CardNumber,
                // 'card_type' => $request['card_type'],
                'create_time' => $paymentData['create_time'],
                'creator_id' => Auth::user()->id ?? null,
                'company_id' => $request["company_id"],

            ];
            $payment_paypal = PaymentsPaypal::create($dataSavePaypal);

            // Payment was successful
            return [
                "success" => true,
                'payment_id' => $transaction->getTransactionReference(),
                'data' => $payment_paypal,
                'paymentData' => $paymentData,
            ];
        } else {
            // $dataFail= [
            // 'payment_gateway' => 'Paypal',
            // 'transaction_number' => $request['payment_number'],
            // 'date' => Carbon::now(),
            // 'amount' => $request['amount'] / 100,
            // 'payment_number' => $request['payment_number'],
            // 'customer_id' => '1111',
            // 'invoice_id' => '1111',
            // 'description' => $transaction->getMessage(),
            // ];

            // FailedPaymentHistory::createFailedPaymentHistory($dataFail);

            return [
                "success" => false,
                'message' => $transaction->getMessage(),
                'data' => $transaction->getData(),
            ];
        }

    }
}
