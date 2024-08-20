<?php

namespace Crater\Http\Controllers\V1;

use Auth;
use Crater\Http\Requests\AuxVaultSettingRequest;
use Crater\Models\AuxVaultSetting;
use Illuminate\Http\Request;
use Log;

class AuxVaultSettingController
{
    public function index(Request $request)
    {

        $limit = $request->input('limit', 10);
        $auxVaultSetting = AuxVaultSetting::latest()
        ->paginateData($limit);

        //return response()->json(['data' => Auth::user()->auxVaultSettings()]);

        return response()->json([
            'data' => $auxVaultSetting,
            'dataTotalCount' => AuxVaultSetting::all()->count(),
        ]);
    }

    public function store(AuxVaultSettingRequest $request)
    {
        $user = Auth::user();

        array_merge($request->validated(), ['user_id' => $user->id, 'company_id' => $user->company_id]);

        $response = AuxVaultSetting::create($request->validated());
        Log::debug($response);

        //validacion de default
        if($response->default == 1 || $response->default) {
            $this->setDefault($response->id);
        }

        return response()->json([ 'authorization' => $response], 201);


    }

    public function show(int $id)
    {


        $auxVaultSetting = AuxVaultSetting::where('id', $id)->first();

        return response()->json(['data' => $auxVaultSetting]);
    }

    public function update(AuxVaultSettingRequest $request, $id)
    {
        $auxVaultSetting = AuxVaultSetting::where('id', $id)->first();

        $auxVaultSetting->update($request->validated());

        //validacion de default
        if($auxVaultSetting->default == 1 || $auxVaultSetting->default) {
            $this->setDefault($auxVaultSetting->id);
        }

        return response()->json($auxVaultSetting);
    }

    public function delete(Request $request)
    {
        Log::debug($request->input());

        AuxVaultSetting::destroy($request->ids);

        return response()->json([
            'success' => 'Deleted',

        ]);
    }

    public function setDefault(int $id)
    {


        AuxVaultSetting::where('id', '==', $id)->update(['default' => 1]);
        AuxVaultSetting::where('id', '!=', $id)->update(['default' => 0]);

        $obk = AuxVaultSetting::where('id', $id)->first();

        if($obk != null) {

            $obk->default = 1;
            $obk->save();
        }

        $response = [
            'status' => 200,
            'response' => 'Authorize setting update correctly',
            'success' => true
        ];


        return response()->json($response, $response['status']);
    }
}
