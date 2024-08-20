<?php

namespace Crater\Http\Controllers;

use Crater\Models\customAppRate;
use Illuminate\Http\Request;

class CustomAppRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // con paginate de 10 en 10
        // orderByField
        // orderBy
        // page
        // search

        $customAppRates = customAppRate::where(function ($query) use ($request) {
            if ($request->has('name')) {
                $query->where('name', 'like', '%'.$request->name.'%');
            }
        })->orderBy(
            $request->orderByField ? $request->orderByField : 'created_at',
            $request->orderBy ? $request->orderBy : 'desc'
        )
            ->paginate($request->limit ? $request->limit : 10);

        return response()->json([
            'customAppRates' => $customAppRates,
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
        $customAppRate = customAppRate::create($request->all());

        return response()->json([
            'customAppRate' => $customAppRate,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\customAppRate  $customAppRate
     * @return \Illuminate\Http\Response
     */
    public function show(customAppRate $customAppRate)
    {
        return response()->json([
            'customAppRate' => $customAppRate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\customAppRate  $customAppRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customAppRate $customAppRate)
    {
        $customAppRate->update($request->all());

        return response()->json([
            'customAppRate' => $customAppRate,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\customAppRate  $customAppRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(customAppRate $customAppRate)
    {
        // validar si esta siendo usado por pbx_package
        $package = $customAppRate->packages()->first();
        if ($package) {
            return response()->json([
                'message' => 'This customAppRate is being used by a package',
            ], 422);
        }

        $customAppRate->delete();

        // success response
        return response()->json([
            'message' => 'Successfully deleted',
        ]);
    }

    // packageAssociateCustomAppRate
    public function packageAssociateCustomAppRate(Request $request)
    {
        $customAppRate = customAppRate::find($request->customAppRateId);
        $packages = $customAppRate->packages()->paginate(10);

        return response()->json([
            'packages' => $packages,
        ]);
    }
}
