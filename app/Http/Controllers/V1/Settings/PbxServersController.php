<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Controllers\V1\CorePBX\PbxServerCheckConnectionController;
use Crater\Http\Requests\PbxServersRequest;
// models
use Crater\Models\LogsDev;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServers;
use Illuminate\Http\Request;
// Services

use Log;

class PbxServersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        /* $resPbxServers = PbxServers::all(); */
        $limit = $request->limit ?? 10;
        $orderBy = $request->has('orderBy') ? $request["orderBy"] : 'desc';
        $orderByField = $request->has('orderByField') ? $request["orderByField"] : 'created_at';


        $resPbxServers = PbxServers::whereCompany($request->header('company'))
                        ->applyFilters($request->only(['hostname', 'search', 'status']))
                        ->orderBy($orderByField, $orderBy)
                        ->paginate($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServers' => $resPbxServers,
        ], "message" => "PbxServersController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController index");
        /////////////////////////////////////////

        return response()->json([
            'pbxServers' => $resPbxServers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Crater\Http\Requests\PbxServersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PbxServersRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;

        $resPbxServers = PbxServers::create($data);

        $checkServerConnection = new PbxServerCheckConnectionController();
        $checkServerConnection->__invoke($resPbxServers);

        foreach ($request->cdrStatus as $key => $value) {
            $resPbxServers->cdrStatus()->create([
                'pbx_servers_id' => $resPbxServers->id,
                'status' => $value,
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServers' => $resPbxServers,
        ], "message" => "PbxServersController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController store");
        /////////////////////////////////////////

        return response()->json([
            'pbxServers' => $resPbxServers,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  \Crater\Http\Requests\PbxServersRequest  $request
     * @param  \Crater\Models\PbxServers  $PbxServers
     * @return \Illuminate\Http\Response
     */
    public function show($id, PbxServersRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $resPbxServer = PbxServers::find($id);
        // consultar json countries
        $resPbxServer = PbxServers::with('cdrStatus:id,pbx_servers_id,status')->where('pbx_servers.id', $id)
        ->leftJoin('countries', 'countries.id', 'pbx_servers.country_id')
        // ->leftJoin('states', 'states.id', 'providers.state_id')
        // ->select('pbx_servers.*', 'countries.id as country_id', 'countries.name as country', '', '')
        ->select('pbx_servers.*', 'countries.code as country_code', 'countries.name as country_name', 'countries.phonecode as country_phonecode')
        ->first();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServers' => $resPbxServer,
        ], "message" => "PbxServersController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController show");
        /////////////////////////////////////////

        return response()->json([
            'pbxServer' => $resPbxServer,
            'message' => 'PBX Server',
            'success' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PbxServers  $PbxServers
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PbxServersRequest $request, $id, PbxServers $PbxServers)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $data = $request->validated();
        $PbxServer = PbxServers::find($id);
        $PbxServer->update($request->validated());

        $PbxServer->cdrStatus()->delete();
        foreach ($request->cdrStatus as $key => $value) {
            $PbxServer->cdrStatus()->create([
                'pbx_servers_id' => $PbxServer->id,
                'status' => $value,
            ]);
        }
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServers' => $PbxServer,
        ], "message" => "PbxServersController put"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController update");
        /////////////////////////////////////////

        return response()->json([
            'pbxServers' => $PbxServer,
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Crater\Models\PbxServers  $PbxServers
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, PbxServers $PbxServers, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $PbxServer = PbxServers::find($id);
        // validar que no este relacionado con la tabla pbx_packages con el campo pbx_server_id
        $pbxPackagesCount = PbxPackages::where('pbx_server_id', $id)->whereNull('deleted_at')->count();
        if ($pbxPackagesCount > 0) {
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => false, "response" => [], "message" => "PbxServersController delete"];
            LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController delete");

            /////////////////////////////////////////
            return response()->json([
                'message' => 'PBX Server is related to PBX Package',
                'success' => false
            ], 422);
        } else {
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => [], "message" => "PbxServersController delete"];
            LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController delete");
            /////////////////////////////////////////

            $PbxServer->deleted_at = Carbon::now();
            $PbxServer->save();

            return response()->json([
                'message' => 'PBX Server deleted',
                'success' => true
            ]);
        }
    }
}
