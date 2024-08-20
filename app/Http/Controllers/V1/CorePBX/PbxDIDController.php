<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PbxDIDRequest;
use Crater\Models\LogsDev;
// models
use Crater\Models\PbxDID;
use Illuminate\Http\Request;

class PbxDIDController extends Controller
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
     * @param  Crater\Http\Requests\PbxDIDRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PbxDIDRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxDIDController", "store");
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

        $resPbxDID = PbxDID::create($data);
        // $pbx_package_id = $resPbxPackages->id;

        if (! $resPbxDID) {
            return response()->json([
                'pbxDID' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxDID' => $resPbxDID,
        ], "message" => "PbxDIDController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxDIDController store");
        /////////////////////////////////////////

        return response()->json([
            'pbxDID' => $resPbxDID,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $didNumber
     * @param  Crater\Http\Requests\PbxDIDRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show($didNumber, PbxDIDRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxDIDController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resPbxDID = PbxDID::where('did_number', '=', $didNumber)->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxDID' => $resPbxDID,
        ], "message" => "PbxDIDController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxDIDController show");
        /////////////////////////////////////////

        return response()->json([
            'pbxDID' => $resPbxDID,
            'message' => 'Pbx DID succesfully',
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
