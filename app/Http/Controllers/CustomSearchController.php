<?php

namespace Crater\Http\Controllers;

use Crater\Models\CustomSearch;
use Crater\Models\LogsDev;
use Crater\Models\PbxCdrTenant;
use Crater\Models\pbxExtensionCustomSearch;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxTenant;
use DB;
use Illuminate\Http\Request;

class CustomSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "index");
        $limit = $request->input('limit') ?? 10;
        $customSearches = CustomSearch::with(['pbxTenant', 'pbxExtension', 'createUser:id,role,role2,name,customcode'])
            // ->orderBy('created_at', 'desc')
            ->when($request->user()->role == 'customer', function ($query) use ($request) {
                return $query->where('created_id', $request->user()->id);
            })
            ->where('company_id', $request->user()->company_id)
            ->applyFilters($request->only([
                'orderByField',
                'orderBy',
            ]))
            ->paginate($limit);

        $res = ["success" => true, "response" => ["datamesage" => [
            'customSearches' => $customSearches,
        ], "message" => "Custom search Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search Index");

        return response()->json([
            'customSearches' => $customSearches,
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
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "store");
        ////////////////

        $company_id = $request->header('company');
        $created_id = Auth()->user()->id;

        $customSearch = CustomSearch::create(
            $request->except(['company_id', 'created_id'])
                + [
                    'company_id' => $company_id,
                    'created_id' => $created_id,
                ]
        );

        $ids = collect($request['extensionAdded'])->pluck('id')->toArray();
        $customSearch->pbxExtension()->sync($ids);
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customSearch' => $customSearch,
        ], "message" => "Custom search created"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search created");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'customSearch' => $customSearch,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\CustomSearch  $customSearch
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, CustomSearch $customSearch)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "show");
        ////////////////

        $customSearch = CustomSearch::with('pbxTenant', 'pbxExtension')->find($customSearch->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customSearch' => $customSearch,
        ], "message" => "Custom search show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'customSearch' => $customSearch,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\CustomSearch  $customSearch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomSearch $customSearch)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "update");
        ////////////////

        $customSearch->update($request->all());

        $ids = collect($request['extensionAdded'])->pluck('id')->toArray();

        $customSearch->pbxExtension()->sync($ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customSearch' => $customSearch,
        ], "message" => "Custom search updated"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search updated");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'customSearch' => $customSearch,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\CustomSearch  $customSearch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CustomSearch $customSearch)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "destroy");
        ////////////////

        $customSearch->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customSearch' => $customSearch,
        ], "message" => "Custom search deleted"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search deleted");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'message' => 'Custom search deleted',
            'success' => true,
        ]);
    }

    // pbxTenants
    public function pbxTenants(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "pbxTenants");
        ////////////////

        $response = [];

        $tenants = PbxCdrTenant::all();
        if ($request->user()->role !== 'customer') {
            $pbxTenants = PbxTenant::select('id', 'name', 'code', 'tenantid', 'pbx_server_id', 'status')
                ->get()
                ->unique('name', 'code', 'pbx_server_id');
        } else {
            // consultar todos los pbx_services del customer y extraer los pbx_tenants
            $pbxTenants = PbxServices::with(['tenant'])->where('customer_id', $request->user()->id)->get()
                ->pluck('tenant')->unique('name', 'code', 'pbx_server_id')->values();
        }

        foreach ($pbxTenants as $pbxTenant) {
            $tenant = $tenants->where('tenantid', $pbxTenant->tenantid)->first();
            if ($tenant && $tenant->status == 1) {
                $tenant = PbxTenant::where('tenantid', $pbxTenant->tenantid)->select('id', 'name', 'code', 'tenantid', 'pbx_server_id', 'status')->first();
                array_push($response, $tenant);
            }
        }
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'pbxTenants' => $response,
        ], "message" => "Custom search pbxTenants"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search pbxTenants");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'data' => $response,
            'message' => 'Custom search pbxTenants'
        ]);
    }

    // pbxTenants
    public function pbxTenantsservice(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "pbxTenantsservice");
        ////////////////

        $response = [];

        $tenants = PbxCdrTenant::all();
        if ($request->user()->role !== 'customer') {
            $pbxTenants = PbxTenant::select('id', 'name', 'code', 'tenantid', 'pbx_server_id', 'status')
                ->get()
                ->unique('name', 'code', 'pbx_server_id');
        } else {
            // consultar todos los pbx_services del customer y extraer los pbx_tenants
            $pbxTenants = PbxServices::with(['tenant'])->where('customer_id', $request->user()->id)->get()
                ->pluck('tenant')->unique('name', 'code', 'pbx_server_id')->values();
        }

        foreach ($pbxTenants as $pbxTenant) {
            $tenant = $tenants->where('tenantid', $pbxTenant->tenantid)->first();
            if ($tenant) {
                $tenant = PbxTenant::where('tenantid', $pbxTenant->tenantid)->select('id', 'name', 'code', 'tenantid', 'pbx_server_id', 'status')->first();
                array_push($response, $tenant);
            }
        }
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'pbxTenants' => $response,
        ], "message" => "Custom search pbxTenants"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search pbxTenants");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'data' => $response,
            'message' => 'Custom search pbxTenants'
        ]);
    }

    // pbxExtensions
    public function pbxExtensions(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "pbxExtensions");
        ////////////////

        $tenant_ids = [];
        $idsPbxTenant = PbxTenant::select('id')
            ->where('name', $request->name)
            ->where('code', $request->code)
            ->where('pbx_server_id', $request->pbx_server_id)
            ->get()->pluck('id')->toArray();

        if ($request->user()->role !== 'customer') {
            $pbxExtensions = PbxExtensions::where('pbx_tenant_id', $request->code)->where('pbx_server_id', $request->pbx_server_id)->get();
        } else {
            // consultar todos los pbx_services del customer y extraer los pbx_tenants
            $pbxExtensions = PbxServices::with(['pbxExtensions'])
                ->where('customer_id', $request->user()->id)
                ->whereIn('pbx_tenant_id', $idsPbxTenant)
                ->get()
                ->pluck('pbxExtensions')->flatten();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'pbxExtensions' => $pbxExtensions,
        ], "message" => "Custom search pbxExtensions"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Custom search pbxExtensions");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'data' => $pbxExtensions,
            'message' => 'Custom search pbxExtensions',
        ]);
    }

    public function reportCDR(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "reportCDR");
        ////////////////

        $idsExtensions = collect($request['extension'])->pluck('pbx_extension_id')->toArray();
        // 'service.pbxServiceExtensions.departments.department' // para obtener el departamento de las extensiones
        $pbxExtensions = PbxServicesExtensions::with(['service.pbxPackage', 'service.pbxServiceExtensions', 'service.pbxServiceExtensions.departments.department'])->whereIn('pbx_extension_id', $idsExtensions)->get();
        // return $pbxExtensions;
        $pbxServices = $pbxExtensions->pluck('service')->unique('id')->values()->filter(function ($pbxService) use ($request) {
            if ($request['includeServicesSuspended']) {
                return $pbxService->status === 'A' || $pbxService->status === 'S';
            } else {
                return $pbxService->status == 'A';
            }
        });
        $extensionesDeparments = [];
        // return $pbxServices;
        $dataSearchCdrs = $pbxServices->map(function ($pbxService) {
            return [
                'pbx_server_id' => $pbxService->pbxPackage['pbx_server_id'],
                'pbx_tenant_id' => $pbxService->pbx_tenant_id,
            ];
        });
        // return $dataSearchCdrs;

        // return $pbxServices;

        $reportCdrsData = [];

        // Hora timestamp de inicio de la consulta
        $start_date = strtotime($request['startDate']);
        $end_date = strtotime($request['endDate']);

        foreach ($dataSearchCdrs as $dataSearchCdr) {
            // saber si tabla existe
            $tableExists = DB::select("SHOW TABLES LIKE 'pbx_cdrs_{$dataSearchCdr["pbx_tenant_id"]}_{$dataSearchCdr["pbx_server_id"]}'");
            // si existe consulta los datos
            if (count($tableExists) > 0) {
                $cdr = DB::table("pbx_cdrs_{$dataSearchCdr['pbx_tenant_id']}_{$dataSearchCdr['pbx_server_id']}")
                    ->whereBetween('start_date', [$start_date, $end_date])
                    ->whereIn('pbx_extension_id', $idsExtensions)->get();

                // relacionar con la extension
                $cdr = $cdr->map(function ($cdr) use ($pbxExtensions) {
                    $cdr->extension = PbxExtensions::select('id', 'name')->find($cdr->pbx_extension_id);
                    $cdr->department = pbxExtensionCustomSearch::with(['department'])->where('pbx_extension_id', $cdr->pbx_extension_id)->get()->pluck('department');

                    // $cdr->service = $pbxExtensions->where('pbx_extension_id', $cdr->pbx_extension_id)->first()->service;
                    return $cdr;
                });
                array_push($reportCdrsData, $cdr);
            }
        }

        $reportCdrsData = collect($reportCdrsData)->flatten()->values();

        return response()->json([
            'data' => $reportCdrsData,
            'message' => 'Custom search reportCDR',
        ]);
    }
}
