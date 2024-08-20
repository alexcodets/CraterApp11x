<?php

namespace Crater\Http\Controllers\V1\ItemGroups;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ItemGroupRequest;
use Crater\Models\itemCategories;
use Crater\Models\ItemGroup;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ItemGroupController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $itemGroups = ItemGroup::where('status', 'A')
            ->applyFilters(
                $request->only([
                    'name',
                    'description',
                    'orderByField',
                    'orderBy'
                ])
            )
            ->whereCompany($request->header('company'))
            ->paginateData($limit);

        $count = ItemGroup::where('status', 'A')->count();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'itemGroups' => $itemGroups,
                    'itemGroupsTotalCount' => $count,
                    ],
                "message" => "Listado de Grupos de items"
            ]
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        LogsDev::finishLog($log, $res, $time, 'D', "Fin de index Items Groups");

        return response()->json([
            'itemGroups' => $itemGroups,
            'itemGroupsTotalCount' => $count
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ItemGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ItemGroupRequest $request)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "store");

        // Create options groups
        $itemGroup = ItemGroup::createItemGroup($request);

        foreach($request->items as $item) {
            DB::table('item_store')->where('item_id', $item['item_id'])->delete();
            foreach($request->item_store as $store) {


                DB::table('item_store')->insert([
                    'item_id' => $item['item_id'],
                    'store_id' => $store['id'],
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now()
                ]);

            }
        }

        $res = [
           "success" => true,
           "response" => [
               "datamesage" => [
                   'itemGroup' => $itemGroup,
                   'success' => true,
               ],
               "message" => "Guardado grupo de items"
           ]
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Guardado grupo de items");

        // Module log
        LogsModule::createLog(
            "Items Groups",
            "Create",
            "admin/item-groups/create",
            $itemGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "ItemGroup: ".$itemGroup->name
        );

        return response()->json([
            'itemGroup' => $itemGroup,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ItemGroup $itemGroup
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, ItemGroup $itemGroup)
    {
        $time = microtime(true);
        $request->merge(['itemGroup' => $itemGroup]);
        // Init log
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "show");

        foreach ($itemGroup->items as $item) {
            $item->unit_name = isset($item->unit) ? $item->unit->name : '';
            $item->item_id = $item->id;
        }

        $itemGroup->load('itemCategories');

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'itemGroup' => $itemGroup,
                ],
                "message" => "Mostrar Grupo de Items"
            ]
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Mostrar Grupo de Items");

        // Module log
        LogsModule::createLog(
            "Item Groups",
            "Show",
            "admin/item-groups/id/view",
            $itemGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "OptionsGroup: ".$itemGroup->name
        );

        return response()->json([
            'itemGroup' => $itemGroup,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ItemGroupRequest $request
     * @param ItemGroup $itemGroup
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ItemGroupRequest $request, ItemGroup $itemGroup)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "update");

        // Update options groups
        $itemGroup = ItemGroup::updateItemGroup($request, $itemGroup);

        foreach($request->items as $item) {
            DB::table('item_store')->where('item_id', $item['item_id'])->delete();
            foreach($request->item_store as $store) {


                DB::table('item_store')->insert([
                    'item_id' => $item['item_id'],
                    'store_id' => $store['id'],
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now()
                ]);

            }
        }

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'itemGroup' => $itemGroup,
                    'success' => true,
                ],
                "message" => "Actualizacion grupo de items"
            ]
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion grupo de items");

        // Module log
        LogsModule::createLog(
            "Item Groups",
            "Update",
            "admin/item-groups/id/edit",
            $itemGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "ItemsGroup: ".$itemGroup->name
        );

        return response()->json([
            'itemGroup' => $itemGroup,
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
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "delete");

        ItemGroup::deleteItemGroup($request->ids);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Borrado de Grupos de Items"
            ]
        ];

        //// Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "Borrado de Grupos de Items");

        return response()->json([
            'success' => true,
        ]);
    }

    public function uploadPicture(Request $request)
    {
        $time = microtime(true);
        //  $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "uploadPicture");

        $data = json_decode($request->picture);

        if ($data) {
            $itemGroup = ItemGroup::find($data->item_group_id);

            if ($itemGroup) {
                // Logs por modulo
                LogsModule::createLog(
                    "Item Groups",
                    "Upload Picture",
                    "admin/item-groups/upload-picture",
                    $itemGroup->id,
                    Auth::user()->name,
                    Auth::user()->email,
                    Auth::user()->role,
                    Auth::user()->company_id,
                    "Item Group upload picture: ".$itemGroup->name
                );

                $itemGroup->clearMediaCollection('item_group_picture');

                $itemGroup->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('item_group_picture');
            }
        }

        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "uploadPicture"];

        //  LogsDev::finishLog($log, $res, $time, 'D', "uploadPicture");

        return response()->json([
            'success' => true,
        ]);
    }

    public function getItemCategories()
    {
        return response()->json([
            'item_categories' => itemCategories::where("is_group", 1)->get(),
        ]);
    }
}
