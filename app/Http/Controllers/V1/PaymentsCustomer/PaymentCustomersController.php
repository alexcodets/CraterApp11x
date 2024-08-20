<?php

namespace Crater\Http\Controllers\V1\PaymentsCustomer;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCustomersController extends Controller
{
    public function index(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentCustomersController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $usercurrent = User::where("id", Auth::id())->first();

        $limit = $request->has('limit') ? $request->limit : 10;

        $payments = Payment::with(['user', 'invoice', 'paymentMethod', 'creator'])
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->applyFilters($request->only([
                'transaction_status',
                'search',
                'payment_number',
                'payment_id',
                'payment_method_id',
                'customer_id',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($usercurrent->company_id)
            ->where('payments.user_id', Auth::id())
            ->select('payments.*', 'users.name', 'invoices.invoice_number', 'payment_methods.name as payment_mode')
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'payments' => $payments,
            'paymentTotalCount' => Payment::count(),
        ], "message" => "index Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "index Payments");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("PaymentsCustomer", "List", "customer/payments", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'payments' => $payments,
            'paymentTotalCount' => Payment::count(),
        ]);
    }

    public function show(Request $request, $id)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentCustomersController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $payment = Payment::with([
            'user',
            'invoice',
            'paymentMethod',
            'fields.customField',
        ]) ->where('payments.id', $id)
         ->first();


        ////registro de authorize
        $authorize = Payment::join('authorize', 'payments.authorize_id', 'authorize.id')->select('authorize.*')->where('payments.id', $payment['id'])->first();

        $dateNow = Carbon::now();
        $transaccionDate = $payment->created_at;
        $transaccionDate->addHours(48);

