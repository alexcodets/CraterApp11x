<?php

namespace Crater\Http\Controllers\V1\CustomField;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomFieldRequest;
use Crater\Models\CustomField;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
//// Models
use Illuminate\Http\Request;

class CustomFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Inicializar log si es necesario
        // ...

        // Determinar el límite de paginación
        $limit = $request->input('limit', 5);

        // Asignar 'label' si 'orderByField' es 'type.label'
        $orderByField = $request->input('orderByField') === 'type.label' ? 'label' : $request->input('orderByField');

        // Aplicar filtros y obtener campos personalizados
        $customFields = CustomField::whereCompany($request->header('company'))
            ->applyFilters($request->only(['type', 'search', $orderByField, 'orderBy']))
            ->latest()
            ->paginateData($limit);

        // Finalizar log si es necesario
        // ...

        // Registrar actividad del módulo
        LogsModule::createLog(
            "CustomFields",
            "List",
            "admin/settings/custom-fields",
            0,
            auth()->user()->name,
            auth()->user()->email,
            auth()->user()->role,
            auth()->user()->company_id
        );

        // Devolver respuesta JSON
        return response()->json(['customFields' => $customFields]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CustomFieldRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomFieldRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomFieldsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $customField = CustomField::createCustomField($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customField' => $customField,
            'success' => true,
        ], "message" => "store customFields"]];
        LogsDev::finishLog($log, $res, $time, 'D', "store customFields");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("CustomFields", "Create", "/admin/settings/custom-fields", $customField->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "customField: ".$customField->name);

        return response()->json([
            'customField' => $customField,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, CustomField $customField)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['CustomField' => $customField]);
        $log = LogsDev::initLog($request, "", "D", "CustomFieldsController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customField' => $customField,
            'success' => true,
        ], "message" => "show customFields"]];
        LogsDev::finishLog($log, $res, $time, 'D', "show customFields");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("CustomFields", "View", "/admin/settings/custom-fields", $customField->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "customField: ".$customField->name);

        return response()->json([
            'customField' => $customField,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomFieldRequest $request, CustomField $customField)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomFieldsController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $customField->updateCustomField($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customField' => $customField,
            'success' => true,
        ], "message" => "update customFields"]];
        LogsDev::finishLog($log, $res, $time, 'D', "update customFields");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("CustomFields", "Update", "/admin/settings/custom-fields", $customField->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "customField: ".$customField->name);

        return response()->json([
            'customField' => $customField,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CustomField $customField)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['CustomField' => $customField]);
        $log = LogsDev::initLog($request, "", "D", "CustomFieldsController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($customField->customFieldValue()->exists()) {
            return response()->json([
                'error' => 'values_attached',
            ]);
        }

        // Logs por modulo
        LogsModule::createLog("CustomFields", "delete", "/admin/settings/custom-fields", $customField->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "customField: ".$customField->name);

        $customField->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "destrory customFields"]];
        LogsDev::finishLog($log, $res, $time, 'D', "destrory customFields");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => true,
        ]);
    }
}
