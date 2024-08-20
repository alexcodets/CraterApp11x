<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PbxExtensionsRequest;
use Crater\Models\LogsDev;
// models
use Crater\Models\PbxExtensions;
use Illuminate\Http\Request;

class PbxExtensionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\PbxExtensionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PbxExtensionsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxExtensionsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        /* $company_id = 1;
        $user_id = 1; */
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;
        // $data['minutes_increments'] = intval(preg_replace('/[^0-9]/', '', $data['minutes_increments'])); // extraer numeros
        // $data['pbx_server_id'] = $request->input('emergency_services_rate_value');

        $resPbxExt = PbxExtensions::create($data);
        // $pbx_package_id = $resPbxPackages->id;

        if (! $resPbxExt) {
            return response()->json([
                'pbxExtension' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxExtension' => $resPbxExt,
        ], "message" => "PbxExtensionsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxExtensionsController store");
        /////////////////////////////////////////

        return response()->json([
            'pbxExtension' => $resPbxExt,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $extName
     * @return \Illuminate\Http\Response
     */
    public function show($extName, PbxExtensionsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxExtensionController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resPbxEXT = PbxExtensions::where('ext_name', '=', $extName)->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxEXT' => $resPbxEXT,
        ], "message" => "PbxExtensionController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxExtensionController show");
        /////////////////////////////////////////

        return response()->json([
            'pbxEXT' => $resPbxEXT,
            'message' => 'Pbx Extension succesfully',
            'success' => true
        ]);
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
