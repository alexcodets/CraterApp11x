<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\TaxCategory;
use Illuminate\Http\Request;

class TaxCategoryController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "TaxCategoryController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $taxCategories = TaxCategory::applyFilters($request->only([
            'orderByField',
            'orderBy',
        ]))
            ->select('*')
            ->latest()
            ->paginateData($limit);


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxCategories' => $taxCategories,
        ], "message" => "TaxCategoryController"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxCategoryController");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Tax Categories", "List", "admin/settings/tax-categories", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'taxCategories' => $taxCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxCategoryController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $request = $request->validate([
            'name' => 'required'
        ]);

        $taxCategory = TaxCategory::create($request);

        // Logs por modulo
        LogsModule::createLog("Tax Types", "Create", "admin/settings/tax-types", $taxCategory->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxCategory->name);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxCategory' => $taxCategory,
        ], "message" => "TaxCategoryController"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxCategoryController");
        /////////////////////////////////////////

        return response()->json([
            'taxCategory' => $taxCategory,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\TaxCategory  $taxCategory
     * @return \Illuminate\Http\Response
     */
    public function show(TaxCategory $taxCategory, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxCategory' => $taxCategory]);
        $log = LogsDev::initLog($request, "", "D", "TaxTypesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        /*  //Log::debug('update category ---------');
        //Log::debug($request); */

        $taxCategory = TaxCategory::find($taxCategory['id']);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxCategory' => $taxCategory,
        ], "message" => "TaxTypesController show"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxTypesController show");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Tax Types", "View", "admin/settings/tax-types", $taxCategory->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxCategory->name);

        return response()->json([
            'taxCategory' => $taxCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\TaxCategory  $taxCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxCategory $taxCategory)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxCategory' => $taxCategory]);
        $log = LogsDev::initLog($request, "", "D", "TaxCategoryController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $request = $request->validate([
            'name' => 'required',
            'id' => ''
        ]);

        //Log::debug('--- update ---');
        //Log::debug($request);

        $taxCategory = TaxCategory::find($request['id']);
        $taxCategory->name = $request['name'];
        $taxCategory->save();

        // Logs por modulo
        LogsModule::createLog("Tax Types", "Update", "admin/settings/tax-types", $taxCategory->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxCategory->name);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxCategory' => $taxCategory,
        ], "message" => "TaxCategoryController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxCategoryController update");
        /////////////////////////////////////////

        return response()->json([
            'taxCategory' => $taxCategory,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
