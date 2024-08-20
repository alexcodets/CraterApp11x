<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\BandwidthRequest;
use Crater\Models\BwConfig;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class BandwidthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "BandwidthController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $bandwidths = BwConfig::applyFilters(
            $request->only([
                'orderByField',
                'orderBy'
            ])
        )
            ->paginateData($limit);

        $count = BwConfig::count();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'bandwidths' => $bandwidths,
                    'bandwidthTotalCount' => $count,
                ],
                "message" => "Bandwidths list"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Bandwidths list end");

        return response()->json([
            'bandwidths' => $bandwidths,
            'bandwidthTotalCount' => $count
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BandwidthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BandwidthRequest $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "BandwidthController", "store");

        $config = BwConfig::createBwConfig($request);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'config' => $config,
                    'success' => true,
                ],
                "message" => "Guardado Bandwidth config"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Guardado Bandwidth config");

        LogsModule::createLog(
            "Bandwidth",
            "Add config",
            "admin/settings/bandwidth/add-config",
            $config->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Bw Config: ".$config->url
        );

        return response()->json([
            'bw_config' => $config,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, BwConfig $bandwidth)
    {
        $time = microtime(true);
        $request->merge(['bw_config' => $bandwidth]);

        $log = LogsDev::initLog($request, "", "D", "BandwidthController", "show");

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'config' => $bandwidth,
                ],
                "message" => "Show Bandwidth config"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Show Bandwidth config");

        LogsModule::createLog(
            "Bandwidth",
            "Show",
            "admin/settings/bandwidth/:id/edit-config",
            $bandwidth->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Bw Config: ".$bandwidth->url
        );

        return response()->json([
            'bw_config' => $bandwidth,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BandwidthRequest $request
     * @param BwConfig $config
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BandwidthRequest $request, BwConfig $bandwidth)
    {
        $time = microtime(true);
        $request->merge(['bw_config' => $bandwidth]);
        $log = LogsDev::initLog($request, "", "D", "BandwidthController", "update");

        $bandwidth = $bandwidth->updateBwConfig($request);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'config' => $bandwidth,
                    'success' => true,
                ],
                "message" => "Update Bandwidth config"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Update Bandwidth config");

        LogsModule::createLog(
            "Bandwidth",
            "Add config",
            "admin/settings/bandwidth/edit-config",
            $bandwidth->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Bw Config: ".$bandwidth->url
        );

        return response()->json([
            'bw_config' => $bandwidth,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "BandwidthController", "delete");

        $config = BwConfig::findOrfail($request->id);

        $config->delete();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Borrado de Bandwidth config"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Borrado de Bandwidth config");

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateDefault(Request $request)
    {
        \DB::table('bw_configs')->update(['is_default' => 0]);

        $config = BwConfig::findOrFail($request->id);
        $config->is_default = $request->default;
        $config->save();

        return response()->json([
            'bw_config' => $config,
            'success' => true,
        ]);
    }
}
