<?php

namespace Crater\Http\Controllers\V1\Payment;

use Auth;
use Carbon\Carbon;
use Crater\Authorize\AuthorizeController;
use Crater\Authorize\Models\Authorize;
use Crater\CorePos\Models\PosMoney;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeletePaymentsRequest;
use Crater\Http\Requests\PaymentRequest;
use Crater\Models\Address;
use Crater\Models\AuxVault;
use Crater\Models\BalanceCustomer;
use Crater\Models\CompanySetting;
use Crater\Models\Expense;
use Crater\Models\ExpenseCategory;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentMethod;
use Crater\Models\PaymentsPaypal;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Log::debug('Request payment controller', ['Request' => $request]);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentsController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // Utilizamos la inyección de dependencias para obtener los parámetros de la solicitud.
        $limit = $request->input('limit', 10);
        $expenses = $request->input('expenses', 'NO');
        $customerid = $request->input('customer_id', 'NO');

        // Preparamos la consulta principal para los pagos.
        $paymentsQuery = Payment::with(['user', 'invoice', 'paymentMethod', 'creator'])
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->applyFilters($request->only([
                'search',
                'payment_number',
                'invoice_number',
                'payment_id',
                'payment_method_id',
                'customer_id',
                'customcode',
                'transaction_status',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->select('payments.payment_number as paynumber', 'payments.*', 'users.name', 'invoices.invoice_number', 'payment_methods.name as payment_mode')
            ->latest();

        // Aplicamos la condición para los gastos si es necesario.
        if (strtolower($expenses) === 'yes') {
            $vector = Expense::whereNotNull('payment_id')->pluck('payment_id');
            $paymentsQuery->whereNotIn('payments.id', $vector);
        }

        // Paginamos los resultados.
        $payments = $paymentsQuery->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'payments' => $payments,
            'paymentTotalCount' => Payment::count(),
        ], "message" => "index Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "index Payments");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payments", "List", "admin/payments", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'payments' => $payments,
            'paymentTotalCount' => Payment::count(),
        ]);
    }

    public function indexAll(Request $request)
    {
        $payments = Payment::with(['user', 'invoice', 'paymentMethod', 'creator'])
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->applyFilters($request->only([
                'search',
                'payment_number',
                'payment_id',
                'payment_method_id',
                'customer_id',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->select('payments.payment_number as paynumber', 'payments.*', 'users.name', 'invoices.invoice_number', 'payment_methods.name as payment_mode')
            ->latest()
            ->paginateData($limit);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        // $authorize = $request->authorize;
        // return $authorize;
        \Log::debug("request");
        \Log::debug($request->input());

        $payment = Payment::createPayment($request);
        \Log::debug("payment");
        \Log::debug($payment);

        \Log::debug(gettype($payment));

        // Suponiendo que $objeto puede ser un array o un objeto de tipo Payment
        if (is_array($payment) && array_key_exists('error', $payment)) {
            // Si es un array y contiene la clave 'error', retornamos un JSON con success = false y el error específico
            return response()->json([
                'success' => false,
                'error' => $payment['error'],
            ], 422); // Código de estado HTTP para "Entidad no procesable"
        } elseif (is_array($payment) && ! array_key_exists('error', $payment)) {
            // Si es un array pero no contiene la clave 'error', retornamos un mensaje de error genérico
            return response()->json([
                'success' => false,
                'error' => 'An error occurred',
            ], 422);
        }
        ///resta del balance cuando el pago se hace con el credito del cliente
        ////actualizar billing direction
        if ($request->get('updatebillinginformation') != null) {
            if ($request->get('updatebillinginformation') == 1) {
                //user_id
                $direccion = Address::where("user_id", $payment->user_id)->where("type", "billing")->first();
                if (is_null($direccion)) {
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
                    if ($request->authorize["card_number"] != null && $request->authorize["cvv"]) {
                        $sql = PaymentAccount::where('client_id', '=', $payment->user_id)
                            ->where('payment_account_type', '=', 'CC')
                            ->get();
                        foreach ($sql as $s) {
                            if (! is_null($s->card_number) && $request->authorize["card_number"] == Crypt::decryptString($s->card_number)) {
                                $paymentaccount = $s;

                                break;
                            }
                        }
                    }
                    if ($request->account_number != null && $request->routing_number != null) {
                        $sql = PaymentAccount::where('client_id', '=', $payment->user_id)
                            ->where('payment_account_type', '=', 'ACH')
                            ->get();
                        foreach ($sql as $s) {
                            if (! is_null($s->account_number) && $request->account_number == Crypt::decryptString($s->account_number)) {
                                $paymentaccount = $s;

                                break;
                            }
                        }
                    }
                    if (! isset($paymentaccount)) {
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
                    $paymentaccount->credit_card = "";

                    if (isset($request->authorize["credit_cards"])) {
                        $paymentaccount->credit_card = $request->authorize["credit_cards"];
                    }
                    $paymentaccount->cvv = isset($request->authorize["cvv"]) && ! is_null($request->authorize["cvv"]) ?
                    Crypt::encryptString($request->authorize["cvv"]) : null;
                    $paymentaccount->expiration_date = isset($request->authorize["date"]) && ! is_null($request->authorize["date"]) ?
                    Crypt::encryptString($request->authorize["date"]) : null;
                    $paymentaccount->status = "A";
                    $paymentaccount->client_id = $request->user_id;
                    $paymentaccount->company_id = $payment->company_id;
                    //$paymentaccount->save();

                } //if

                if ($request->account_number != null && $request->routing_number != null) {
                    $direccion = Address::where("user_id", $payment->user_id)->where("type", "billing")->first();
                    if (is_null($paymentaccount)) {
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

                    if (is_null($direccion) || ! isset($direccion->name) || (isset($direccion->name) && $direccion->name == null)) {
                        $customer = User::where("id", $payment->user_id)->first();
                        $paymentaccount->first_name = $customer->contact_name;
                    } else {
                        $paymentaccount->first_name = $direccion->name;
                    }

                    if (! is_null($direccion)) {
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

                if (! is_null($paymentaccount)) {
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

        ///generate el expense
        if ($payment != null) {
            \Log::debug($payment);

            $paymentmethod = PaymentMethod::where("id", $payment->payment_method_id)->first();
            //Log::debug($paymentmethod);
            if ($paymentmethod != null) {
                if ($paymentmethod->generate_expense == 1 || $paymentmethod->generate_expense == true) {
                    $this->createExpense($payment, $paymentmethod->expCatGen);
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

        try {
            //Log::debug('Payment controller 319');
            $payment->paymentSuccess($payment);
        } catch (\Throwable $th) {
            //Log::debug('Payment controller catch');
            //Log::debug($th);
            //throw $th;
        }

        // Logs por modulo
        LogsModule::createLog("Payments", "Create", "admin/payments/create", $payment->id, Auth::user()->name ?? null, Auth::user()->email ?? null, Auth::user()->role ?? null, Auth::user()->company_id ?? null, "Payment: ".$payment->payment_number);

        // Customer Balance
        $customer = User::where("id", $payment->user_id)->first();

        return response()->json([
            'payment' => $payment,
            'success' => true,
            'customer' => $customer,
        ]);
    }

    public function show(Request $request, Payment $payment)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Payment' => $payment]);
        $log = LogsDev::initLog($request, "", "D", "PaymentsController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $payment->load([
            'user',
            'invoice',
            'paymentMethod',
            'fields.customField',
        ]);

        Log::debug('payment show', ['payment' => $payment]);
        ////registro de authorize
        $authorize = Payment::join('authorize', 'payments.authorize_id', 'authorize.id')->select('authorize.*')->where('payments.id', $payment['id'])->first();
        $auxVault = AuxVault::where('id', $payment['aux_vault_id'])->first();
        $authorize2 = Authorize::where("id", $payment['authorize_id'])->first();
        $dateNow = Carbon::now();
        $transaccionDate = $payment->created_at;
        $transaccionDate->addHours(48);
        $invoice_id = null;

        if ($transaccionDate < $dateNow) {
            $payment['isRefunded'] = true;
        } elseif ($transaccionDate > $dateNow) {
            $payment['isVoid'] = true;
        }

        if ($payment->invoice_id != null) {
            $inv = Invoice::where("id", $payment->invoice_id)->whereNULL("deleted_at")->first();
            if ($inv != null) {
                $invoice_id = $inv;
            }

        }

        $expenselist = Expense::where("payment_id", $payment->id)->get()->toarray();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),

            'payment' => $payment,
            'authorize' => $authorize,

            'aux_vault' => $auxVault,
            'invoice_id' => $invoice_id,
            'expenselist' => $expenselist,
        ], "message" => "show Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "show Payments");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payments", "View", "admin/payments/id/view", $payment->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment: ".$payment->payment_number);

        return response()->json([
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),
            'payment_gateway_name' => $payment->getPaymentGatewayAttribute(),
            'payment' => $payment,
            'authorize' => $authorize,
            'aux_vault' => $auxVault,
            'invoice_id' => $invoice_id,
            'expenselist' => $expenselist,
            'authorize2' => $authorize2,
        ]);
    }

    public function update_payment_status(Request $request)
    {
        $payment = Payment::where("id", $request["id"])->first();
        $payment->transaction_status = $request["transaction_status"];
        $payment->notes = $request["notes"];
        $payment->applied_credit_customer = $request["applied_credit_customer"];
        $payment->save();

        return $payment;
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        \Log::debug("update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Payment' => $payment]);
        $log = LogsDev::initLog($request2, "", "D", "PaymentsController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $old_status = $payment->transaction_status;
        $payment = $this->update_payment_status($request);

        if ($payment->transaction_status != "Approved") {
            $pay = Payment::where("id", $payment->id)->first();
            $pay->transaction_status = $payment->transaction_status;
            $pay->save();

            //// Pagos CON/O sin factura
            // Payment con factura
            if ($payment->invoice_id != null) {
                $invoice = Invoice::where("id", $payment->invoice_id)->first();
                //Log( $invoice );
                if ($invoice != null) {
                    if ($invoice->total == $payment->amount) {
                        if ($payment->transaction_status != "Pending") {
                            $invoice->due_amount = $invoice->total;
                            $invoice->status = "SENT";
                            $invoice->paid_status = "UNPAID";
                            $invoice->save();
                        }
                    } else {
                        if ($invoice->total > $payment->amount) {
                            $invoice->due_amount = $invoice->due_amount + $payment->amount;
                            $invoice->status = "SENT";

                            if ($invoice->due_amount > $invoice->total) {
                                $invoice->due_amount = $invoice->total;
                            }

                            if ($invoice->due_amount == $invoice->total) {
                                $invoice->paid_status = "UNPAID";
                            } else {
                                $invoice->paid_status = "PARTIALLY_PAID";
                            }
                            $invoice->save();
                        }
                    }

                    // devolver el dinero al usuario (ya sea que pago con algun metodo de pago o de su balance)
                    if ($payment->transaction_status != "Pending") {
                        // pago con metodo de pago
                        if ($request["payment_method"] != null) {
                            if ($payment->transaction_status != "Void" && $payment->transaction_status != "Refunded") {
                                if ($payment->transaction_status == "Unapply" &&
                                    $payment->applied_credit_customer == true) {
                                    $customer = User::where("id", $payment->user_id)->first();
                                    if ($customer != null) {
                                        //Registrar transaccion  en "balance_customer"
                                        $balance = new BalanceCustomer();
                                        $balance->status = "A";
                                        $balance->present_balance = $customer->balance;
                                        $balance->amount_op = $payment->amount / 100;
                                        $balance->amount_final = $customer->balance + ($payment->amount / 100);
                                        $balance->payment_id = $payment->id;
                                        $balance->user_id = $customer->id;
                                        $balance->save();
                                        //Agregar balance al customer
                                        $customer->balance = $customer->balance + ($payment->amount / 100);
                                        $customer->save();

                                    }
                                }
                            }
                        }
                        // pago con balance (customer balance)
                        else {
                            $customer = User::where("id", $payment->user_id)->first();
                            if ($customer != null) {
                                //Registrar transaccion  en "balance_customer"
                                $balance = new BalanceCustomer();
                                $balance->status = "A";
                                $balance->present_balance = $customer->balance;
                                $balance->amount_op = $payment->amount / 100;
                                $balance->amount_final = $customer->balance + ($payment->amount / 100);
                                $balance->payment_id = $payment->id;
                                $balance->user_id = $customer->id;
                                $balance->save();
                                //Agregar balance al customer
                                $customer->balance = $customer->balance + ($payment->amount / 100);
                                $customer->save();
                            }
                        }
                    }

                } else {
                    // validaciones de customer
                    $customer = User::where("id", $payment->user_id)->first();
                    $customer_balance = BalanceCustomer::where("user_id", $payment->user_id)->first();
                    $expense = Expense::where("payment_id", $payment->id)->first();

                    if ($customer != null) {
                        if ($customer->balance > $payment->amount) {
                            $customer->balance = $customer->balance - ($payment->amount / 100);
                            $customer->save();
                            $customer_balance->present_balance = $customer->balance - ($payment->amount / 100);
                            $customer_balance->save();

                            if ($expense != null) {
                                $expense->deleted_at = Carbon::now();
                                $expense->save();
                            }
                        }
                    }
                }
                //return;
            }
            // Payment sin factura (recarga o descuento de saldo/balance)
            else {
                if ($payment->transaction_status != "Pending") {
                    // validaciones de customer
                    $customer = User::where("id", $payment->user_id)->first();
                    $customer_balance = BalanceCustomer::where("user_id", $payment->user_id)->first();
                    //$expense = Expense::where("payment_id",    $payment->id)->first();
                    if ($customer != null) {
                        // condiciones (if)
                        $CUSTOMER_BALANCE = $customer->balance;
                        $PAYMENT_AMOUNT = $payment->amount / 100;
                        //
                        $balance = new BalanceCustomer();
                        $balance->status = "D";
                        $balance->present_balance = $customer->balance;

                        if ($CUSTOMER_BALANCE >= $PAYMENT_AMOUNT) {
                            $balance->amount_op = $payment->amount / 100;
                            $balance->amount_final = $customer->balance - ($payment->amount / 100);
                            $customer->balance = $customer->balance - ($payment->amount / 100);
                        }

                        if ($PAYMENT_AMOUNT > $CUSTOMER_BALANCE) {
                            $expense_amount = ($payment->amount / 100) - $customer->balance;
                            $balance->amount_op = $payment->amount / 100;
                            $balance->amount_final = 0;

                            if ($expense_amount > 0) {
                                $numberexpense = "";
                                $numberexpense = $this->getNextExpenseNumber($payment);
                                $pop = CompanySetting::where("company_id", $payment->company_id)->where("option", "expense_prefix")->first();
                                $expense_category = ExpenseCategory::where("for_payments", 1)->first();

                                $expense = new Expense();
                                $expense->expense_number = $pop->value."-".$numberexpense;
                                $expense->subject =
                                $payment->payment_number.' - '.$old_status.' - '.$payment->payment_date
                                .' - '.$payment->transaction_status;
                                $expense->expense_date = Carbon::now()->format('Y-m-d');
                                $expense->amount = $expense_amount * 100;
                                $expense->user_id = $payment->user_id;
                                $expense->notes = $payment->notes;
                                $expense->creator_id = $payment->creator_id;
                                $expense->company_id = $payment->company_id;
                                $expense->payment_id = $payment->id;
                                $expense->payment_date = $payment->payment_date;
                                $expense->payment_method_id = $payment->payment_method_id;
                                $expense->expense_category_id = $expense_category->id;
                                $expense->save();
                            }
                            $customer->balance = 0;
                        }

                        $balance->payment_id = $payment->id;
                        $balance->user_id = $customer->id;
                        // guardando la transaccion de descontar saldo, balance_customer
                        $balance->save();
                        // guardando el balance que le queda al customer en su cuenta
                        $customer->save();

                        /*
                    if ($customer->balance > $payment->amount)
                    {
                    $customer->balance = $customer->balance - ($payment->amount /100);
                    $customer->save();

                    $customer_balance->present_balance = $customer->balance - ($payment->amount /100);
                    $customer_balance->save();

                    if ($expense != null)
                    {
                    $expense->deleted_at = Carbon::now();
                    $expense->save();

                    }
                    }*/
                    }
                }

            }
            ////

            // Generacion de expense, con el ExpenseCategorie asociado al payment_method
            if ($payment->transaction_status == "Void" || $payment->transaction_status == "Refunded") {
                if ($request["payment_method"] != null) {
                    if ($request["payment_method"]["generate_expense"] == 1) {
                        $expense_category = ExpenseCategory::find($request["payment_method"]["generate_expense_id"]);
                        if ($expense_category) {
                            $numberexpense = "";
                            $numberexpense = $this->getNextExpenseNumber($payment);
                            $pop = CompanySetting::where("company_id", $payment->company_id)
                                ->where("option", "expense_prefix")
                                ->first();
                            $expense = new Expense();
                            $expense->expense_number = $pop->value."-".$numberexpense;
                            $expense->subject = $payment->payment_number;
                            $expense->expense_date = Carbon::now()->format('Y-m-d');
                            $expense->amount = $payment->amount;
                            $expense->user_id = $payment->user_id;
                            $expense->notes = $payment->notes;
                            $expense->creator_id = $payment->creator_id;
                            $expense->company_id = $payment->company_id;
                            $expense->payment_id = $payment->id;
                            $expense->payment_date = $payment->payment_date;
                            $expense->payment_method_id = $payment->payment_method_id;
                            $expense->expense_category_id = $expense_category->id;
                            $expense->save();
                        }
                    }
                }
            }
        }

        if ($payment->status_with_authorize == true) {
            //Manejo del estado de la factura
            try {
                if ($payment->transaction_status == "Void") {
                    AuthorizeController::void($request);
                } elseif ($payment->transaction_status == "Refunded") {
                    AuthorizeController::refunded($request);
                }
            } catch (Exception $e) {
                return response()->json([
                    'payment' => $e,
                    'success' => false,
                ]);
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'payment' => $payment,
            'success' => true,
        ], "message" => "update Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "updatePayments");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payments", "Update", "admin/payments/id/edit", $payment->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment: ".$payment->payment_number);

        return response()->json([
            'payment' => $payment,
            'success' => true,
            "req" => $payment->transaction_status,
        ]);
    }

    public function delete(DeletePaymentsRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentsController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        foreach ($request->ids as $id) {
            $payment = Payment::find($id);
            // Logs por modulo
            LogsModule::createLog("Payments", "delete", "payments/delete", $payment->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment: ".$payment->payment_number);

        }

        Payment::deletePayments($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "delete Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "delete Payments");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    public function paypalProcess(Request $request)
    {
        $gateway = new Braintree\Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config('services.braintree.merchantId'),
            'publicKey' => config('services.braintree.publicKey'),
            'privateKey' => config('services.braintree.privateKey'),
        ]);

        $token = $gateway->ClientToken()->generate();

        return $token;
    }

    public function savePaymentPaypal(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentsPaypalController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //// Obtener el registro del usuario logueado
        $user_id = Auth::user()->id;
        $company_id = Auth::user()->company_id;

        // save payment
        $payment_paypal = PaymentsPaypal::create($request->except('user_id', 'company_id') + ['user_id' => $user_id, 'company_id' => $company_id]);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_paypal' => $payment_paypal,
        ], "message" => "Paypal payments store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal payments store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json($payment_paypal, 201);
    }

    public function createExpense(Payment $payment, $expenseCategory = null)
    {
        //Log::debug("Entro a");

        // $type = ExpenseCategory::where("for_payments", 1)->first();
        if ($expenseCategory != null) {
            //   //Log::debug("entro a b ");

            $numberexpense = "";
            $numberexpense = $this->getNextExpenseNumber($payment);
            //Log::debug($numberexpense);

            $pop = CompanySetting::where("company_id", $payment->company_id)->where("option", "expense_prefix")->first();

            $expense = new Expense();
            $expense->expense_number = $pop->value."-".$numberexpense;
            $expense->expense_date = $payment->payment_date;

            $expense->amount = $payment->amount;
            $expense->user_id = $payment->user_id;
            $expense->notes = $payment->notes;
            $expense->creator_id = $payment->creator_id;
            $expense->company_id = $payment->company_id;
            $expense->payment_id = $payment->id;
            $expense->payment_method_id = $payment->payment_method_id;
            $expense->expense_category_id = $expenseCategory->id;

            $expense->save();

        }
    }

    public function getNextExpenseNumber($payment)
    {
        // Get the last created order
        $pop = CompanySetting::where("company_id", $payment->company_id)->where("option", "expense_prefix")->first();
        //Log::debug($pop);
        $value = $pop->value;
        $expense = Expense::where('expense_number', 'LIKE', $value.'-%')
            ->orderBy('created_at', 'desc')
            ->first();
        if (! $expense) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $expense->expense_number);
            $number = $number[1];
        }
        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf('%06d', intval($number) + 1);
    }

    public function addMultiplePayment(PaymentRequest $request)
    {
        $payment = Payment::createMultiplePayment($request);

        return response()->json([
            'payment' => $payment,
            'success' => true,
        ]);
    }

    public function showMultiplePayment(Request $request, $payment_id)
    {
        $payment = Payment::find($payment_id);

        $payment->load([
            'user',
            'invoice',
            'paymentMethod',
            'fields.customField',
            'paymentMethods',
        ]);

        return response()->json([
            "payment" => $payment,
            'nextPaymentNumber' => $payment->getPaymentNumAttribute(),
            'payment_prefix' => $payment->getPaymentPrefixAttribute(),
            "success" => true,
        ]);
    }

    public function getPaymentMethods(Request $request)
    {
        // Company Currency
        $currency_option = CompanySetting::where('option', 'currency')
            ->whereCompany($request->header('company'))
            ->first();

        // Payment methods available in the module
        $payment_methods = PaymentMethod::select('payment_methods.*', 'pos_payment_methods.payment_method_id')
            ->join('pos_payment_methods', 'payment_methods.id', '=', 'pos_payment_methods.payment_method_id')
            ->get();

        foreach ($payment_methods as $pm) {
            $moneys_id = \DB::table('pos_money_payment_methods')->where('payment_method_id', $pm->id)->pluck('pos_money_id');

            if ($moneys_id->isNotEmpty()) {
                $moneys = PosMoney::whereIn('id', $moneys_id)
                    ->where("currency_id", $currency_option->value)
                    ->orderBy('amount', 'ASC')
                    ->get();
                $pm->pos_money = $moneys;
            } else {
                $pm->pos_money = [];
            }
        }

        return response()->json([
            "payment_methods" => $payment_methods,
            "success" => true,
        ]);
    }
}
