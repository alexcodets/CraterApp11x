<?php

namespace Crater\Http\Controllers\V1\EstimatesCustomer;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Estimate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\User;
use Illuminate\Http\Request;

class EstimateCustomersController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "EstimateCustomersController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $user = User::where("id", Auth::id())->first();

        $limit = $request->has('limit') ? $request->limit : 10;

        $estimates = Estimate::with([
            'items',
            'user',
            'estimateTemplate',
            'taxes',
            'creator',
        ])
            ->join('users', 'users.id', '=', 'estimates.user_id')
            ->applyFilters($request->only([
                'status',
                'customer_id',
                'estimate_id',
                'estimate_number',
                'from_date',
                'to_date',
                'search',
                'orderByField',
                'orderBy',
            ]))
            /* ->whereCompany($request->header('company')) */
            ->whereCompany($user->company_id)
             ->where('estimates.user_id', $user->id)
             ->where('estimates.status', "!=", "DRAFT")
            ->select('estimates.*', 'users.name', 'users.customcode')
            ->latest()
            ->paginateData($limit);

        $siteData = [
            'estimates' => $estimates,
            'estimateTotalCount' => Estimate::count(),
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $siteData, "message" => "__invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "__invoke");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Estimates", "List", "/admin/estimates", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($siteData);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "EstimatesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $estimate = Estimate::with([
            'items',
            'items.taxes',
            'user',
            'estimateTemplate',
            'creator',
            'taxes',
            'taxes.taxType',
            'fields.customField',
    ]) ->where('estimates.id', $id)
         ->first();


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'estimate' => $estimate,
            'nextEstimateNumber' => $estimate->getEstimateNumAttribute(),
            'estimatePrefix' => $estimate->getEstimatePrefixAttribute(),
        ], "message" => "show estimate"]];
        LogsDev::finishLog($log, $res, $time, 'D', "show estimate");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Estimates", "View", "admin/estimates/id/view", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate: ".$estimate->estimate_number);

        return response()->json([
            'estimate' => $estimate,
            'nextEstimateNumber' => $estimate->getEstimateNumAttribute(),
            'estimatePrefix' => $estimate->getEstimatePrefixAttribute(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function edit(Estimate $estimate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estimate $estimate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estimate $estimate)
    {
        //
    }
}
