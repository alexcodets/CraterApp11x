<?php

namespace Crater\Authorize\Controllers;

use Auth;
use Braintree\Gateway;
use Carbon\Carbon;
use Crater\Authorize\Models\Authorize;
use Crater\Authorize\Models\AuthorizeSetting;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Crater\Models\Payment;
use Crater\Models\PaymentDevolution;
use Crater\Models\PaypalSetting;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class AuthorizeController extends Controller
{
    protected $paymentAletoryNumber;

    protected $transactionId;

    public function __construct()
    {
        $this->paymentAletoryNumber = rand(1000, 9999);
        $this->transactionId = rand(100000000, 999999999);
    }

    public function charge(Request $request)
    {
        $response = self::chargeMetho($request);

        return $response;
    }

    ////  Metodo que hace peticion de cobro de tarjeta de credito
    public static function chargeMetho($request)
    {
        $paymentAletoryNumber = rand(1000, 9999);
        $transactionId = rand(100000000, 999999999);
        $typeclient = "business";
        $customer = User::where("id", $request['user_id'])->first();
        $name = $request['name'];
        $companyname = $request['company_name'];
        if ($customer != null) {

            if ($customer->customer_type == 'R') {
                $typeclient = "individual";
            }

            $name = $customer->name;
            $companyname = $customer->contact_name;
        }
        $currencycode = "USD";
        $codecurency = CompanySetting::where("option", "currency")->where("company_id", 1)->first();
        if ($codecurency != null) {
            $money = Currency::where("id", $codecurency->value)->first();
            if ($money != null) {
                $currencycode = $money->code;
            }

        }

        $authorize_setting = AuthorizeSetting::where('deleted_at', null)->where('status', "A")->first();

        if ($authorize_setting["test_mode"] == true) {
            $url = 'https://apitest.authorize.net/xml/v1/request.api';
        } else {
            $url = 'https://api.authorize.net/xml/v1/request.api';
        }

        $curl = curl_init();

        $amount_total = $request['amount'] / 100;

        $expiration_date = $request['date'];
        //\Log::debug(  $expiration_date);
        if (strlen($expiration_date) > 7) {

            $expiration_date = substr($expiration_date, 0, 7);

        }
        //\Log::debug(  $expiration_date);
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "createTransactionRequest": {
                "merchantAuthentication": {
                    "name": "'.$authorize_setting["login_id"].'",
                    "transactionKey": "'.$authorize_setting["transaction_key"].'"
                },
                "refId": "'.$transactionId.'",
                "transactionRequest": {
                    "transactionType": "authCaptureTransaction",
                    "amount": "'.$amount_total.'",

                    "payment": {
                        "creditCard": {
                            "cardNumber": "'.$request['card_number'].'",
                            "expirationDate": "'.$expiration_date.'",
                            "cardCode": "'.$request['cvv'].'"
                        }
                    },
                    "order": {
                        "invoiceNumber": "'.$request['invoice_number'].'",
                        "description": "'.$request['description'].'"
                    },
                    "poNumber": "'.$request['payment_number'].'-'.$paymentAletoryNumber.'",
                    "customer": {
                        "id":  "'.$request['customcode'].'",
                        "email": "'.$request['email'].'"
                    },
                    "billTo": {
                        "firstName": "'.$name.'",
                        "lastName": "'.$request['last_name'].'",
                        "company": "'.$companyname.'",
                        "address": "'.$request['address_street_1'].'",
                        "city": "'.$request['city'].'",
                        "state": "'.$request['state'].'",
                        "zip": "'.$request['zip'].'",
                        "country": "'.$request['country'].'",
                        "phoneNumber": "'.$request['phone'].'",
                        "email": "'.$request['email'].'"
                    },
                    "transactionSettings": {
                        "setting": {
                            "settingName": "duplicateWindow",
                            "settingValue": "0"
                        }
                    }
                }
            }
        }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;

    }

    public function paypalCheckout(Request $request)
    {
        $paypalSettings = PaypalSetting::select('*')->where('status', 'A');
        $paypalMode = '';
        if ($paypalSettings->test_mode == 1) {
            $paypalMode = 'sandbox';
        } else {
            $paypalMode = 'production';
        }
        $gateway = new Gateway([
            'environment' => config($paypalMode),
            'merchantId' => config($paypalSettings->paypal_id),
            'publicKey' => config($paypalSettings->paypal_signature),
            'privateKey' => config($paypalSettings->paypal_secret),
        ]);

        $amount = $request->input('amount') / 100;
        $nonce = $request->payment_method_nonce;

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'customer' => [
                'firstName' => $request['first_name'],
                'lastName' => $request['last_name'],
            ],
            'options' => [
                'submitForSettlement' => true,
            ],
        ]);

        if ($result->success) {
            $transaction = $result->transaction;
            $creditCard = $transaction->creditCard;

            $cc_number = substr($request['card_number'], -4);

            $authorize = new Authorize();
            $authorize->expiration_date = Crypt::encryptString($creditCard->expirationMonth.'/'.$creditCard->expirationYear);
            $authorize->transaction_id = $request['transId'];
            $authorize->payer_email = $request['payer_email'];
            $authorize->amount = $amount;
            $authorize->currency = 'Paypal';
            $authorize->payment_status = 'Captured';
            $authorize->card_number = null;
            $authorize->credit_card = $creditCard->bin;
            $authorize->name = $request['first_name'];
            $authorize->address_street_1 = $request['address_street_1'];
            $authorize->address_street_2 = $request['address_street_2'];
            $authorize->city = $request['city'];
            $authorize->state = $request['state'];
            $authorize->state_id = $request['state_id'];
            $authorize->country = $request['country'];
            $authorize->country_id = $request['country_id'];
            $authorize->zip = $request['zip'];
            $authorize->phone = $request['phone'];
            $authorize->creator_id = Auth::user()->id;
            $authorize->company_id = Auth::user()->company_id;
            $authorize->save();

            return response()->json([
                'authorize' => $authorize,
            ]);

            return $transaction->id;
        } else {
            $errorString = "";

            foreach ($result->errors->deepAll() as $error) {
                $errorString .= 'Error: '.$error->code.": ".$error->message."\n";
            }

            return $errorString;
        }
    }

    public function saveCharge(Request $request)
    {
        //dd($request->all());
        $cc_number = substr($request['card_number'], -4);

        $authorize = new Authorize();
        $authorize->expiration_date = Crypt::encryptString($request->date);
        $authorize->transaction_id = $request['transId'];
        $authorize->payer_email = $request['payer_email'];
        $authorize->amount = $request['amount'];
        $authorize->currency = 'USD';
        $authorize->payment_status = 'Captured';
        $authorize->card_number = $cc_number;
        $authorize->credit_card = $request['credit_cards'];
        $authorize->credit_card_full = Crypt::encryptString($request['card_number']);
        $authorize->name = $request['name'];
        $authorize->address_street_1 = $request['address_street_1'];
        $authorize->address_street_2 = $request['address_street_2'];
        $authorize->city = $request['city'];
        $authorize->state = $request['state'];
        $authorize->state_id = $request['state_id'];
        $authorize->country = $request['country'];
        $authorize->country_id = $request['country_id'];
        $authorize->zip = $request['zip'];
        $authorize->phone = $request['phone'];
        $authorize->creator_id = Auth::user() ? Auth::user()->id : $request['user_id'];
        $authorize->company_id = Auth::user() ? Auth::user()->company_id : $request['company_id'];
        $authorize->save();

        return response()->json([
            'authorize' => $authorize,
        ]);

    }

    public function insert_payment_devolution($request)
    {

        if ($request["invoice_id"]) {
            //Log::debug($request);
            $paymentDevolutions = new PaymentDevolution();
            $paymentDevolutions->invoice_id = $request["invoice_id"];
            $paymentDevolutions->payment_method = $request["payment_method"]["name"];
            $paymentDevolutions->transaction_id = $request["id"];
            $paymentDevolutions->date = Carbon::now();
            $paymentDevolutions->amount = $request["amount"];
            $paymentDevolutions->payload = (string) (json_encode($request));
            $paymentDevolutions->status = $request["transaction_status"];
            $paymentDevolutions->updated_at = Carbon::now();
            $paymentDevolutions->created_at = Carbon::now();
            $paymentDevolutions->save();

            return ["id" => $request["invoice_id"]];

        }

    }

    public function update_payment_status(Request $request)
    {

        $payment = Payment::where("id", $request["id"])->first();
        $payment->transaction_status = $request["transaction_status"];
        $payment->save();

    }

    public function void(Request $request)
    {
        \Log::debug("void");
        \Log::debug($request);
        if ($request->has('transaction_status') && $request["transaction_status"] === 'Void' && $request["status_with_authorize"] === true) {

            //$user_id = Auth::id();
            $user_id = Auth::user()->id;

            //$authorize_setting = AuthorizeSetting::where('creator_id', $user_id)->where('deleted_at', null)->where('status', 'A')->first();
            $authorize_setting = AuthorizeSetting::where('deleted_at', null)->where('status', "A")->first();
            //Log::debug(   $authorize_setting);
            $authorize = Payment::join('authorize', 'payments.authorize_id', 'authorize.id')->select('authorize.transaction_id')->where('payments.id', $request->id)->first();

            if ($authorize_setting["test_mode"] == true) {

                $url = 'https://apitest.authorize.net/xml/v1/request.api';

            } else {

                $url = 'https://api.authorize.net/xml/v1/request.api';

            }

            //Log::debug( $authorize);
            //Log::debug( $request->input());
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "createTransactionRequest": {
                    "merchantAuthentication": {
                        "name": "'.$authorize_setting["login_id"].'",
                        "transactionKey": "'.$authorize_setting["transaction_key"].'"
                    },
                    "refId": "'.$this->transactionId.'",
                    "transactionRequest": {
                        "transactionType": "voidTransaction",
                        "refTransId": "'.$authorize['transaction_id'].'"
                    }
                }
            }',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);

            $responseJson = json_encode($response);
            $error = strpos($responseJson, 'error');
            if ($error !== false) {
                $this->update_payment_status($request);
                $this->insert_payment_devolution($request);
            }

            return $response;

        }

    }

    public function refunded(Request $request)
    {
        if ($request->has('transaction_status') && $request["transaction_status"] === 'Refunded' && $request["status_with_authorize"] === true) {
            if ($request["payment_method"]["account_accepted"] == 'C') {
                $user_id = Auth::user()->id;
                //$authorize_setting = AuthorizeSetting::where('creator_id', $user_id)->where('status', 'A')->where('deleted_at', null)->first();
                $authorize_setting = AuthorizeSetting::where('deleted_at', null)->where('status', "A")->first();

                $authorize = Payment::join('authorize', 'payments.authorize_id', 'authorize.id')
                    ->select('authorize.expiration_date', 'authorize.card_number', 'authorize.credit_card_full')
                    ->where('payments.id', $request->id)
                    ->first();

                $expiration_date = Crypt::decryptString($authorize->expiration_date);
                $credit_card_full = '';
                if ($authorize->credit_card_full) {
                    $credit_card_full = Crypt::decryptString($authorize->credit_card_full);
                } else {
                    // return error message if credit card no is not found
                    return response()->json([
                        'error' => 'Credit card no not found',
                    ], 404);
                }

                if ($authorize_setting["test_mode"] == true) {

                    $url = 'https://apitest.authorize.net/xml/v1/request.api';

                } else {

                    $url = 'https://api.authorize.net/xml/v1/request.api';

                }

                $curl = curl_init();

                $amount_total = $request->amount / 100;

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                    "createTransactionRequest": {
                        "merchantAuthentication": {
                            "name": "'.$authorize_setting["login_id"].'",
                            "transactionKey": "'.$authorize_setting["transaction_key"].'"
                        },
                        "refId": "'.$this->transactionId.'",
                        "transactionRequest": {
                            "transactionType": "refundTransaction",
                            "amount": "'.($request["amount"] / 100).'",
                            "payment": {
                                "creditCard": {
                                    "cardNumber": "'.$credit_card_full.'",
                                    "expirationDate": "'.$expiration_date.'"
                                }
                            },
                            "refTransId": "'.$authorize["transaction_id"].'"
                        }
                    }
                }',
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);

                curl_close($curl);

                $responseJson = json_encode($response);
                $error = strpos($responseJson, 'error');

                if ($error !== false) {
                    $this->update_payment_status($request);
                    $this->insert_payment_devolution($request);
                }

                return $response;
            }

            if ($request["payment_method"]["account_accepted"] == 'A') {
                $authorize_setting = AuthorizeSetting::where('deleted_at', null)->where('status', "A")->first();

                $authorize = Payment::join('authorize', 'payments.authorize_id', 'authorize.id')
                    ->select(
                        'authorize.ACH_TYPE',
                        'authorize.account_number',
                        'authorize.routing_number',
                        'authorize.name_on_account'
                    )
                    ->where('payments.id', $request->id)
                    ->first();

                $accountType = '';
                $routingNumber = '';
                $accountNumber = '';
                $nameOnAccount = '';

                if ($authorize) {
                    $accountType = $authorize->ACH_TYPE;
                    $routingNumber = $authorize->routing_number;
                    $accountNumber = $authorize->account_number;
                    $nameOnAccount = $authorize->name_on_account;
                } else {
                    // return error message if ACH account not found
                    return response()->json([
                        'error' => 'ACH account not found',
                    ], 404);
                }

                if ($authorize_setting["test_mode"] == true) {

                    $url = 'https://apitest.authorize.net/xml/v1/request.api';

                } else {

                    $url = 'https://api.authorize.net/xml/v1/request.api';

                }

                $curl = curl_init();
                $amount_total = $request->amount / 100;

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                    "createTransactionRequest": {
                        "merchantAuthentication": {
                            "name": "'.$authorize_setting["login_id"].'",
                            "transactionKey": "'.$authorize_setting["transaction_key"].'"
                        },
                        "refId": "'.$this->transactionId.'",
                        "transactionRequest": {
                            "transactionType": "refundTransaction",
                            "amount": "'.($request["amount"] / 100).'",
                            "payment": {
                                "bankAccount": {
                                    "accountType": "'.$accountType.'",
                                    "routingNumber": "'.$routingNumber.'",
                                    "accountNumber": "'.$accountNumber.'",
                                    "nameOnAccount": "'.$nameOnAccount.'",
                                }
                            },
                            "order": {
                                "invoiceNumber": "'.$request['invoice']['invoice_number'].'",
                                "description": "'.'Refunded Payment'.'",
                            },
                            "poNumber": "'.$request['payment_number'].'",
                            "customer": {
                                "id":  "'.$request['user']['customcode'].'",
                                "email": "'.$request['user']['email'].'"
                            }
                        }
                    }
                }',
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);

                curl_close($curl);

                $responseJson = json_encode($response);

                $error = strpos($responseJson, 'error');

                if ($error !== false) {
                    $this->insert_payment_devolution($request);
                    $this->update_payment_status($request);
                }

                return $response;
            }
        }
    }

    /// ach metodo que ejecuta peticion de ach
    public function ach(Request $request)
    {
        $response = self::achMetho($request);

        ////procesar el response

        return $response;
    }

    public static function achMetho($request)
    {

        $paymentAletoryNumber = rand(1000, 9999);
        $transactionId = rand(100000000, 999999999);
        $typeclient = "business";
        $customer = User::where("id", $request['user_id'])->first();
        $name = $request['name'];
        $companyname = $request['company_name'];
        $ultraname = $name;
        if ($customer != null) {

            if ($customer->customer_type == 'R') {
                $typeclient = "individual";
            }

            $name = $customer->name;
            $ultraname = substr($name, 0, 21); //parche
            $companyname = $customer->contact_name;
        }
        $currencycode = "USD";
        $codecurency = CompanySetting::where("option", "currency")->where("company_id", 1)->first();
        if ($codecurency != null) {
            $money = Currency::where("id", $codecurency->value)->first();
            if ($money != null) {
                $currencycode = $money->code;
            }

        }

        $authorize_setting = AuthorizeSetting::where('deleted_at', null)->where('status', "A")->first();
        Log::debug($request);

        if ($authorize_setting["test_mode"] == true) {
            $url = 'https://apitest.authorize.net/xml/v1/request.api';
        } else {
            $url = 'https://api.authorize.net/xml/v1/request.api';
        }

        $amount_total = $request["amount"] / 100;

        $payment_data_number = explode("-", $request["payment_number"]);
        $auxachtype = "checking";
        // Verificar si el índice ACH_type existe dentro de request
        if (isset($request["ACH_type"])) {
            // Verificar si el índice value existe dentro del array interno
            if (isset($request["ACH_type"]["value"])) {
                // Guardar el contenido de value en una variable auxiliar
                $auxachtype = $request["ACH_type"]["value"];
                // Hacer lo que quieras con la variable aux
                // Por ejemplo, imprimir su valor

            }
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "createTransactionRequest": {
                "merchantAuthentication": {
                    "name": "'.$authorize_setting["login_id"].'",
                    "transactionKey": "'.$authorize_setting["transaction_key"].'"
                },
                "refId": "'.$transactionId.'",
                "transactionRequest": {
                    "transactionType": "authCaptureTransaction",
                    "amount": "'.$amount_total.'",
                    "payment": {
                        "bankAccount": {
                            "accountType": "'.strtolower($auxachtype).'",
                            "routingNumber": "'.$request['routing_number'].'",
                            "accountNumber": "'.$request['account_number'].'",
                            "nameOnAccount": "'.$ultraname.'",
                        }
                    },
                    "order": {
                        "invoiceNumber": "'.$request['invoice_number'].'",
                        "description": "'.$request['description'].'",
                    },
                    "poNumber": "'.$request['payment_number'].'-'.$paymentAletoryNumber.'",
                    "customer": {
                        "id":  "'.$request['customcode'].'",
                        "email": "'.$request['email'].'"
                    },
                    "billTo": {
                        "firstName": "'.$name.'",
                        "lastName": "'.$request['last_name'].'",
                        "company": "'.$companyname.'",
                        "address": "'.$request['address_street_1'].'",
                        "city": "'.$request['city'].'",
                        "state": "'.$request['state'].'",
                        "zip": "'.$request['zip'].'",
                        "country": "'.$request['country'].'",
                        "email": "'.$request['email'].'"
                    },
                    "transactionSettings": {
                        "setting": {
                            "settingName": "duplicateWindow",
                            "settingValue": "0"
                        }
                    }
                }
            }
        }',
            CURLOPT_HTTPHEADER => [
                // 'auth_id: 818cb4c5-5e10-4ae9-abaf-2b5f9436dbfd',
                // 'auth_token: 9bdc7fad-c5be-44b1-9d42-01d945ad2073',
                'content-type: application/json',
            ],
        ]);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);

        curl_close($curl);

        return $response;

    }

    public function saveChargeACH(Request $request)
    {

        $authorize = new Authorize();
        $authorize->expiration_date = Crypt::encryptString($request->date);
        $authorize->transaction_id = $request['transId'];
        $authorize->payer_email = $request['payer_email'];
        $authorize->amount = $request['amount'];
        $authorize->currency = 'USD';
        $authorize->payment_status = 'Captured';
        //$authorize->card_number = $cc_number;
        $authorize->name = $request['name'];
        $authorize->address_street_1 = $request['address_street_1'];
        $authorize->address_street_2 = $request['address_street_2'];
        $authorize->city = $request['city'];
        $authorize->state = $request['state'];
        $authorize->state_id = $request['state_id'];
        $authorize->country = $request['country'];
        $authorize->country_id = $request['country_id'];
        $authorize->zip = $request['zip'];
        $authorize->phone = $request['phone'];
        $authorize->phone = $request['phone'];

        $auxachtype = "checking";
        // Verificar si el índice ACH_type existe dentro de request
        if (isset($request["ACH_type"])) {
            // Verificar si el índice value existe dentro del array interno
            if (isset($request["ACH_type"]["value"])) {
                // Guardar el contenido de value en una variable auxiliar
                $auxachtype = $request["ACH_type"]["value"];
                // Hacer lo que quieras con la variable aux
                // Por ejemplo, imprimir su valor

            }
        }

        $authorize->ACH_type = strtolower($auxachtype);
        $authorize->account_number = $request['account_number'];
        $authorize->bank_name = $request['bank_name'];
        $authorize->name_on_account = $request['name'];
        $authorize->num_check = $request['num_check'];
        $authorize->routing_number = $request['routing_number'];
        $authorize->creator_id = Auth::user()->id;
        $authorize->company_id = Auth::user()->company_id;
        $authorize->save();

        return response()->json([
            'authorize' => $authorize,
        ]);

    }
}