        if ($transaccionDate < $dateNow) {
            $payment['isRefunded'] = true;
        } elseif ($transaccionDate > $dateNow) {
            $payment['isVoid'] = true;
        }
        $invoice_id = null;
        if($payment->invoice_id != null) {
            $inv = Invoice::where("id", $payment->invoice_id)->whereNULL("deleted_at")->first();
            if($inv != null) {
                $invoice_id = $inv;
            }

        }


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),
            'payment' => $payment,
            'authorize' => $authorize,
            'invoice_id' => $invoice_id,
        ], "message" => "show Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "show Payments");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payments", "View", "admin/payments/id/view", $payment->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment: ".$payment->payment_number);

        return response()->json([
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),
            'payment' => $payment,
            'authorize' => $authorize,
            'invoice_id' => $invoice_id,
        ]);
    }

    public function store(PaymentRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentsCustomersController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        // $authorize = $request->authorize;
        // return $authorize;
        $usercurrent = User::where("id", Auth::id())->first();

        Log::debug('Before Payment creation inside PaymentCustomersController(Store)');
        $payment = Payment::createPayment($request);
        Log::debug($payment);
        \Log::debug("Payment {$payment->id} created from PaymentCustomersController(Store): with payment_number: {$payment->payment_number}");

        ///resta del balance cuando el pago se hace con el credito del cliente
        ////actualizar billing direction
        if ($request->get('updatebillinginformation') != null) {
            if ($request->get('updatebillinginformation') == 1) {
                //user_id
                $direccion = Address::where("user_id", $payment->user_id)->where("type", "billing")->first();
                if(is_null($direccion)) {
                    $direccion = new Address();
                    $direccion->user_id = $payment->user_id;
                    $direccion->type = "billing";
                }
                if ($request->authorize != null) {

                    $direccion->name = $request->authorize["name"];
                    $direccion->address_street_1 = $request->authorize["address_street_1"];
                    $direccion->address_street_2 = $request->authorize["address_street_2"];
                    $direccion->city = $request->authorize["city"];
                    $direccion->state_id = $request->authorize["state_id"];
                    $direccion->country_id = $request->authorize["country_id"];
                    $direccion->zip = $request->authorize["zip"];
                    $direccion->phone = $request->authorize["phone"];
                    $direccion->save();
                }
            } //

        } //
        ////crear cuenta
        if ($request->get('createaccount') != null) {
            if ($request->get('createaccount') == 1) {
                //user_id
                $paymentaccount = null;
                if ($request->authorize != null) {
                    if($request->card_number != null && $request->cvv != null) {
                        $sql = PaymentAccount::where('client_id', '=', $payment->user_id)
                            ->where('payment_account_type', '=', 'CC')
                            ->get();
                        foreach ($sql as $s) {
                            if (! is_null($s->card_number) && $request->card_number == Crypt::decryptString($s->card_number)) {
                                $paymentaccount = $s;

                                break;
                            }
                        }
                    }
                    if($request->account_number != null && $request->routing_number != null) {
                        $sql = PaymentAccount::where('client_id', '=', $payment->user_id)
                                        ->where('payment_account_type', '=', 'ACH')
                                        ->get();
                        foreach($sql as $s) {
                            if(! is_null($s->account_number) && $request->account_number == Crypt::decryptString($s->account_number)) {
                                $paymentaccount = $s;

                                break;
                            }
                        }
                    }
                    if(! isset($paymentaccount)) {
                        $paymentaccount = new PaymentAccount();
                    }

                    $paymentaccount->first_name = $request->authorize["name"];
                    $paymentaccount->country_id = $request->authorize["country_id"];
                    $paymentaccount->state_id = $request->authorize["state_id"];
                    $paymentaccount->city = $request->authorize["city"];
                    $paymentaccount->address_1 = $request->authorize["address_street_1"];
                    $paymentaccount->address_2 = $request->authorize["address_street_2"];
                    $paymentaccount->zip = $request->authorize["zip"];
                    $paymentaccount->payment_account_type = "CC";
                    $paymentaccount->card_number = isset($request->authorize["card_number"]) && ! is_null($request->authorize["card_number"]) ?
                                                    Crypt::encryptString($request->authorize["card_number"]) :
                                                    null;
                    $paymentaccount->credit_card = $request->credit_card;
                    $paymentaccount->cvv = isset($request->authorize["cvv"]) && ! is_null($request->authorize["cvv"]) ?
                                                Crypt::encryptString($request->authorize["cvv"]) :
                                                null;
                    $paymentaccount->expiration_date = isset($request->authorize["date"]) && ! is_null($request->authorize["date"]) ?
                                                        Crypt::encryptString($request->authorize["date"]) :
                                                        null;
                    $paymentaccount->status = "A";
                    $paymentaccount->client_id = $request->user_id;
                    $paymentaccount->company_id = $payment->company_id;
                    //$paymentaccount->save();

                } //if

                if ($request->account_number != null && $request->routing_number != null) {
                    $direccion = Address::where("user_id", $payment->user_id)->where("type", "billing")->first();
                    if(is_null($paymentaccount)) {
                        $paymentaccount = new PaymentAccount();
                    }

                    $paymentaccount->status = "A";
                    $paymentaccount->client_id = $request->user_id;
                    $paymentaccount->company_id = $payment->company_id;
                    $paymentaccount->payment_account_type = "ACH";
                    $paymentaccount->account_number = ! is_null($request->account_number) ? Crypt::encryptString($request->account_number) : null;
                    $paymentaccount->routing_number = ! is_null($request->routing_number) ? Crypt::encryptString($request->routing_number) : null;
                    $paymentaccount->ACH_type = ! is_null($request->routing_number) ? Crypt::encryptString($request->ACH_type) : null;
                    if ($request->account_number != null) {

                        $paymentaccount->bank_name = ! is_null($request->bank_name) ? Crypt::encryptString($request->bank_name) : null;
                    }

                    if(is_null($direccion) || ! isset($direccion->name) || (isset($direccion->name) && $direccion->name == null)) {
                        $customer = User::where("id", $payment->user_id)->first();
                        $paymentaccount->first_name = $customer->contact_name;
                    } else {
                        $paymentaccount->first_name = $direccion->name;
                    }

                    if(! is_null($direccion)) {
                        $paymentaccount->country_id = $direccion->country_id;
                        $paymentaccount->state_id = $direccion->state_id;
                        $paymentaccount->city = $direccion->city;
                        $paymentaccount->address_1 = $direccion->address_street_1;
                        $paymentaccount->address_2 = $direccion->address_street_2;
                        $paymentaccount->zip = $direccion->zip;
                    }
                    $paymentaccount->country_id = isset($request->authorize["country_id"]) ?
                                                    $request->authorize["country_id"] : $paymentaccount->country_id;
                    $paymentaccount->state_id = isset($request->authorize["state_id"]) ?
                                                    $request->authorize["state_id"] : $paymentaccount->state_id;
                    $paymentaccount->city = isset($request->authorize["city"]) ?
                                                $request->authorize["city"] : $paymentaccount->city;
                    $paymentaccount->address_1 = isset($request->authorize["address_street_1"]) ?
                                                    $request->authorize["address_street_1"] : $paymentaccount->address_1;
                    $paymentaccount->address_2 = isset($request->authorize["address_street_2"]) ?
                                                    $request->authorize["address_street_2"] : $paymentaccount->address_2;
                    $paymentaccount->zip = isset($request->authorize["zip"]) ? $request->authorize["zip"] : $paymentaccount->zip;
                    //$paymentaccount->save();
                }

                if(! is_null($paymentaccount)) {
                    $paymentaccount->save();
                }
            } //

        } //

        if ($request->customer_credit == true && $request->invoice_id != null) {
            if ($request->user_id != null) {
                $userobj = User::where("id", $request->user_id)->first();

                if ($userobj != null) {
                    $actual = $userobj->balance;
                    $monto = $request->amount / 100;
                    $userobj->balance = $actual - $monto;
                    $userobj->save();
                    $balance = new BalanceCustomer();
                    $balance->status = "D";
                    $balance->present_balance = $actual;
                    $balance->amount_op = $monto;
                    $balance->amount_final = $userobj->balance;
                    $balance->payment_id = $payment->id;
                    $balance->user_id = $userobj->id;

                    $balance->save();

                }
            }

        }

        ///resta del balance cuando el pago se hace con el credito del cliente

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'payment' => $payment,
            'success' => true,
        ], "message" => " store Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "  store Payments");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payments", "Create", "admin/payments/create", $payment->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment: ".$payment->payment_number);

        return response()->json([
            'payment' => $payment,
            'success' => true,
        ]);
    }
}
