<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TollFreeCategory;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxCategorie;
use Illuminate\Http\Request;

class PbxCategorieController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "PbxCategorieController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 5;
        $orderBy = $request->has('orderBy') ? $request["orderBy"] : 'desc';
        $orderByField = $request->has('orderByField') ? $request["orderByField"] : 'id';

        $categories = PbxCategorie::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'category_id',
                'search',
            ]))
            ->orderBy($orderByField, $orderBy)
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'categories' => $categories,
        ], "message" => "Did index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Toll Free index");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Did", "List", "/admin/corePBX/billing-templates/toll-free", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'categories' => $categories,
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
    public function store(TollFreeCategory $request)
    {

        $data = $request->validated();

        $data['company_id'] = $request->header('company');
        $category = PbxCategorie::create($data);

        // Logs por modulo
        LogsModule::createLog("DidTollFree", "Create", "admin/corePBX/billing-templates/toll-free", $category->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Did Category: ".$category->name);

        return response()->json([
            'category' => $category,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PbxCategorie  $pbxCategorie
     * @return \Illuminate\Http\Response
     */
    public function show(PbxCategorie $pbxCategorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\PbxCategorie  $pbxCategorie
     * @return \Illuminate\Http\Response
     */
    public function edit(PbxCategorie $pbxCategorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PbxCategorie  $pbxCategorie
     * @return \Illuminate\Http\Response
     */
    public function update(TollFreeCategory $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "DidTollFreeupdate");
        //////////////////

        $data = $request->validated();
        $category = PbxCategorie::find($id);
        $category->update($data);


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'category' => $category,
        ], "message" => "Item update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Item  update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("PbxCategorie", "Update", "admin/items/id/edit", $category->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Item: ".$category->name);

        return response()->json([
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PbxCategorie  $pbxCategorie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxCategorieController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $category = PbxCategorie::find($id);
        if ($category) {
            $category->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The record does not exist'
            ]);
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "soft delete success"];
        LogsDev::finishLog($log, $res, $time, 'D', "soft delete success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Record removed successfully.'
        ]);
    }
}
