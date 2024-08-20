<?php

namespace Crater\Http\Controllers\V1\PrefixGroup;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PrefixGroupRequest;
use Crater\Models\CustomRateGroupItems;
use Crater\Models\InternationalRate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
//
use Crater\Models\PrefixGroup;
use DB;
use Illuminate\Http\Request;

class PrefixGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $time = microtime(true);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $log = LogsDev::initLog($request, "", "D", "PrefixGroupController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $prefixGroups = PrefixGroup::where('status', 'A')
            ->applyFilters(
                $request->only([
                    'name',
                    'description',
                    'type',
                    'orderByField',
                    'orderBy'
                ])
            )
            ->whereCompany($request->header('company'))
            ->paginateData($limit);

        $count = PrefixGroup::where('status', 'A')->count();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'prefixGroups' => $prefixGroups,
                    'itemGroupsTotalCount' => $count,
                ],
                "message" => "Listado de Grupos de prefijos"
            ]
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        LogsDev::finishLog($log, $res, $time, 'D', "Fin de index Prefix Groups");

        // Logs por modulo
        LogsModule::createLog(
            "Prefixes Groups",
            "Index",
            "/admin/corePBX/billing-templates/prefix-groups",
            0,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id
        );

        return response()->json([
            'prefixGroups' => $prefixGroups,
            'prefixGroupsTotalCount' => $count
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PrefixGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PrefixGroupRequest $request)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PrefixGroupController", "store");

        // Create prefix group
        $prefixGroup = PrefixGroup::createPrefixGroup($request);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'prefixGroup' => $prefixGroup,
                    'success' => true,
                ],
                "message" => "Guardado grupo de prefijos"
            ]
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Guardado grupo de prefijos");

        // Module log
        LogsModule::createLog(
            "Prefixes Groups",
            "Create",
            "/admin/corePBX/billing-templates/prefix-groups/create",
            $prefixGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "PrefixGroup: ".$prefixGroup->name
        );

        return response()->json([
            'prefixGroup' => $prefixGroup,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param PrefixGroup $prefixGroup
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, PrefixGroup $prefixGroup)
    {
        $time = microtime(true);
        $request->merge(['prefixGroup' => $prefixGroup]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PrefixGroupController", "show");

        $prefixGroup->load('PrefixGroup');

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'prefixGroup' => $prefixGroup,
                ],
                "message" => "Mostrar Grupo de prefijos"
            ]
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Mostrar Grupo de prefijos");

        // Module log
        LogsModule::createLog(
            "Prefix Groups",
            "Show",
            "/admin/corePBX/billing-templates/prefix-groups/:id/view",
            $prefixGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Prefix Group: ".$prefixGroup->name
        );

        return response()->json([
            'prefixGroup' => $prefixGroup,
            'success' => true,
        ]);
    }

    public function showNew(Request $request)
    {
        $array = CustomRateGroupItems::where('prefixrate_id', $request->id)->where('deleted_at', null)->pluck('int_rate_id');

        $limit = $request->has('limit') ? $request->limit : 10;

        $international_rate = InternationalRate::whereIn('id', $array)
                              ->applyFilters($request->only(['category', 'name', 'country_id' ,'prefix_type', 'prefix', 'from', 'to']))
                              ->orderBy('id', 'DESC')
                              ->paginateData($limit);

        return response()->json([
            'international_rate' => $international_rate,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param PrefixGroupRequest $request
     * @param PrefixGroup $prefixGroup
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PrefixGroupRequest $request, PrefixGroup $prefixGroup)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PrefixGroupController", "update");

        // Update options groups
        $prefixGroup = PrefixGroup::updatePrefixGroup($request, $prefixGroup);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'prefixGroup' => $prefixGroup,
                    'success' => true,
                ],
                "message" => "Actualizacion grupo de prefijos"
            ]
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion grupo de prefijos");

        // Module log
        LogsModule::createLog(
            "Prefixes Groups",
            "Update",
            "/admin/corePBX/billing-templates/prefix-groups/:id/edit",
            $prefixGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Prefixes Group: ".$prefixGroup->name
        );

        return response()->json([
            'prefixGroup' => $prefixGroup,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        //// Log init
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PrefixGroupController", "delete");

        $has_a_related_package = DB::table('pbx_packages_prefixrate_groups')
                   ->where('prefixrate_group_id', $request->ids)
                   ->get();

        if($has_a_related_package->isNotEmpty()) {
            return response()->json([
                'message' => 'It cannot be removed, it is related to a package',
                'success' => false
            ], 406);
        }

        $prefixGroup = PrefixGroup::find($request->ids);

        PrefixGroup::deletePrefixGroup($request->ids);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Borrado de Grupos de prefijos"
            ]
        ];

        //// Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "Borrado de Grupos de prefijos");

        return response()->json([
            'success' => true,
        ]);
    }
}
