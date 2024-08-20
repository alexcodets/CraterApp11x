<?php

namespace Crater\Http\Controllers;

use Crater\Models\itemCategories;
use Illuminate\Http\Request;

class ItemCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 5;
        // con paginates
        $ListCategoriesItems = itemCategories::applyFilters(
            $request->only([
                'orderByField',
                'orderBy',
            ])
        )
            ->latest()
            ->paginateData($limit);

        // api
        return response()->json([
            'message' => 'List Categories Items',
            'categories' => $ListCategoriesItems
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
        // save data in database
        $itemCategories = itemCategories::create($request->all());

        // api
        return response()->json([
            'message' => 'Item Categories created successfully',
            'item' => $itemCategories
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\itemCategories  $itemCategories
     * @return \Illuminate\Http\Response
     */
    public function show(itemCategories $itemCategories)
    {
        $itemCategories = itemCategories::find($itemCategories->id);

        return response()->json($itemCategories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\itemCategories  $itemCategories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $itemCategories = itemCategories::find($id);
        $itemCategories->update($request->all());

        return response()->json([
            'message' => 'Item Categories updated successfully',
            'item' => $itemCategories
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\itemCategories  $itemCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $itemCategories = itemCategories::find($id);
        $itemCategories->delete();

        return response()->json([
            'message' => 'Item Categories deleted successfully'
        ]);
    }
}
