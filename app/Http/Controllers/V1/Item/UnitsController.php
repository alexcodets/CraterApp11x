<?php

namespace Crater\Http\Controllers\V1\Item;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UnitRequest;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Unit;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UnitsController", "index");
        //////////////////

        $limit = $request->has('limit') ? $request->limit : 5;

        $units = Unit::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'unit_id',
                'orderByField',
                'orderBy',
            ]))
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'units' => $units,
        ], "message" => "Unit Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Unit Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Units", "List", "admin/settings/customization", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'units' => $units,
        ]);
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
    public function store(UnitRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UnitsController", "store");
        //////////////////

        $data = $request->validated();
        $data['company_id'] = $request->header('company');
        $unit = Unit::create($data);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'unit' => $unit,
        ], "message" => "Unit store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Unit store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Units", "Create", "admin/settings/customization", $unit->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Unit: ".$unit->name);

        return response()->json([
            'unit' => $unit,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Unit $unit)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['unit' => $unit]);
        $log = LogsDev::initLog($request, "", "D", "UnitsController", "show");
        //////////////////

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'unit' => $unit,
        ], "message" => "Unit show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Unit show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,


        // Logs por modulo
        LogsModule::createLog("Units", "View", "admin/settings/customization", $unit->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Unit: ".$unit->name);


        return response()->json([
            'unit' => $unit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(UnitRequest $request, Unit $unit)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['unit' => $unit]);
        $log = LogsDev::initLog($request2, "", "D", "UnitsController", "update");
        //////////////////

        $unit->update($request->validated());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'unit' => $unit,
        ], "message" => "Unit update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Unit update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,



        // Logs por modulo
        LogsModule::createLog("Units", "Update", "admin/settings/customization", $unit->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Unit: ".$unit->name);


        return response()->json([
            'unit' => $unit,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Unit $unit)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['unit' => $unit]);
        $log = LogsDev::initLog($request, "", "D", "UnitsController", "destroy");
        //////////////////

        if ($unit->items()->exists()) {
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => false, "response" => ["datamesage" => [
                'error' => 'items_attached',
            ], "message" => "error: items_attached"]];
            LogsDev::finishLog($log, $res, $time, 'D', "error: items_attached");
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

            return response()->json([
                'error' => 'items_attached',
            ]);
        }

        // Logs por modulo
        LogsModule::createLog("Units", "delete", "admin/settings/customization", $unit->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Unit: ".$unit->name);


        $unit->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Unit deleted successfully',
        ], "message" => "Unit deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Unit deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Unit deleted successfully',
        ]);
    }
}
