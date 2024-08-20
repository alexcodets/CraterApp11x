<?php

namespace Crater\Http\Controllers\V1\Item;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests;
use Crater\Http\Requests\DeleteItemsRequest;
use Crater\Models\AvalaraConfig;
use Crater\Models\AvalaraServiceType;
use Crater\Models\Item;
use Crater\Models\itemCategories;
use Crater\Models\ItemGroup;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxPackageItem;
use Crater\Models\TaxType;
use Crater\Models\Unit;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ItemsController extends Controller
{
    /**
     * Retrieve a list of existing Items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "index");
        //////////////////

        //search limit
        $searchTerm = $request->input('search');
        $searchCategory = $request->input('categories_id');
        $searchStore = $request->input('store_id');
        $company = $request->header('company');
        $limit = $request->input('limit', 10); // Asegúrate de definir $limit

        $query = Item::with(['taxes', 'creator', 'retentions', 'itemCategories', 'itemStore'])
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->when($request['is_pos'], function ($query) use ($searchStore, $searchTerm, $searchCategory, $company) {
                return $query->join('item_store', 'item_store.item_id', '=', 'items.id')
                    ->join('items_item_categories', 'items.id', '=', 'items_item_categories.item_id')
                    ->where('items.allow_pos', true)
                    ->where('items.company_id', $company)
                    ->when($searchStore, fn ($q) => $q->where('item_store.store_id', $searchStore))
                    ->when($searchTerm, fn ($q) => $q->where('items.name', 'LIKE', "%{$searchTerm}%"))
                    ->when($searchCategory, fn ($q) => $q->whereIn('items_item_categories.item_category_id', $searchCategory));
            }, function ($query) use ($request, $company, $limit) { // Asegúrate de incluir $company y $limit aquí
                return $query->applyFilters($request->only([
                    'search', 'price', 'unit_id', 'item_id', 'orderByField', 'orderBy', 'avalara_bool', 'is_pos', 'categories_id',
                ]))
                    ->whereCompany($company);
            })
            ->select('items.*', 'units.name as unit_name')
            ->latest();

        $items = $request['is_pos'] ? $query->paginate(10000) : $query->paginateData($limit);

        // Preparar los datos de respuesta
        $responseData = [
            'items' => $items,
            'taxTypes' => TaxType::latest()->get(),
            'itemTotalCount' => Item::count(),
        ];

        // Registro de log de desarrollo
        $res = ["success" => true, "response" => ["datamesage" => $responseData, "message" => "Item Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Item Index");

        // Registro de log por módulo
        $user = Auth::user();
        LogsModule::createLog("Items", "List", "admin/items", 0, $user->name, $user->email, $user->role, $user->company_id);

        // Devolver la respuesta JSON utilizando los datos preparados
        return response()->json($responseData);
    }

    /**
     * Método para cargar selectores de formularios con elementos de ítems.
     * Este método es exclusivo para ser utilizado en formularios donde se requiere seleccionar ítems.
     *
     * @param Request $request Datos de la solicitud que pueden incluir parámetros como 'limit'.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con los ítems para el selector.
     */
    public function indexselectitem(Request $request)
    {
        // Definir el límite de ítems por página, con un valor predeterminado de 10 si no se especifica en la solicitud.
        $limit = $request->input('limit', 10);

        // Obtener los ítems con solo los campos 'id' y 'name' para el selector.
        // La paginación se aplica según el límite definido anteriormente.
        $items = Item::select('items.id', 'items.name')->paginateData($limit);

        // Preparar los datos de respuesta con los ítems obtenidos.
        $responseData = [
            'items' => $items,
        ];

        // Devolver la respuesta en formato JSON.
        return response()->json($responseData);
    }

    /**
     * Create Item.
     *
     * @param  Crater\Http\Requests\ItemsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\ItemsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "store");

        $item = Item::createItem($request);

        try {
            // save stores
            if (isset($request->item_store)) {
                foreach ($request->item_store as $store) {
                    DB::table('item_store')->insert([
                        'item_id' => $item->id,
                        'store_id' => $store['id'],
                        'created_at' => Carbon::now(),
                    ]);
                }
            }

            // save sections
            foreach ($request->item_section as $section) {
                DB::table('item_section')->insert([
                    'item_id' => $item->id,
                    'section_id' => $section['id'],
                    'created_at' => Carbon::now(),
                ]);
            }
        } catch (\Throwable $th) {
            Log::debug($th);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'item' => $item,
        ], "message" => "Item store"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Item  store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Items", "Create", "admin/items/create", $item->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Item: ".$item->name);

        return response()->json([
            'item' => $item,
        ]);
    }

    /**
     * get an existing Item.
     *
     * @param  Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Item $item)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Item' => $item]);

        $log = LogsDev::initLog($request, "", "D", "ItemsController", "show");
        //////////////////

        $item->load('taxes', 'itemGroups', 'retentions', 'itemCategories', 'itemStore', 'itemSection');

        if ($item['avalara_service_type'] != null) {
            $avalara_service_type = AvalaraServiceType::where('id', $item['avalara_service_type'])->first();
            $item['avalara_service_types'] = $avalara_service_type;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'item' => $item,
        ], "message" => "Item show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Item  show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Items", "Update", "admin/items/id/edit", $item->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Item: ".$item->name);

        return response()->json([
            'item' => $item,
        ]);
    }

    /**
     * Update an existing Item.
     *
     * @param  Crater\Http\Requests\ItemsRequest $request
     * @param  \Crater\Models\Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Requests\ItemsRequest $request, Item $item)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Item' => $item]);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "update");
        //////////////////

        $item = $item->updateItem($request);
        DB::table('item_store')->where('item_id', $item['data']['id'])->delete();
        // save stores
        foreach ($request->item_store as $store) {
            DB::table('item_store')->insert([
                'item_id' => $item['data']['id'],
                'store_id' => $store['id'],
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);
        }
        DB::table('item_section')->where('item_id', $item['data']['id'])->delete();
        // save stores
        foreach ($request->item_section as $section) {
            DB::table('item_section')->insert([
                'item_id' => $item['data']['id'],
                'section_id' => $section['id'],
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'item' => $item['data'],
        ], "message" => "Item update"]];

        if (! $item['success']) {
            return response()->json(['message' => 'You cannot change a false avalara option as it is being used in avalara'], 422);
        }
        LogsDev::finishLog($log, $res, $time, 'D', "Item  update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Items", "Update", "admin/items/id/edit", $item['data']->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Item: ".$item['data']->name);

        return response()->json([
            'item' => $item['data'],
        ]);
    }

    /**
     * Delete a list of existing Items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteItemsRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "delete");
        //////////////////

        foreach ($request->ids as $id) {
            $item = Item::find($id);
            LogsModule::createLog("Items", "delete", "/items/delete", $item->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Item: ".$item->name);

            Item::deleteItemGroups($item); // Eliminar los grupos asociados

            Log::info("Item deleted: ".$item->name);
            $item->delete(); // Eliminar item
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "Item destroy"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Item  destroy");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function avalaraServiceTypes(Request $request, $code)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "avalaraServiceTypes");
        ///////////////////////////////////////

        $avalara_service_types = AvalaraServiceType::select('*')->where('avalara_transaction_types', $code)->orderBy('service_type', 'asc')->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'avalara_service_types' => $avalara_service_types,
        ], "message" => "Avalara service types list"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Avalara service types list");
        /////////////////////////////////////////

        return response()->json([
            'avalara_service_types' => $avalara_service_types,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function setPrefix(Request $request)
    {
        //
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "setPrefix");
        //////////////////////////////////////////////////////////////////////////////////////////

        $prefix = $request->item_prefix;
        $itemslist = Item::all();

        if (! isset($prefix) || trim($prefix) !== '') {
            $itemslist->each(function ($pro) use ($prefix) {
                $up = Item::find($pro['id']);
                $up->item_number = $prefix.'-000'.$pro['id'];
                $up->save();
            });
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "setPrefix"]];
        LogsDev::finishLog($log, $res, $time, 'D', "setPrefix");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    public function uploadPicture(Request $request)
    {
        $time = microtime(true);
        //  $log = LogsDev::initLog($request, "", "D", "ItemsController", "uploadPicture");

        $data = json_decode($request->picture);

        if ($data) {
            $item = Item::find($data->item_id);

            if ($item) {
                // Logs por modulo
                LogsModule::createLog(
                    "Items",
                    "Upload Picture",
                    "admin/items/upload-picture",
                    $item->id,
                    Auth::user()->name,
                    Auth::user()->email,
                    Auth::user()->role,
                    Auth::user()->company_id,
                    "Item upload picture: ".$item->name
                );

                $item->clearMediaCollection('item_picture');

                $item->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('item_picture');
            }
        }

        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "uploadPicture"];

        //LogsDev::finishLog($log, $res, $time, 'D', "uploadPicture");

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * method to get item usage
     */
    public function getUsage(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ItemsController", "getUsage");
        //////////////////////////////////////////////////////////////////////////////////////////

        $pbx_package_item = PbxPackageItem::select('id')->where('items_id', $id)->get();
        $avalara_config_item = AvalaraConfig::select('id')->where('item_did_id', $id)->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "getUsage"]];
        LogsDev::finishLog($log, $res, $time, 'D', "getUsage");
        /////////////////////////////////////////

        return response()->json([
            'pbx_package_item' => $pbx_package_item,
            'avalara_config_item' => $avalara_config_item,
            'success' => true,
        ]);
    }

    public function getItemCategories()
    {
        return response()->json([
            'item_categories' => itemCategories::where("is_item", 1)->get(),
        ]);
    }

    public function getItems()
    {
        $customers = User::where("role", "customer")->whereNull("deleted_at")->get();
        $users = User::where("role", "super admin")->whereNull("deleted_at")->get();

        return response()->json([
            'items' => Item::with('itemStore')->where('company_id', auth()->user()->company->id),
            'units' => Unit::all(),
            'items_categories' => itemCategories::all(),
            'items_groups' => ItemGroup::all(),
            'customers_by_item' => $customers,
            'users_by_item' => $users,
        ]);
    }

    public function getItemsByFilters(Request $request)
    {
        $items = Item::select("*")
            ->when(($request['units_id'] != null), function ($query) use ($request) {
                $query->whereIn('unit_id', $request['units_id']);
            })
        //  ->when(($request['categories_id'] != null), function ($query) use ($request) {
        //     $query->whereIn('item_category_id', $request['categories_id']);
        //  })
            ->when(($request['groups_id'] != null), function ($query) use ($request) {
                $items_ids = DB::table('item_group_items')
                    ->where('item_group_id', $request['groups_id'])
                    ->pluck('item_id');
                $query->whereIn('id', $items_ids);
            })
            ->whereNull("deleted_at")
            ->get();

        return response()->json([
            "items" => $items,
        ]);
    }
}
