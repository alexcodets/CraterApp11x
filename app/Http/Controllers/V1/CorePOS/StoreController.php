<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Item;
use Crater\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            //Log::debug($request);
            $limit = $request->has('limit') ? $request->limit : 10;
            $query = Store::query();
            $store = $query->select(
                'companies.name as company_name',
                'stores.id as id',
                'stores.name as name',
                'stores.description as description',
                'stores.created_at as created_at'
            )
                ->join('companies', 'stores.company_id', '=', 'companies.id')
                ->applyFilters($request->only([
                    'name',
                    'description',
                    'company_name',
                    'orderByField',
                    'orderBy',
                ]))
                ->latest()
                ->paginateData($limit);

            $res = [
                "success" => true,
                "message" => "Stores index",
                "response" => [
                    "data" => $store,
                ]
            ];

            //Log::debug($store);
            return response()->json([
                "success" => true,
                'stores' => $store,
            ]);
        } catch (\Throwable $th) {
            //Log::debug($th);
            return response()->json([
                "success" => false,
                'error' => $th
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $user = auth()->user();
            $data['user_id'] = $user->id;
            $data['company_id'] = $user->company->id;
            $store = Store::create($data);
            if (! empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    if ($item['item_id'] != null) {
                        DB::table('item_store')->insert([
                            'store_id' => $store->id,
                            'item_id' => $item['item_id']
                        ]);

                        $obj = Item::where("id", $item['item_id'])->first();
                        if($obj != null) {
                            $obj->allow_pos = 1;
                            $obj->save();
                        }
                    }
                }
            }

            if (! empty($data['item_groups'])) {
                foreach ($data['item_groups'] as $item) {
                    DB::table('item_group_store')->insert([
                        'store_id' => $store->id,
                        'item_group_id' => $item['id'],
                        'created_at' => Carbon::now()->format('Y-m-d')
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'store created successful'
            ]);
        } catch (Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
                'message' => 'Error'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        try {

            $store = Store::where('id', $store->id)->whereNull('deleted_at')->first();
            $items = $store->items->whereNull('deleted_at');
            $itemsGroup = $store->itemsGroups->whereNull('deleted_at');

            return response()->json([
                'success' => true,
                'store' => $store,
                'items' => $items,
                'items_group' => $itemsGroup
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        try {
            $response = $request->all();
            $id = $response['id'];
            $data = $response['data'];
            Store::where('id', $store->id)
                ->update([
                    'name' => $data['name'],
                    'description' => $data['description']
                ]);

            DB::table('item_store')->where('store_id', $store->id)->whereNull('deleted_at')->delete();
            DB::table('item_group_store')->where('store_id', $store->id)->whereNull('deleted_at')->delete();

            if (! empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $item['item_id'] = array_key_exists('item_id', $item) ? $item['item_id'] : $item['id'];
                    if ($item['item_id'] != null) {
                        DB::table('item_store')->insert([
                            'store_id' => $store->id,
                            'item_id' => $item['item_id']
                        ]);

                        $obj = Item::where("id", $item['item_id'])->first();
                        if($obj != null) {
                            $obj->allow_pos = 1;
                            $obj->save();
                        }
                    }
                }
            }

            if (! empty($data['item_groups'])) {
                foreach ($data['item_groups'] as $item) {
                    $item['item_id'] = array_key_exists('item_id', $item) ? $item['item_id'] : $item['id'];
                    DB::table('item_group_store')->insert([
                        'store_id' => $store->id,
                        'item_group_id' => $item['item_id'],
                        'created_at' => Carbon::now()->format('Y-m-d')
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'store created successful'
            ]);
        } catch (Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
                'message' => 'Error'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }

    public function deleteStore(Request $request)
    {
        try {

            $data = $request->all();
            Store::where('id', $data['id'])->delete();

            DB::table('item_store')->where('store_id', $data['id'])->update(['deleted_at' => Carbon::now()->format('Y-m-d')]);
            DB::table('item_group_store')->where('store_id', $data['id'])->update(['deleted_at' => Carbon::now()->format('Y-m-d')]);

            return response()->json([
                'success' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function getStores(Request $request)
    {
        try {
            $storesItems = DB::table('item_store')->whereIn('item_id', $request['items'])->get();
            $storeIds = [];
            foreach ($storesItems as $result) {
                $storeIds[$result->store_id] = true;
            }
            $storeIds = array_keys($storeIds);
            $stores = Store::whereIn('id', $storeIds)->get();

            return response()->json([
                'success' => true,
                'stores' => $stores
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);
            //throw $th;

            return response()->json([
                'success' => false,
                'message' => 'Error'
            ]);
        }
    }
}
