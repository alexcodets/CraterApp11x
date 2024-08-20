<?php

namespace Crater\Http\Controllers\V1\Payment;

use Auth;
// settings
use Crater\Authorize\Models\AuthorizeSetting as ModelsAuthorizeSetting;
use Crater\Http\Controllers\Controller;
//
use Crater\Http\Requests\PaymentMethodRequest;
use Crater\Models\AuxVaultSetting;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentMethod;
use Crater\Models\PaypalSetting;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        //  $time = microtime(true);
        //$log = LogsDev::initLog($request, "", "D", "PaymentMethodsController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        \Log::debug("elevar sus seres");

        $limit = $request->input('limit', 5);
        $isCustomer = Auth::check() && Auth::user()->role === 'customer';
        \Log::debug(Auth::user()->role);
        \Log::debug($isCustomer);
        // Aplicar filtros y obtener métodos de pago
        $paymentMethodsQuery = PaymentMethod::whereCompany($request->header('company'))
            ->applyFilters($request->only(['method_id', 'search', 'orderByField', 'orderBy']))
            ->whereIsMultiple(0)
            ->latest();

        // Aplicar condición adicional si el usuario es un cliente
        if ($isCustomer) {
            $paymentMethodsQuery->where('for_customer_use', '<>', 0);
        }

        $paymentMethods = $paymentMethodsQuery->paginateData($limit);

        // payment methods with settings (Payment Gateways -> authorize_settings and paypal_settings)
        $exist_authorize_setting = ModelsAuthorizeSetting::where('is_default', 1)->count();
        $exist_paypal_setting = PaypalSetting::where('status', 'A')->count();
        $exists_aux_vault_settings = AuxVaultSetting::where('default', 1)->count();
        $paymentMethodsWithSettings =
        $this->paymentMethodsWithSettings($request, $exist_authorize_setting, $exist_paypal_setting, $exists_aux_vault_settings);
        //


        return response()->json([
            'paymentMethods' => $paymentMethods,
            'paymentMethodsWithSettings' => $paymentMethodsWithSettings,
            'exist_authorize_setting' => $exist_authorize_setting,
            'exist_paypal_setting' => $exist_paypal_setting,
            'exist_aux_vault_setting' => $exists_aux_vault_settings,
        ]);
    }

    public function paymentMethodsWithSettings($request, $exist_authorize_setting, $exist_paypal_setting, $exists_aux_vault_settings)
    {
        $payment_methods = PaymentMethod::whereCompany($request->header('company'))
            ->whereIsMultiple(0)
            ->when($exists_aux_vault_settings > 0 || $exist_authorize_setting > 0, function ($query) {
                // Si $exists_aux_vault_settings o $exist_authorize_setting son mayores a cero, se traen todos los registros
                return $query;
            })
            ->when($exists_aux_vault_settings == 0 && $exist_authorize_setting == 0 && $exist_paypal_setting > 0, function ($query) {
                // Si $exists_aux_vault_settings y $exist_authorize_setting son iguales a cero y $exist_paypal_setting mayor a cero,
                // los registros se deben filtrar por 'account_accepted' != 'A'
                return $query->where('account_accepted', '!=', 'A');
            })
            ->when($exists_aux_vault_settings == 0 && $exist_authorize_setting == 0 && $exist_paypal_setting == 0, function ($query) {
                // Si $exists_aux_vault_settings, $exist_authorize_setting y $exist_paypal_setting son iguales a cero,
                // los registros se deben filtrar por 'add_payment_gateway' = 0 y 'paypal_button' = 0
                return $query->where('add_payment_gateway', 0)->where('paypal_button', 0);
            })
            ->latest()
            ->get();

        return $payment_methods;
    }

    public function getPaymentModesCorePosMoney(Request $request)
    {
        $paymentMethods = PaymentMethod::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'method_id',
                'search',
            ]))
            ->whereIsMultiple(0)
            ->get();

        return response()->json([
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentMethodsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $paymentMethod = PaymentMethod::createPaymentMethod($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'paymentMethod' => $paymentMethod,
        ], "message" => "store paymentMethods"];
        LogsDev::finishLog($log, $res, $time, 'D', "store paymentMethods");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payment Modes", "Create", "admin/settings/payment-mode", $paymentMethod->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment Modes: ".$paymentMethod->name);

        return response()->json([
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, PaymentMethod $paymentMethod)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['PaymentMethod' => $paymentMethod]);
        $log = LogsDev::initLog($request, "", "D", "PaymentMethodsController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'paymentMethod' => $paymentMethod,
        ], "message" => "show paymentMethods"];
        LogsDev::finishLog($log, $res, $time, 'D', "show paymentMethods");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payment Modes", "View", "admin/settings/payment-mode", $paymentMethod->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment Modes: ".$paymentMethod->name);

        return response()->json([
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['PaymentMethod' => $paymentMethod]);
        $log = LogsDev::initLog($request2, "", "D", "PaymentMethodsController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        \Log::debug($request->input());
        $paymentMethod->update($request->validated());
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'paymentMethod' => $paymentMethod,
        ], "message" => "update paymentMethods"];
        LogsDev::finishLog($log, $res, $time, 'D', "update paymentMethods");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payment Modes", "Update", "admin/settings/payment-mode", $paymentMethod->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment Modes: ".$paymentMethod->name);

        return response()->json([
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PaymentMethod $paymentMethod)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['PaymentMethod' => $paymentMethod]);
        $log = LogsDev::initLog($request, "", "D", "PaymentMethodsController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $payments = $paymentMethod->payments;

        if ($payments->count() > 0) {
            return response()->json([
                'error' => 'payments_attached',
            ]);
        }

        // Logs por modulo
        LogsModule::createLog("Payment Modes", "delete", "admin/settings/payment-mode", $paymentMethod->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment Modes: ".$paymentMethod->name);

        $paymentMethod->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => 'Payment method deleted successfully',
        ], "message" => "destroy paymentMethods"];
        LogsDev::finishLog($log, $res, $time, 'D', "destroy paymentMethods");
        /////////////////////////////////////////

        return response()->json([
            'success' => 'Payment method deleted successfully',
        ]);
    }

    public function paymentModesWithoutLogin(Request $request)
    {

        $limit = $request->has('limit') ? $request->limit : 5;

        $paymentMethods = PaymentMethod::where('for_customer_use', '<>', 0)
            ->latest()
            ->get();

        return response()->json([
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function paymentMethodActive()
    {

        $paymentsWithoutGateway = [];
        $paymentsWithGatewayCreditCard = [];
        $paymentsACH = [];

        $isCustomer = Auth::check() && Auth::user()->role === 'customer';

        $companyId = auth()->user()->company->id;
        $methods = PaymentMethod::where('company_id', $companyId)->whereNull('deleted_at');

        $paymentsWithoutGateway = PaymentMethod::where('company_id', $companyId)->whereNull('deleted_at')->where('add_payment_gateway', 0)->where('for_customer_use', 1)->where('status', 'A')->get();

        $paymentGateways = PaymentGateways::where('company_id', $companyId)->where('status', 'A')->whereNull('deleted_at')->get();

        if ($paymentGateways) {

            $paymentsWithGatewayCreditCard = PaymentMethod::where('company_id', $companyId)->whereNull('deleted_at')->where('add_payment_gateway', 1)->where('for_customer_use', 1)->where('account_accepted', 'C')->where('status', 'A')->get();
        }

        $authorizeActive = false;

        foreach ($paymentGateways as $gateway) {
            if ($gateway['name'] == 'Authorize' && $gateway['status']) {
                $authorizeActive = true;
            }
        }

        if ($authorizeActive) {
            $paymentsACH = PaymentMethod::where('company_id', $companyId)->whereNull('deleted_at')->where('account_accepted', 'A')->where('status', 'A')->where('for_customer_use', 1)->get();
        }

        $nonEmptyArrays = [];

        if (! empty($paymentsWithoutGateway)) {
            $nonEmptyArrays[] = $paymentsWithoutGateway->toArray();
        }
        if (! empty($paymentsWithGatewayCreditCard)) {
            $nonEmptyArrays[] = $paymentsWithGatewayCreditCard->toArray();
        }
        if (! empty($paymentsACH)) {
            $nonEmptyArrays[] = $paymentsACH->toArray();
        }

        $mergedArray = call_user_func_array('array_merge', $nonEmptyArrays);

        return response()->json(['payment_methods' => $mergedArray]);

    }
}
