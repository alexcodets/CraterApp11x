<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Crater\Http\Controllers\Controller;
use Crater\Models\CorePosHistory;
use Illuminate\Http\Request;

class CorePosHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $companyId = auth()->user()->company->id;

        $limit = $request->has('limit') ? $request->limit : 10;




        $CorePosHistory = CorePosHistory::where("company_id", $companyId)
            ->applyFilters($request->only([
                'user_id',
                'customer_id',
                'action',
                'item_id',
                'action',
                'invoice_number',
                'document_number',
                'from_date',
                'to_date',
                'orderBy' ,
                'orderByField',
            ]))
            ->paginateData($limit);

        return response()->json([
            'success' => true,
            'data' => $CorePosHistory,
            'coreposcount' => CorePosHistory::count(),
            'message' => 'Corepos history',
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
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
}
