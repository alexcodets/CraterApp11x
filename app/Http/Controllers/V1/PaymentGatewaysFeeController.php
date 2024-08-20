<?php

namespace Crater\Http\Controllers\V1;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PaymentGatewaysFee;
use Crater\Models\PaymentPaymentGatewaysFee;
use Illuminate\Http\Request;

class PaymentGatewaysFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $time = microtime(true);

        // Definir el límite de paginación con un valor predeterminado de 10 si no se proporciona
        $limit = $request->input('limit', 10);

        // Aplicar filtros solo si es necesario
        $filters = $request->only([
            'name',
            'payment_gateway',
            'authorize_setting_id',
            'aux_vault_setting_id',
            'paypal_settings_id',
            'orderByField',
            'orderBy',
        ]);

        \Log::debug($filters);
        $PaymentGatewaysFee = PaymentGatewaysFee::whereCompany($request->header('company'))
            ->when(count($filters) > 0, function ($query) use ($filters) {
                return $query->applyFilters($filters);
            })
            ->wherenull("deleted_at")
            ->latest()
            ->paginateData($limit);

        // Preparar la respuesta
        $response = [
            'success' => true,
            'response' => ['PaymentGatewaysFee' => $PaymentGatewaysFee],
            'message' => 'PaymentGatewaysFeeController',
            'totalPaymentFees' => PaymentGatewaysFee::wherenull("deleted_at")->count(),
        ];

        // Registro de log
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewaysFeeController", "index");
        LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

        // Logs por modulo
        LogsModule::createLog("Payment gateways fees", "List", "admin/settings/gateway/fees", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        // Retornar la respuesta JSON
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Iniciar el registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewaysFeeController", "store");

        try {
            // Validar y obtener solo los datos seguros
            \Log::debug($request->input());
            $data = $request->input();
            $data['company_id'] = $request->header('company');

            // Crear el registro en la base de datos
            \Log::debug($data);
            $PaymentGatewaysFee = PaymentGatewaysFee::create($data);

            // Registrar la acción en el módulo correspondiente
            LogsModule::createLog("Payment gateways fees", "Create", "admin/settings/gateway/fees", $PaymentGatewaysFee->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

            // Preparar la respuesta de éxito
            $response = [
                "success" => true,
                "response" => ['PaymentGatewaysFee' => $PaymentGatewaysFee],
                "message" => "PaymentGatewaysFee creado con éxito.",
            ];
        } catch (\Exception $e) {
            // Preparar la respuesta de error
            $response = [
                "success" => false,
                "message" => "Error al crear PaymentGatewaysFee: ".$e->getMessage(),
            ];
        }

        // Finalizar el registro de log
        LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

        // Devolver la respuesta JSON
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PaymentGatewaysFee  $paymentGatewaysFee
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Iniciar el registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewaysFeeController", "show");

        $PaymentGatewaysFee = PaymentGatewaysFee::where("id", $id)->first();

        // Verificar si la consulta falló o retornó null
        if ($PaymentGatewaysFee === null) {
            // Preparar la respuesta de error
            $response = [
                "success" => false,
                "response" => [],
                "message" => "PaymentGatewaysFee no encontrado o error en la consulta.",
            ];
        } else {
            // Preparar la respuesta de éxito
            $response = [
                "success" => true,
                "response" => ['paymentGatewaysFee' => $PaymentGatewaysFee],
                "message" => "Información del PaymentGatewaysFee obtenida con éxito.",
            ];
        }

        // Finalizar el registro de log
        LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

        // Registrar la acción en el módulo correspondiente
        LogsModule::createLog("Payment gateways fees", "View", "admin/settings/gateway/fees", $PaymentGatewaysFee->id ?? null, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        // Devolver la respuesta JSON
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\PaymentGatewaysFee  $paymentGatewaysFee
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentGatewaysFee $paymentGatewaysFee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PaymentGatewaysFee  $paymentGatewaysFee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        // Iniciar el registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewaysFeeController", "update");

        try {
            $paymentGatewaysFee = PaymentGatewaysFee::where("id", $id)->first();

            if (! $paymentGatewaysFee) {
                throw new Exception("PaymentGatewaysFee no encontrado.");
            }

            \Log::debug($request->input());
            $data = $request->input();
            $data['company_id'] = $request->header('company');

            $paymentGatewaysFee->update($data);

            $response = [
                "success" => true,
                "response" => ['paymentGatewaysFee' => $paymentGatewaysFee],
                "message" => "Información del PaymentGatewaysFee obtenida con éxito.",
            ];
        } catch (Exception $e) {
            $response = [
                "success" => false,
                "message" => $e->getMessage(),
            ];
        }

        // Finalizar el registro de log
        LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

        // Registrar la acción en el módulo correspondiente
        LogsModule::createLog("Payment gateways fees", "update", "admin/settings/gateway/fees", $paymentGatewaysFee->id ?? null, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        // Devolver la respuesta JSON
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PaymentGatewaysFee  $paymentGatewaysFee
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentGatewaysFee $paymentGatewaysFee, Request $request)
    {
        // Iniciar el registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewaysFeeController", "delete");

        // Verificar si existen registros asociados en PaymentPaymentGatewaysFee
        $relatedRecords = PaymentPaymentGatewaysFee::where('payment_gateways_fee_id', $paymentGatewaysFee->id)->exists();

        if ($relatedRecords) {
            // Finalizar el registro de log con respuesta de error
            $response = [
                "success" => false,
                "message" => "No se puede eliminar: ya existen registros asociados con este fee.",
            ];
            LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

            // Devolver la respuesta JSON con error
            return response()->json($response);
        } else {
            // Realizar el borrado lógico actualizando el campo deleted_at
            $paymentGatewaysFee->deleted_at = now();
            $paymentGatewaysFee->save();

            // Preparar la respuesta de éxito
            $response = [
                "success" => true,
                "message" => "PaymentGatewaysFee eliminado con éxito.",
            ];

            // Finalizar el registro de log con respuesta de éxito
            LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

            // Registrar la acción en el módulo correspondiente
            LogsModule::createLog("Payment gateways fees", "delete", "admin/settings/gateway/fees", $paymentGatewaysFee->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

            // Devolver la respuesta JSON con éxito
            return response()->json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PaymentGatewaysFee  $paymentGatewaysFee
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Iniciar el registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewaysFeeController", "delete");

        \Log::debug("pop");
        \Log::debug($request->input());
        $paymentGatewaysFee = PaymentGatewaysFee::where("id", $request->id)->first();
        \Log::debug($paymentGatewaysFee);

        if($paymentGatewaysFee == null) {

            // Finalizar el registro de log con respuesta de error
            $response = [
               "success" => false,
               "message" => "No se puede eliminar: ya existen registros asociados con este fee.",
            ];
            LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

            // Devolver la respuesta JSON con error
            return response()->json($response);
        }

        // Verificar si existen registros asociados en PaymentPaymentGatewaysFee
        $relatedRecords = PaymentPaymentGatewaysFee::where('payment_gateways_fee_id', $paymentGatewaysFee->id)->exists();
        \Log::debug($relatedRecords);
        if ($relatedRecords) {
            // Finalizar el registro de log con respuesta de error
            $response = [
                "success" => false,
                "message" => "No se puede eliminar: ya existen registros asociados con este fee.",
            ];
            LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

            // Devolver la respuesta JSON con error
            return response()->json($response);
        } else {
            // Realizar el borrado lógico actualizando el campo deleted_at
            $paymentGatewaysFee->deleted_at = now();
            $paymentGatewaysFee->save();

            // Preparar la respuesta de éxito
            $response = [
                "success" => true,
                "message" => "PaymentGatewaysFee eliminado con éxito.",
            ];

            // Finalizar el registro de log con respuesta de éxito
            LogsDev::finishLog($log, $response, $time, 'D', "PaymentGatewaysFeeController");

            // Registrar la acción en el módulo correspondiente
            LogsModule::createLog("Payment gateways fees", "delete", "admin/settings/gateway/fees", $paymentGatewaysFee->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

            // Devolver la respuesta JSON con éxito
            return response()->json($response);
        }
    }
}
