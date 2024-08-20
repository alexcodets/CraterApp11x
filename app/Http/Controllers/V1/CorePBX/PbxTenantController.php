<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PbxTenantRequest;
use Crater\Models\LogsDev;
use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxServices;
// models
use Crater\Models\PbxTenant;
use Illuminate\Http\Request;
use Log;

class PbxTenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxTenantController", "index");

        Log::debug('Aqui');
        $pbxServices = PbxServices::with('tenant')->when($request->user()->role == 'customer', function ($query) use ($request) {
            return $query->where('customer_id', $request->user()->id);
        })->get();



        $pbxTenants = $pbxServices->pluck('tenant')->unique('name');
        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'pbxTenants' => $pbxTenants,
        ], "message" => "PbxTenant Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxTenant Index");

        // Fin de registro de log, debe guardarse inmediatamente antes de un return
        // return api
        return response()->json([
            'pbxTenants' => $pbxTenants
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Crater\Http\Requests\PbxTenantRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PbxTenantRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxTenantController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        /* $company_id = 1;
        $user_id = 1; */
        //Log::debug("detaille");
        //Log::debug($data['details']);
        $var = $data['details'];
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;

        if (isset($data['details']['tenant_id'])) {
            $data['code'] = $data['details']['tenant_id'];
            $data['tenantid'] = $data['details']['tenant_id'];
        } else {
            $data['code'] = $data['details']['code'];
            $data['tenantid'] = $data['details']['tenantid'];
            $data['pbx_server_id'] = $data['pbx_server_id'];
        }

        $resPbxTenant = PbxTenant::create($data);
        // $profile_did_id = $resProfileDID->id;

        if (! $resPbxTenant) {
            return response()->json([
                'pbxTenant' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxTenant' => $resPbxTenant,
        ], "message" => "PbxTenantController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxTenantController store");
        /////////////////////////////////////////

        return response()->json([
            'pbxTenant' => $resPbxTenant,
            'message' => '¡Registro Exitoso!',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $tenantId
     * @param  \Crater\Http\Requests\PbxTenantRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show($tenantId, PbxTenantRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxTenantController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $resPbxTenant = PbxTenant::find($id);
        $resPbxTenant = PbxTenant::where('tenantid', '=', $tenantId)->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxTenant' => $resPbxTenant,
        ], "message" => "PbxTenantController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxTenantController show");
        /////////////////////////////////////////

        return response()->json([
            'pbxTenant' => $resPbxTenant,
            'message' => 'Pbx Tenant succesfully',
            'success' => true,
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

    public function tenantsAll(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxTenantController", "tenantsAll");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $limit = $request->get('limit', 10);

        //Log::debug( $orderByField);
        //Log::debug( $orderBy);
        $tenants = PbxCdrTenant::applyFilters($request->only([
                'orderByField',
                'orderBy',
            ]))->PaginateData($limit);

        foreach ($tenants as $tenant) {
            $tenant->tenant = PbxTenant::where('code', '=', $tenant->tenantid)->select(['id', 'name'])->first();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'tenants' => $tenants,
        ], "message" => "PbxTenantController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxTenantController tenantsAll");

        /////////////////////////////////////////
        return response()->json([
            'tenants' => $tenants,
            'message' => 'Pbx Tenants succesfully',
            'success' => true,
        ]);

    }
}
