<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Crater\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
            $companyId = auth()->user()->company()->pluck('id')->first();
            $data = $request->all();
            Log::debug($data);

            DB::table('pos_item_categories')->where('company_id', $companyId)->delete();

            foreach($data['data'] as $itemCategory) {
                try {
                    DB::table('pos_item_categories')->insert([
                        'item_category_id' => $itemCategory['id'],
                        'company_id' => $companyId
                    ]);

                } catch (\Throwable $th) {
                    Log::debug($th);
                }
            }

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function getPosItemCategoriesCompany()
    {

        $companyId = auth()->user()->company()->pluck('id')->first();
        $itemCategories = DB::table('pos_item_categories')
            ->select('pos_item_categories.*', 'item_categories.name AS name')
            ->join('item_categories', 'pos_item_categories.item_category_id', '=', 'item_categories.id')
            ->where('company_id', $companyId)->get();

        return response()->json([
            'success' => true,
            'data' => $itemCategories
        ]);
    }
}
