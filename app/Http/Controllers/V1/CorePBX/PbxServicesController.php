<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PbxServicesDIDRequest;
use Crater\Http\Requests\PbxServicesExtRequest;
use Crater\Http\Requests\PbxServicesRequest;
use Crater\Models\CompanySetting;
//Traits
use Crater\Models\Invoice;
use Crater\Models\InvoiceDid;
use Crater\Models\InvoiceExtension;
use Crater\Models\Item;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxAdditionalCharge;
use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesAppRate;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxServicesItems;
use Crater\Models\PbxServicesTaxTypes;
use Crater\Models\PbxServicesTaxTypesCdr;
use Crater\Models\PbxTenant;
use Crater\Models\PrefixRateGroups;
use Crater\Models\ProfileDID;
use Crater\Models\ProfileExtensions;
use Crater\Models\Tax;
use Crater\Models\TaxType;
use Crater\Models\TollFreeCustomDidGroup;
use Crater\Models\User;
use Crater\Pbxware\PbxWareApi;
use Crater\Traits\SendEmailsTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
// class
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;

class PbxServicesController extends Controller
{
    use SendEmailsTrait;

    /**
     * Display a listing of the resource.
     * @param idPbxPackage
     * @return \Illuminate\Http\Response
     */
    public function listTenant(Request $request, $idPbxPackage)
    {
        $resTenantsFiltered = [];
        $idsToExclude = [];
        $pbxApi = new PbxWareApi();
        $excluded = false;

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "listTenant");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resTenantsApi = $pbxApi->getTenantList($idPbxPackage);
        //Log::debug(json_encode($resTenantsApi));
        if ($resTenantsApi === null) {
            return json_encode([
                "error" => true,
                "message" => "server out of service or down",
            ]);
        } elseif (! is_object($resTenantsApi)) {
            return json_encode([
                "error" => true,
                "message" => json_decode($resTenantsApi)->message,
            ]);
        }
        //obtener id tenants a excluir del listado
        $resTenant = PbxServices::leftjoin('pbx_packages', 'pbx_packages.id', '=', 'pbx_services.pbx_package_id')
            ->select('pbx_services.pbx_tenant_id as id')

            ->where(function ($query) {
                $query->orwhere('pbx_services.status', '=', "A")
                    ->orwhere('pbx_services.status', '=', "P")
                    ->orwhere('pbx_services.status', '=', "S");
            })

            ->where(function ($query) {
                $query->where('pbx_packages.template_extension_id', '=', 0)
                    ->Where('pbx_packages.template_did_id', '=', 0)
                    ->orwhere('pbx_packages.all_cdrs', '=', 1);
            })

            ->get();

        foreach ($resTenant as $key => $value) {
            array_push($idsToExclude, $value->id);
        }
        // get tenant to exlude array

        $resTenantsBd = PbxTenant::all()->whereIn('id', $idsToExclude);
        $cont = 0;
        if ($resTenantsApi != null && $resTenantsApi != "") {
            foreach ($resTenantsApi as $key => $valueApi) {
                // comparar tenant con array de tenant de bd a excluir
                foreach ($resTenantsBd as $key2 => $valueBd) {
                    // var_dump($valueBd->details);
                    /*  if (gettype($valueApi) === 'string'){
                    $excluded = true;
                    } */
                    //Log::debug(gettype($valueApi));
                    if (gettype($valueApi) === 'string' || ($valueApi->tenantcode == $valueBd->details['code'] && $valueApi->name == $valueBd->details['name'])) {
                        $excluded = true;

                        break;
                    } else {
                        $excluded = false;
                    }
                }

                if ($excluded == false) {
                    // push to filtered data
                    $resTenantsFiltered[$key] = $valueApi;
                    $cont++;
                    // array_push($resTenantsFiltered, $valueApi);
                }
            }
        }

        //Log::debug(json_encode($resTenantsFiltered));

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'tenantList' => $resTenantsFiltered,
        ], "message" => "PbxServicesController listTenant"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController listTenant");
        /////////////////////////////////////////////////////////////////////////////

        // success
        return response()->json([
            'tenantList' => $resTenantsFiltered,
        ]);
    }

    /**
     * Display a listing of the Extensions by tenant / server from pbxware API.
     * @param idPbxPackage
     * @return \Illuminate\Http\Response
     */
    public function listExtByTenant(Request $request)
    {
        // params
        $idPbxPackage = $request->pbx_package_id;
        $idTenant = $request->pbx_tenant_id;
        $idServer = $request->pbx_server_id;
        $tenantCode = $request->tenant_code;

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "listExtByTenant");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $pbxApi = new PbxWareApi();
        $resExt = $pbxApi->getExtListByTenant($idPbxPackage, $idTenant);
        $filteredExt = [];

        // Si en el $resExt la respuesta es null se debe validar, si cumple se debe enviar un mensaje de error

        // validar que resExt  contenga un key "error"
        // validar en ambas respuestas se debe mostrar la respuesta con el Toat y en el alert de la tabla en rojo FALLIDO

        if (empty($resExt)) {
            return response()->json([
                "success" => true,
                'ExtensionByTenantList' => $filteredExt,
                "message" => "Server OK - No Extensions Available"]);
        }

        if (array_key_exists("error", $resExt)) {
            return response()->json([
                'message' => $resExt,
                "status" => false,
                "error" => true,
            ]);
        }
        if (array_key_exists("status", $resExt)) {
            return response()->json([
                'ExtensionByTenantList' => $resExt,
                "status" => true,
            ]);
        }

        // validar que resExt sea un array
        foreach ($resExt as $idExt => $ext) {

            if ($ext != "No extensions available.") {
                $ext->pbxext_id = $idExt;

                if (! is_string($ext) || is_object($ext)) {
                    $ext->only_api = false;
                    $ext->db_available = false;
                    //Log::debug("---------- full extensions");
                    //Log::debug(json_encode($ext));
                    $resPbxExtId = PbxExtensions::select('id')
                        ->where('pbxext_id', '=', $ext->pbxext_id)
                        ->where('pbx_server_id', '=', $idServer)
                        ->where('pbx_tenant_code', '=', $tenantCode)
                        ->get();

                    foreach ($resPbxExtId as $key => $extId) {
                        $resExtToExclude = PbxServicesExtensions::all()->where('pbx_extension_id', $extId->id)->whereNull("deleted_at");
                        if (count($resExtToExclude) === 0) {
                            $ext->db_available = true;
                            $filteredExt[$idExt] = $ext;
                        }
                    }

                    if (count($resPbxExtId) === 0) {
                        $ext->only_api = true;
                        $filteredExt[$idExt] = $ext;
                    }

                }
            }
        }

        //Log::debug("---------- full extensions filtered");
        //Log::debug(json_encode($filteredExt));

        // VALIDAR si la variable filterExt se encuentra vacia, si se cumple no debe salir mensaje de error, debe mostrar como mensaje
        // Server OK- Extension already in use
        // Server OK- DIDs already in use

        if (empty($filteredExt)) {
            return response()->json([
                "success" => true,
                'ExtensionByTenantList' => $filteredExt,
                "message" => "Server OK - Extensions Aleady in use"]);
        }
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true,
            "response" => [
                'ExtensionByTenantList' => $filteredExt,
            ], "message" => "PbxServicesController listExtByTenant"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController listExtByTenant");
        ///////////////////////////////////////////////////////////////////////////////////

        // success
        return response()->json([
            'ExtensionByTenantList' => $filteredExt,
        ]);
    }

    /**
     * Display a listing of the DID by tenant / server from pbxware API.
     * @param idPbxPackage
     * @return \Illuminate\Http\Response
     */
    public function listDIDByTenant(Request $request)
    {
        $idPbxPackage = $request->pbx_package_id;
        $idTenant = $request->pbx_tenant_id;
        $idServer = $request->pbx_server_id;
        $tenantCode = $request->tenant_code;
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "listDIDByTenant");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $pbxApi = new PbxWareApi();
        $resDID = $pbxApi->getDIDListByTenant($idPbxPackage, $idTenant);
        $filteredDID = [];

        if (empty($resDID)) {
            return response()->json([
                "success" => true,
                'DIDByTenantList' => $filteredDID,
                "message" => "Server OK - No Extension Available"]);
        }

        // validar que resDID  contenga un key "error"
        if (array_key_exists("error", $resDID)) {
            return response()->json([
                'message' => $resDID,
                "status" => false,
                "error" => true,
            ]);
        }
        if (array_key_exists("status", $resDID)) {
            return response()->json([
                'DIDByTenantList' => $resDID,
                "status" => true,
            ]);
        }

        foreach ($resDID as $idDid => $did) {

            //Log::debug('-----type-------');
            //Log::debug(json_encode($did));
            //Log::debug(gettype($did));
            if (gettype($did) === 'object') {
                $did->only_api = false;
                $did->db_available = false;
                $did->pbxdid_id = $idDid;
                $resPbxDidId = PbxDID::select('id')
                    ->where('pbxdid_id', '=', $did->pbxdid_id)
                    ->where('server', '=', $did->server)
                    ->where('pbx_server_id', '=', $idServer)
                    ->where('pbx_tenant_code', '=', $tenantCode)
                    ->get();
                foreach ($resPbxDidId as $key => $didBd) {
                    // try to find did on pbx_service_did
                    $resPbxServiceDid = PbxServicesDID::select('*')->where('pbx_did_id', $didBd->id)->whereNull("deleted_at")->get();

                    if (count($resPbxServiceDid) === 0) {
                        // $did->only_api = true;
                        $did->db_available = true;
                        $filteredDID[$idDid] = $did;
                    }
                }

                if (count($resPbxDidId) === 0) {
                    $did->only_api = true;
                    $filteredDID[$idDid] = $did;
                }

            }
        }

        if (empty($filteredDID)) {
            return response()->json([
                "success" => true,
                'DIDByTenantList' => $filteredDID,
                "message" => "Server OK - Did's Aleady in use"]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true,
            'DIDByTenantList' => $filteredDID,
            "message" => "PbxServicesController listDIDByTenant"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController listDIDByTenant");
        ///////////////////////////////////////////////////////////////////////////////////

        // validate tenant list
        /* if ($resTenants->status ) {
        return response()->json([
        'tenantList' => [],
        git     'message' => $resTenants->message,
        'status' => $resTenants->status,
        ]);
        } */

        // success
        return response()->json([
            'DIDByTenantList' => $filteredDID,
        ]);
    }

    /**
     * get the renewal date value from date_begin
     */
    public function getRenewalDate($dateBegin, $term)
    {
        $today = Carbon::now();
        $date = Carbon::parse($dateBegin);
        $renewal_date = null;
        if ($date < $today) {
            $date = $today;
        }

        switch ($term) {
            case 'daily':
                $renewal_date = $date->addDay();

                break;
            case 'weekly':
                $renewal_date = $date->addWeek();

                break;
            case 'monthly':
                $renewal_date = $date->addMonth();

                break;
            case 'bimonthly':
                $renewal_date = $date->addMonths(2);

                break;
            case 'quarterly':
                $renewal_date = $date->addQuarter();

                break;
            case 'biannual':
                $renewal_date = $date->addMonths(6);

                break;
            case 'yearly':
                $renewal_date = $date->addYear();

                break;
        }

        return $renewal_date->format('Y-m-d');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $resPbxServices = PbxServices::all();
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'PbxServices' => $resPbxServices,
        ], "message" => "PbxServicesController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController index");
        /////////////////////////////////////////

        return response()->json([
            'PbxServices' => $resPbxServices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PbxServicesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PbxServicesRequest $request)
    {
        try {

            //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
            $time = microtime(true);
            $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "store");
            //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

            $data = $request->validated();
            Log::debug("440");
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->id;
            $data['company_id'] = $company_id;
            $data['creator_id'] = $user_id;
            $data['date_from'] = $request->input('discount_start_date');
            $data['date_to'] = $request->input('discount_end_date');
            $data['renewal_date'] = $this->getRenewalDate($request->date_begin, $request->term);
            $data['date_prev'] = $request->date_begin;
            $data['discount_term_type'] = $request->input('time_period_value');
            $data['time_period'] = $request->time_period;
            $data['time_period_value'] = $request->time_period_type;
            $data['discount_val'] = $request->discount_val;
            $data['pbxpackages_price'] = $request->pbxpackages_price * 100;
            Log::debug(452);

            $data['apply_tax_type'] = $request->tax_type['value'];
            Log::debug(455);
            Log::debug(456, ['error' => $request->all()]);
            if (! empty($request->address['id'])) {
                $data['addresses_id'] = $request->address['id'] ? $request->address['id'] : null;
            }
            Log::debug(456);
            if ($request->taxCdr && $request->taxCdr['name'] != 'none') {
                Log::debug(459);
                $data['tax_type_id'] = $request->taxCdr['id'];
            }
            Log::debug(462);
            $data['discount_term_type'] = $request->time_period_value;
            // $data['prefixrate_groups_id']=$request->prefixrate_groups_id;
            /* //Log::debug('------------------------------------');
            //Log::debug($request->input('only_callrating'));
            //Log::debug($data['only_callrating']); */
            $data['inclusive_minutes_seconds_consumed'] = 0;

            $currentDate = Carbon::now();
            //Log::debug($currentDate);

            $discount_deadline = Carbon::parse($request->date_begin);
            //Log::debug($discount_deadline);
            Log::debug(472);
            $resulttime = $discount_deadline->lte($currentDate);
            //Log::debug($resulttime);

            if ($resulttime) {

                $data['status'] = "A";

            } else {

                $data['status'] = "P";

            }

            $resPbxServices = PbxServices::create($data);
            $pbxServices_saved_id = $resPbxServices->id;
            Log::debug(488);
            //agregando prefijo

            if (! $resPbxServices) {
                return response()->json([
                    'pbxService' => null,
                    'message' => 'error',
                    'status' => 406,
                    'success' => false,
                ]);
            }

            // verificar id del servicio guaradado
            if ($pbxServices_saved_id) {
                $tenanApiId = $request->tenant_api_id;
                // validar array de ext y did en el request
                // ext
                if (count($request->extensions) > 0) {
                    // invocar metodo de insert ext
                    $this->addPbxServicesExt(collect($request->extensions), $pbxServices_saved_id, $company_id, $user_id, $tenanApiId, $data['pbx_package_id']);
                }
                // did
                if (count($request->dids) > 0) {
                    // invocar metodo de insert did
                    $this->addPbxServicesDid(collect($request->dids), $pbxServices_saved_id, $company_id, $user_id, $tenanApiId, $data['pbx_package_id']);
                }
                // Items
                if (count($request->items) > 0) {
                    $this->addPbxServicesItems(collect($request->items), $pbxServices_saved_id, $company_id, $user_id, );
                }
                // Packages Tax Types
                if (count($request->taxes) > 0) {
                    $this->addPbxServicesTaxTypes(collect($request->taxes), $pbxServices_saved_id, $company_id);
                }
                Log::debug(522);
                // Packages Tax Types
                if (count($request['taxesCdr']) > 0) {
                    //guarda impuestos cdrs
                    foreach ($request['taxesCdr'] as $taxesCdr) {
                        $tax_base = TaxType::find($taxesCdr['id']);
                        if ($tax_base != null) {
                            $this->addPbxServicesTaxTypesCdrs($tax_base, $pbxServices_saved_id, $company_id);
                        }
                    }
                }
                // Add / update "pbx_services_custom_app_rates"
                if (isset($request['pbx_services_app_rate'])) {
                    if (count($request['pbx_services_app_rate']) > 0) {
                        foreach ($request['pbx_services_app_rate'] as $custom_app) {
                            $customApp = [
                                'id' => $custom_app['id'] ?? null,
                                'app_name' => $custom_app['app_name'],
                                'quantity' => $custom_app['quantity'],
                                'costo' => $custom_app['costo'],
                                'pbx_package_id' => $request['pbx_package_id'],
                                'pbx_service_id' => $pbxServices_saved_id,
                            ];
                            $this->updatePbxServicesCustomApp($customApp);
                        }
                    }
                }
            }
            Log::debug(550);
            // Insert into pbx_services_prefixrate_groups (Grupos de Custom Destination "inbound-Outbound)
            if (array_key_exists("custom_destination_groups", $data)) {
                if (count($request['custom_destination_groups']) > 0) {
                    foreach ($request['custom_destination_groups'] as $group) {
                        DB::table('pbx_services_prefixrate_groups')->insert([
                            'pbx_service_id' => $pbxServices_saved_id,
                            'prefixrate_group_id' => $group['id'],
                            'type' => $group['type'],
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                }

            }
            //
            Log::debug(571);
            //////////// agregar prefijo
            $prefijoob = CompanySetting::where("option", "pbx_services_prefix")->where("company_id", Auth::user()->company_id)->first();
            $prefix = "PBXS";
            if ($prefijoob != null) {
                $prefix = $prefijoob->value;
            }
            // convertir id del pbx service en string
            $cIdPbxService = strval($pbxServices_saved_id);
            // varificar longitud del id del servicio
            $idDigitsCount = strlen($cIdPbxService);
            // switch para evaluar cada caso (cantidad de digitos del id)
            switch ($idDigitsCount) {
                case 1:
                    $prefix .= '-'.'000'.$cIdPbxService;

                    break;
                case 2:
                    $prefix .= '-'.'00'.$cIdPbxService;

                    break;
                case 3:
                    $prefix .= '-'.'0'.$cIdPbxService;

                    break;
                case 3:
                    $prefix .= '-'.$cIdPbxService;

                    break;
            }

            // crear pbx_additional_charges

            $packagePbx = $request->package;

            // extension aditional charge
            if (! empty($packagePbx['profile_extensions_aditional_charges']) && count($request->extensions) > 0) {
                PbxAdditionalCharge::createAdditionalCharge($packagePbx['profile_extensions_aditional_charges'], count($request->extensions), $pbxServices_saved_id);
            }

            // did aditional charge
            if (! empty($packagePbx['profile_did_aditional_charges']) && count($request->dids) > 0) {
                PbxAdditionalCharge::createAdditionalCharge($packagePbx['profile_did_aditional_charges'], count($request->dids), $pbxServices_saved_id);
            }
            Log::debug(611);

            // asignar prefijo correspondiente
            $resPbxServices->pbx_services_number = $prefix;
            $objpbx = PbxPackages::where("id", $resPbxServices->pbx_package_id)->first();
            // $resPbxServices->only_callrating = 0;

            if ($resPbxServices->cap_total != null && $resPbxServices->cap_total != 0) {
                $resPbxServices->inclusive_minutes_seconds_consumed = $resPbxServices->cap_total * 60;
            }

            if ($objpbx != null) {

                if (($objpbx->extensions == 0 || $objpbx->extensions == false) && ($objpbx->did == 0 || $objpbx->did == false)) {
                    $resPbxServices->only_callrating = 1;
                }
            }
            $resPbxServices->allow_customapp = $request->input('allow_customapp') ? 1 : 0;
            $resPbxServices->custom_app_rate_id = $request->input('custom_app_rate_id');
            $resPbxServices->suspension_type = $request->input('suspension_type');
            $resPbxServices->save();
            //

            try {
                $this->sendEmailToCustomer($resPbxServices->customer_id, $company_id, 'create', 'PBX Service Created', $resPbxServices);
            } catch (Exception $e) {
                echo 'Excepción capturada: ', $e->getMessage();
            }
            Log::debug(639);
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => [
                'pbxService' => $resPbxServices,
            ], "message" => "PbxServicesController post"];
            LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController store");

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            return response()->json([
                'pbxService' => $resPbxServices,
                'message' => '¡PBX Service saved successfully!',
                'success' => true,
            ]);

        } catch (\Throwable $th) {
            Log::debug(653);
            Log::debug('error', ['error' => $th]);

            return response()->json([
                'message' => $th,
                'success' => true,
            ]);
        }
    }

    /**
     * add pbx services items
     * @param $items
     * @param $pbx_service_id
     * @param $company_id
     *
     */
    public function addPbxServicesItems($items, $pbx_service_id, $company_id, $user_id)
    {
        $res = [];
        $items->each(function ($item) use ($pbx_service_id, $res, $company_id, $user_id) {
            //Log::debug(" ");
            //Log::debug(" ");
            //Log::debug("item::  ");
            //Log::debug($item);
            if ((array_key_exists('items_id', $item) || array_key_exists('id', $item)) && array_key_exists('item_id', $item) == false) {
                //if ($item['items_id'] || $item['id'] ) {
                //Log::debug("entro 1");
                $iditem = array_key_exists('items_id', $item) ? $item['items_id'] : $item['id'];
                if (array_key_exists('item_number', $item)) {
                    $val = Item::where("item_number", $item["item_number"])->first();
                    if ($val != null) {
                        $iditem = $val->id;
                    }
                }

                $newPbxServicesItems = new PbxServicesItems();
                $newPbxServicesItems->pbx_services_id = $pbx_service_id;
                $newPbxServicesItems->creator_id = $user_id;
                $newPbxServicesItems->company_id = $company_id;
                $newPbxServicesItems->item_group_id = isset($item['item_group_id']) ? $item['item_group_id'] : 0;
                $newPbxServicesItems->items_id = $iditem;
                $newPbxServicesItems->discount_type = array_key_exists('discount_type', $item) ? $item['discount_type'] : $item['pivot']['discount_type'];
                $newPbxServicesItems->quantity = $item['quantity'];
                $newPbxServicesItems->discount = array_key_exists('discount', $item) ? $item['discount'] : $item['pivot']['discount'];
                $newPbxServicesItems->discount_val = array_key_exists('discount_val', $item) ? $item['discount_val'] : $item['pivot']['discount_val'];
                $newPbxServicesItems->price = $item['price'];
                $newPbxServicesItems->tax = array_key_exists('tax', $item) ? $item['tax'] : $item['pivot']['tax'];
                $newPbxServicesItems->total = $item['price'] * $item['quantity'];
                $newPbxServicesItems->description = $item['description'];
                $newPbxServicesItems->name = $item['name'];

                $newPbxServicesItems->end_period_number = $item['end_period_number'];
                $newPbxServicesItems->end_period_act = $item['end_period_act'];
                // $newPbxServicesItems->status = $item['status'];
                $res[] = $newPbxServicesItems->save();

                if (array_key_exists('taxes', $item) && count($item['taxes']) > 0) {
                    foreach ($item['taxes'] as $tax) {
                        if (gettype($tax['amount']) !== "NULL") {
                            $taxeSave = [
                                'company_id' => $company_id,
                                'creator_id' => Auth::id(),
                                'tax_type_id' => $tax['tax_type_id'],
                                'amount' => $tax['amount'],
                                'percent' => $tax['percent'],
                                'name' => $tax['name'],
                                'compound_tax' => $tax['compound_tax'],
                                'pbx_services_items_id' => $newPbxServicesItems->id,
                            ];
                            $newPbxServicesItems->taxes()->create($taxeSave);
                        }
                    }
                }
            } else {

                if (array_key_exists('item_id', $item)) {
                    if (true) {
                        //Log::debug("entro 21");

                        $iditem = $item['item_id'];
                        if (array_key_exists('item_number', $item)) {
                            $val = Item::where("item_number", $item["item_number"])->first();
                            if ($val != null) {
                                $iditem = $val->id;
                            }
                        }

                        $newPbxServicesItems = new PbxServicesItems();
                        $newPbxServicesItems->pbx_services_id = $pbx_service_id;
                        $newPbxServicesItems->creator_id = $user_id;
                        $newPbxServicesItems->company_id = $company_id;
                        $newPbxServicesItems->item_group_id = isset($item['item_group_id']) ? $item['item_group_id'] : 0;
                        $newPbxServicesItems->items_id = $iditem;
                        $newPbxServicesItems->discount_type = isset($item['discount_type']) ? $item['discount_type'] : 0;
                        $newPbxServicesItems->quantity = $item['quantity'];
                        $newPbxServicesItems->discount = isset($item['discount']) ? $item['discount'] : 0;
                        $newPbxServicesItems->discount_val = isset($item['discount_val']) ? $item['discount_val'] : 0;
                        $newPbxServicesItems->price = $item['price'];
                        $newPbxServicesItems->tax = isset($item['tax']) ? $item['tax'] : 0;
                        $newPbxServicesItems->total = $item['price'] * $item['quantity'];
                        $newPbxServicesItems->description = $item['description'];
                        $newPbxServicesItems->name = $item['name'];
                        // $newPbxServicesItems->status = $item['status'];
                        $res[] = $newPbxServicesItems->save();

                        if (array_key_exists('taxes', $item) && count($item['taxes']) > 0) {
                            foreach ($item['taxes'] as $tax) {
                                if (gettype($tax['amount']) !== "NULL") {
                                    $taxeSave = [
                                        'company_id' => $company_id,
                                        'creator_id' => Auth::id(),
                                        'tax_type_id' => $tax['tax_type_id'],
                                        'amount' => $tax['amount'],
                                        'percent' => $tax['percent'],
                                        'name' => $tax['name'],
                                        'compound_tax' => $tax['compound_tax'],
                                        'pbx_services_items_id' => $newPbxServicesItems->id,
                                    ];
                                    $newPbxServicesItems->taxes()->create($taxeSave);
                                }
                            }
                        }
                    }
                }
            }

        });

        return $res;
    }

    /**
     * add pbx services tax types
     * @param $taxes
     * @param $pbx_service_id
     * @param $company_id
     */
    public function addPbxServicesTaxTypes($taxes, $pbx_service_id, $company_id)
    {
        $res = [];
        $taxes->each(function ($item) use ($pbx_service_id, $res, $company_id) {

            $newPackageTaxTypes = new PbxServicesTaxTypes();
            $newPackageTaxTypes->pbx_services_id = $pbx_service_id;
            $newPackageTaxTypes->company_id = $company_id;
            $newPackageTaxTypes->tax_types_id = $item['tax_types_id'];
            $newPackageTaxTypes->name = $item['name'];
            $newPackageTaxTypes->percent = $item['percent'];
            $newPackageTaxTypes->compound_tax = $item['compound_tax'];
            $newPackageTaxTypes->amount = $item['amount'];
            $res[] = $newPackageTaxTypes->save();
        });

        return $res;
    }

    /**
     * add array pbx services extensions
     * @param $items
     * @param $pbx_service_id
     * @param $company_id
     *
     */
    public function addPbxServicesExt($extensions, $pbx_service_id, $company_id, $user_id, $pbx_tenant_id, $pbx_package_id)
    {
        $res = [];
        $idPbxExt = 0;
        $extensions->each(function ($ext) use ($pbx_service_id, $res, $company_id, $user_id, $pbx_tenant_id, $idPbxExt, $pbx_package_id) {
            if ($ext['id'] != null) {
                if (isset($ext['name'])) {
                    //Log::debug($ext);
                    // validar si existe extensiones en (pbx_ext)
                    $PbxExt = PbxExtensions::select('id')->where('pbxext_id', '=', $ext['pbxext_id'])->where('pbx_server_id', '=', $ext['pbx_server_id'])->where('pbx_tenant_code', '=', $ext['pbx_tenant_code'])->get();
                    //Log::debug($PbxExt);
                    // si no existe, almacenar
                    if (sizeof($PbxExt) <= 0) {
                        // data pbx ext
                        $paramsExt = [];
                        $paramsExt['company_id'] = $company_id;
                        $paramsExt['creator_id'] = $user_id;
                        $paramsExt['pbx_tenant_id'] = $pbx_tenant_id;
                        $paramsExt['name'] = $ext['name'];
                        $paramsExt['email'] = $ext['email'];
                        $paramsExt['status'] = $ext['status'];
                        $paramsExt['api_id'] = $ext['id'];
                        $paramsExt['ext'] = $ext['ext'];
                        $paramsExt['linenum'] = $ext['linenum'];
                        $paramsExt['location'] = $ext['location'];
                        $paramsExt['macaddress'] = $ext['macaddress'];
                        $paramsExt['protocol'] = $ext['protocol'];
                        $paramsExt['ua_fullname'] = $ext['ua_fullname'];
                        $paramsExt['ua_id'] = $ext['ua_id'];
                        $paramsExt['ua_name'] = $ext['ua_name'];
                        $paramsExt['pbxext_id'] = $ext['pbxext_id'];
                        $paramsExt['pbx_server_id'] = $ext['pbx_server_id'];
                        $paramsExt['pbx_tenant_code'] = $ext['pbx_tenant_code'];
                        //
                        $resPbxExt = PbxExtensions::create($paramsExt);
                        $idPbxExt = $resPbxExt->id;

                    } else {
                        $idPbxExt = $PbxExt[0]['id'];
                    }

                    // data pbx services ext
                    $paramsServiceExt = [];
                    $paramsServiceExt['company_id'] = $company_id;
                    $paramsServiceExt['creator_id'] = $user_id;
                    $paramsServiceExt['pbx_service_id'] = $pbx_service_id;
                    $paramsServiceExt['pbx_extension_id'] = $idPbxExt;
                    $paramsServiceExt['price'] = $this->getTemplateExtensions($pbx_package_id) != null ? $this->getTemplateExtensions($pbx_package_id) : null;
                    // guardar exstensiones
                    $res[] = PbxServicesExtensions::create($paramsServiceExt);
                }
            }
        });

        return $res;
    }

    /**
     * get of the price of a template
     *
     * @param [int] $id
     * @return void
     */
    public function getTemplateExtensions($id = null)
    {
        try {

            $price = DB::table('pbx_packages')->join('profile_extensions', 'pbx_packages.template_extension_id', '=', 'profile_extensions.id')
                ->where('pbx_packages.id', $id)->pluck('profile_extensions.rate')->first();

            return $price;
        } catch (Throwable $e) {
            Log::error('Error getTemplateExtensions', ['Error' => $e]);

            return null;
        }
    }

    /**
     * add array pbx services extensions
     * @param $items
     * @param $pbx_service_id
     * @param $company_id
     *
     */
    public function addPbxServicesDid($dids, $pbx_service_id, $company_id, $user_id, $pbx_tenant_id, $pbx_packages_id)
    {
        $res = [];
        $idPbxDid = null;
        //
        $dids->each(function ($did) use ($pbx_service_id, $res, $company_id, $user_id, $pbx_tenant_id, $idPbxDid, $pbx_packages_id) {
            if ($did['id'] != null) {
                // validar si existe did en mysql
                if (isset($did['number'])) {
                    //Log::debug($did);
                    $PbxDid = PbxDID::select('id')->where('number', '=', $did['number'])->where('pbxdid_id', '=', $did['pbxdid_id'])->where('pbx_server_id', '=', $did['pbx_server_id'])->where('pbx_tenant_code', '=', $did['pbx_tenant_code'])->get();

                    //Log::debug($PbxDid);
                    // si no existe, almacenar
                    if (sizeof($PbxDid) <= 0) {
                        // $user_id = Auth::user()->id;

                        // data pbx did
                        $paramsDid = [];
                        $paramsDid['company_id'] = $company_id;
                        $paramsDid['creator_id'] = $user_id;
                        $paramsDid['pbx_tenant_id'] = $pbx_tenant_id;
                        $paramsDid['api_id'] = $did['id'];
                        $paramsDid['number'] = $did['number'];
                        $paramsDid['server'] = $did['server'];
                        $paramsDid['status'] = $did['status'];
                        $paramsDid['trunk'] = $did['trunk'];
                        $paramsDid['type'] = $did['type'];
                        $paramsDid['number2'] = $did['number2'];
                        $paramsDid['ext'] = $did['ext'];
                        $paramsDid['e164'] = $did['e164'];
                        $paramsDid['e164_2'] = $did['e164_2'];
                        $paramsDid['pbxdid_id'] = $did['pbxdid_id'];
                        $paramsDid['pbx_server_id'] = $did['pbx_server_id'];
                        $paramsDid['pbx_tenant_code'] = $did['pbx_tenant_code'];
                        // guardar did
                        $resPbxDid = PbxDID::create($paramsDid);
                        $idPbxDid = $resPbxDid->id;

                    } else {
                        $idPbxDid = $PbxDid[0]['id'];
                    }
                    // data pbx services did
                    $paramsServiceDid = [];
                    $paramsServiceDid['company_id'] = $company_id;
                    $paramsServiceDid['creator_id'] = $user_id;
                    $paramsServiceDid['pbx_service_id'] = $pbx_service_id;
                    $paramsServiceDid['pbx_did_id'] = $idPbxDid;
                    $paramsServiceDid['custom_did_id'] = $did['custom_did_group_id'] != 0 ? $did['custom_did_group_id'] : null;
                    $paramsServiceDid['price'] = $this->getTemplateDID($pbx_packages_id, $paramsServiceDid['custom_did_id']);

                    $paramsServiceDid['name_prefix'] = $did['template_name'];

                    // save service did
                    $res[] = PbxServicesDID::create($paramsServiceDid);

                }
            }
        });

        return $res;
    }

    /**
     * get the price of a template
     *
     * @param [int] $id
     * @return void
     */
    public function getTemplateDID($id, $custom_did_id)
    {
        try {
            if ($custom_did_id == null || $custom_did_id == 0) {

                $result = DB::table('pbx_packages')->join('profile_did', 'pbx_packages.template_did_id', '=', 'profile_did.id')
                    ->where('pbx_packages.id', $id)->pluck('profile_did.did_rate')->first();

                if ($result == null || $result == '') {
                    $price = null;
                } else {
                    $price = $result;
                }
            } else {
                $price = DB::table('profile_did_toll_frees')->where('id', $custom_did_id)->pluck('rate_per_minute')->first();
            }

            return round($price, 2);
        } catch (Throwable $e) {
            Log::error('Error getTemplateExtensions', ['Error' => $e]);

            return null;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PbxServicesDIDRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storePbxServiceDID(PbxServicesDIDRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "storePbxServiceDID");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $data = $request->validated();
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        /*  $company_id = 1;
        $user_id = 1; */
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;
        $resPbxServicesDID = PbxServicesDID::create($data);
        // $profile_did_id = $resProfileDID->id;

        if (! $resPbxServicesDID) {
            return response()->json([
                'pbxServiceDID' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServiceDID' => $resPbxServicesDID,
        ], "message" => "PbxServicesController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController storePbxServiceDID");
        ////////////////////////////////////////////////////////////////////////////

        return response()->json([
            'pbxServiceDID' => $resPbxServicesDID,
            'message' => '¡Registro Exitoso!',
            'success' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PbxServicesExtRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storePbxServiceExtensions(PbxServicesExtRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "storePbxServiceExtensions");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        /* $company_id = 1;
        $user_id = 1; */
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;

        $resPbxServicesEXT = PbxServicesExtensions::create($data);
        // $profile_did_id = $resProfileDID->id;

        if (! $resPbxServicesEXT) {
            return response()->json([
                'pbxServiceEXT' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServiceEXT' => $resPbxServicesEXT,
        ], "message" => "PbxServicesController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController storePbxServiceExtensions");
        ////////////////////////////////////////////////////////////////////////////

        return response()->json([
            'pbxServiceEXT' => $resPbxServicesEXT,
            'message' => '¡Registro Exitoso!',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resources (list did) from a pbxService
     *
     * @param  int  $idPbxService
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showDIDListByService($idPbxService, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "showDIDListByService");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // obtener pbx service
        $resPbxServicesDID = PbxServicesDID::where('pbx_service_id', '=', $idPbxService)->join('profile_did', 'pbx_services_did.pbx_profile_did_id', '=', 'profile_did.id')->get();

        $resPbxDID = [];
        // buscar pbx did asociados al servicio
        foreach ($resPbxServicesDID as $pbxService) {
            $result = PbxDID::find($pbxService->pbx_did_id);
            $result->package_name = $pbxService->name;
            $result->package_did_rate = $pbxService->did_rate;
            array_push($resPbxDID, $result);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'DIDListByPbxService' => $resPbxDID,
        ], "message" => "PbxServicesController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController storePbxServiceExtensions");
        ////////////////////////////////////////////////////////////////////////////

        return response()->json([
            'DIDListByPbxService' => $resPbxDID,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resources (list did) from a pbxService
     *
     * @param  int  $idPbxService
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showEXTListByService($idPbxService, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "showEXTListByService");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // obtener pbx service
        $resPbxServicesEXT = PbxServicesExtensions::where('pbx_service_id', '=', $idPbxService)->join('profile_extensions', 'pbx_services_extensions.pbx_profile_extension_id', '=', 'profile_extensions.id')->get();

        $resPbxEXT = [];
        // buscar pbx did asociados al servicio
        foreach ($resPbxServicesEXT as $pbxService) {

            $result = PbxExtensions::find($pbxService->pbx_extension_id);
            $result->package_name = $pbxService->name;
            $result->package_rate = $pbxService->rate;

            array_push($resPbxEXT, $result);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'ExtListByPbxService' => $resPbxEXT,
        ], "message" => "PbxServicesController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController storePbxServiceExtensions");
        ////////////////////////////////////////////////////////////////////////////

        return response()->json([
            'ExtListByPbxService' => $resPbxEXT,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resPbxServices = PbxServices::with('package')
            ->with('did')
            ->with('extensions')
            ->with('tenant')
            ->with('taxTypes')
            ->with('items')
            ->with('address')
            ->select('pbx_services.*')
            ->find($id);
        if ($resPbxServices->pbx_package_id) {
            // get server/pbx_package
            $server = PbxPackages::leftjoin('pbx_servers', 'pbx_servers.id', '=', 'pbx_packages.pbx_server_id')
                ->select('pbx_servers.*')
                ->where('pbx_packages.pbx_server_id', '=', $resPbxServices->pbx_package_id)
                ->first();
            // assign
            $resPbxServices->package->server = $server;
            // get profile_did_rate / pbx package
            $profile_did = PbxPackages::leftjoin('profile_did', 'profile_did.id', '=', 'template_did_id')
                ->select('profile_did.*')
                ->where('pbx_packages.id', '=', $resPbxServices->pbx_package_id)
                ->first();
            // assign
            $resPbxServices->package->profile_did = $profile_did;

            //custom groups
            $listtc = TollFreeCustomDidGroup::where("profile_did_id", $profile_did.id)->pluck("custom_did_group_id");
            //  //Log::debug("aqui");
            //Log::debug($listtc);
            // get profile_extensions_rate / pbx package
            $profile_extensions = PbxPackages::leftjoin('profile_extensions', 'profile_extensions.id', '=', 'template_extension_id')
                ->select('profile_extensions.*')
                ->where('pbx_packages.id', '=', $resPbxServices->pbx_package_id)
                ->first();
            // assign
            $resPbxServices->package->profile_extensions = $profile_extensions;

        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxService' => $resPbxServices,
        ], "message" => "PbxServicesController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController show");
        /////////////////////////////////////////

        return response()->json([
            'pbxService' => $resPbxServices,
            'message' => 'Pbx Service succesfully',
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     * @param CustomerPackage $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, PbxServices $service)
    {
        Log::debug("---------uodate");
        Log::debug($request);

        $pbx_service_id = $request['parameters']['id'];
        $custom_destination_groups_request = $request['parameters']["custom_destination_groups"];

        $time = microtime(true);
        $company_id = Auth::user()->company_id;
        $request->merge(['service' => $service]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PbxServiceController", "update");
        $pbxService = PbxServices::updatePbxServices($request, $service);

        // evaluar status actualizado
        if ($pbxService->status === 'A') {
            try {
                //  $this->sendEmailToCustomer($pbxService->customer_id, $company_id, 'reactivation', 'PBX Service Reactivation', $pbxService);

            } catch (Throwable $e) {
                // return response()->json(['message' => $e->getMessage()]);
            }

        }
        if ($pbxService->status === 'S') {
            try {
                $this->sendEmailToCustomer($pbxService->customer_id, $company_id, 'suspend', 'PBX Service Suspended', $pbxService);
            } catch (Throwable $e) {
                // return response()->json(['message' => $e->getMessage()]);
            }
        }
        if ($pbxService->status === 'C') {
            try {
                $this->sendEmailToCustomer($pbxService->customer_id, $company_id, 'cancel', 'PBX Service Cancelled', $pbxService);
            } catch (Throwable $e) {
                // return response()->json(['message' => $e->getMessage()]);
            }
        }
        // validar para generar "invoice" prorate
        if (isset($request['parameters']['invoice_prorate'])) {
            if ($request['parameters']['invoice_prorate']) {
                $this->generateInvoiceProrate($request['parameters'], $pbxService->id);
            }
        }

        if ($pbxService->id) {
            // Packages Tax Types cdrs
            pbxServicesTaxTypesCdr::where('pbx_services_id', $pbxService->id)->delete();

            if (count($request->parameters['taxesCdr']) > 0) {
                if (count($request->parameters['taxesCdr']) > 0) {
                    foreach ($request->parameters['taxesCdr'] as $tax) {
                        $this->addPbxServicesTaxTypesCdrs($tax, $pbxService->id, $company_id);
                    }
                }
            }

            // Add update pbx_services_custom_app
            if (isset($request['parameters']['pbx_services_app_rate'])) {
                PbxServicesAppRate::where('pbx_service_id', $pbxService->id)->delete();
                if (count($request['parameters']['pbx_services_app_rate']) > 0) {

                    foreach ($request['parameters']['pbx_services_app_rate'] as $custom_app) {
                        $customApp = [
                            'id' => $custom_app['id'] ?? null,
                            'app_name' => $custom_app['app_name'],
                            'quantity' => $custom_app['quantity'],
                            'costo' => $custom_app['costo'],
                            'pbx_package_id' => $request['parameters']['pbx_package_id'],
                            'pbx_service_id' => $pbxService->id,
                        ];
                        $this->updatePbxServicesCustomApp($customApp);
                    }
                }
            }
        }

        if ($request->parameters['allow_pbx_packages_update']) {
            PbxAdditionalCharge::deleteAdditionalCharges($request->parameters['id']);
            $packageData = $request->parameters['package'];

            if (! empty($packageData['profile_extensions']['aditional_charges_a']) && count($request->parameters['extensions']) > 0) {
                PbxAdditionalCharge::createAdditionalCharge($packageData['profile_extensions']['aditional_charges_a'], count($request->parameters['extensions']), $request->parameters['id']);
            }

            if (! empty($packageData['profile_did']['aditional_charges_a']) && count($request->parameters['dids']) > 0) {
                PbxAdditionalCharge::createAdditionalCharge($packageData['profile_did']['aditional_charges_a'], count($request->parameters['dids']), $request->parameters['id']);
            }

        } else {
            PbxAdditionalCharge::updateAdditionalCharge(count($request->parameters['dids']), count($request->parameters['extensions']), $request->parameters['id']);

        }

        //// Update custom destinations groups (Delete and Create)
        $custom_destination_groups = DB::table('pbx_services_prefixrate_groups')
            ->where("pbx_service_id", $pbx_service_id)
            ->where('deleted_at', null)
            ->pluck('id');
        // Delete
        if (count($custom_destination_groups) > 0) {
            foreach ($custom_destination_groups as $id) {
                DB::table('pbx_services_prefixrate_groups')->where('id', $id)->delete();
            }
        }
        // Create
        if (! is_null($custom_destination_groups_request)) {
            // Create
            foreach ($custom_destination_groups_request as $group) {
                DB::table('pbx_services_prefixrate_groups')->insert([
                    'pbx_service_id' => $pbx_service_id,
                    'prefixrate_group_id' => $group['id'],
                    'type' => $group['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
            }
        }
        //////////////////////////////////////////////////////

        // // Assign value from "extensions" / pbx_services_extensions
        // if(isset($request["parameters"]["pbx_package"]["profile_extensions"]))
        // {
        //     if(isset($request["parameters"]["pbx_package"]["profile_extensions"]["rate"]))
        //     {
        //         $pbx_service_id = $request["parameters"]["id"];
        //         $profile_extensions_rate = $request["parameters"]["pbx_package"]["profile_extensions"]["rate"];

        //         $ids = PbxServicesExtensions::where("pbx_service_id", $pbx_service_id)->pluck('id');

        //         foreach($ids as $id)
        //         {
        //             $PbxServicesExtensions = PbxServicesExtensions::find($id);
        //             if($PbxServicesExtensions) {
        //                 $PbxServicesExtensions->price = $profile_extensions_rate;
        //                 $PbxServicesExtensions->save();
        //             }
        //         }
        //     }
        // }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["su|ccess" => true, "response" => [
            "update" => $pbxService,
        ], "message" => "PbxServicesController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController update");
        ///////////////////////////////////////////////////////////////////////////////
        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion de un servicio");

        return response()->json([
            'service' => $pbxService,
            'success' => true,
            'message' => 'PBX Service successfully updated',
        ]);

    }

    /**
     *
     *
     */
    public function saveInvoiceExtension($ext, $pbxservicesextension)
    {
        //Log::debug($ext);
        $pbxExt = PbxExtensions::select('*')->where('pbxext_id', $ext['pbxext_id'])->get();
        $pbx_service_ext = PbxServicesExtensions::select('pbx_service_id', 'prorate')->where('pbx_extension_id', $pbxExt[0]->id)->get();
        //Log::debug('--- pbx srvice ext ---');
        //Log::debug($pbxExt[0]->id);
        //Log::debug($pbx_service_ext);
        $pbx_service_package = PbxServices::select('pbx_package_id')->where('id', $pbxservicesextension->pbx_service_id)->get();
        // $pbx_service_package = CustomDidGroup::select('pbx_package_id')->where('id', $pbx_service_ext[0]->pbx_service_id)->get();
        $pbx_package = PbxPackages::find($pbx_service_package[0]->pbx_package_id);
        $pbx_template = ProfileExtensions::find($pbx_package->template_extension_id);

        $extData = [];
        $extData['pbx_extension_id'] = $pbxExt[0]->id;
        $extData['invoice_id'] = $ext['invoice_id'];
        $extData['template_extension_id'] = $pbx_package->template_extension_id;
        $extData['company_id'] = Auth::user()->company_id;
        $extData['creator_id'] = Auth::user()->id;
        $extData['pbx_extension_name'] = $ext['name'];
        $extData['pbx_extension_ext'] = $ext['ext'];
        $extData['pbx_extension_email'] = $ext['email'];
        $extData['pbx_extension_ua_fullname'] = $ext['ua_fullname'];
        $extData['template_extension_name'] = $pbx_template->name;
        $extData['template_extension_rate'] = ($pbxservicesextension->prorate) / 100;

        InvoiceExtension::create($extData);

    }

    /**
     * obtiene total de prorate si la extension tiene el valor, ademas realiza insert del mismo en invoice_extension
     */
    public function getTotalProrateExtension($data)
    {
        $total = 0;
        foreach ($data as $ext) {
            //Log::debug($ext->prorate);
            if (isset($ext['prorate'])) {
                if ($ext['invoice_prorate'] == 0) {
                    $total += $ext['prorate'];
                }
            }
        }

        return $total;
    }

    /**
     *
     */
    public function saveInvoiceDid($did, $pbxservicedid)
    {
        $pbxDid = PbxDID::select('*')->where('number', $did['number'])->get();
        $pbx_service_did = PbxServicesDID::select('pbx_service_id', 'custom_did_id', 'prorate')->where('pbx_did_id', $pbxDid[0]->id)->get();
        /* //Log::debug('--- pbx did ---');
        //Log::debug($pbxDid);*/
        $pbx_service_package = PbxServices::select('pbx_package_id')->where('id', $pbxservicedid->pbx_service_id)->get();
        // $pbx_service_package = CustomDidGroup::select('pbx_package_id')->where('id', $pbx_service_did[0]->pbx_service_id)->get();
        $pbx_package = PbxPackages::find($pbx_service_package[0]->pbx_package_id);
        $pbx_template = ProfileDID::find($pbx_package->template_did_id);
        $didData = [];
        $didData['pbx_did_id'] = $pbxDid[0]->id;
        $didData['invoice_id'] = $did['invoice_id'];
        $didData['template_did_id'] = $pbx_package->template_did_id;
        $didData['company_id'] = Auth::user()->company_id;
        $didData['creator_id'] = Auth::user()->id;
        $didData['pbx_did_number'] = $did['number'];
        $didData['pbx_did_server'] = $did['server'];
        $didData['pbx_did_trunk'] = $did['trunk'];
        $didData['pbx_did_type'] = $did['type'];
        /* $didData['custom_did_id'] = $did[''];
        $didData['custom_did_rate'] = $did[''];*/
        $didData['template_did_name'] = $pbx_template->name;
        $didData['template_did_rate'] = $pbxservicedid->prorate / 100;

        InvoiceDid::create($didData);

    }

    /**
     *
     */
    public function getTotalProrateDid($data)
    {
        $total = 0;
        foreach ($data as $did) {
            if (isset($did['prorate'])) {
                if (! $did['invoice_prorate']) {
                    $total += $did['prorate'];
                }
            }
        }

        return $total;
    }

    /**
     * generate invoice prorate
     *
     *
     */
    public function generateInvoiceProrate($request, $pbxServiceId = null)
    {
        //Log::debug('---- request-----');

        $contDID = PbxServicesDID::where('pbx_service_id', $pbxServiceId)->whereNULL('deleted_at')->where('invoiced_prorate', 0)->whereNotNull('date_prorate')->get()->count();
        $contEXT = PbxServicesExtensions::where('pbx_service_id', $pbxServiceId)->whereNULL('deleted_at')->where('invoiced_prorate', 0)->whereNotNull('date_prorate')->get()->count();

        if ($contDID == 0 && $contEXT == 0) {
            //Log::debug("anulado");
            return false;

        }
        //Log::debug($request);
        //Log::debug('---- pbx service id -----');
        //Log::debug($request);
        //Log::debug('---- $pbxServiceId -----');
        //Log::debug($pbxServiceId);
        $today = Carbon::today();
        $prefix = CompanySetting::getSetting('invoice_prefix', $request['company_id']);
        $nextNumber = Invoice::getNextInvoiceNumber($prefix);
        // data para la tabla invoice
        $request['invoice_number'] = $prefix.'-'.$nextNumber;
        $request['invoice_date'] = $today->format('Y-m-d');
        $request['due_date'] = $today->addDays(7)->format('Y-m-d');
        $request['company_id'] = Auth::user()->company_id;
        $request['pbx_service_id'] = $pbxServiceId;
        $request['pbx_total_extension'] = $this->getTotalProrateExtension($request['extensions']);

        $request['pbx_total_did'] = $this->getTotalProrateDid($request['dids']);

        $request['tax_per_item'] = 'NO';
        $request['discount_per_item'] = 'NO';

        $request['subtotal'] = $request['total_prorate'];

        $totaldescuento = 0;

        if ($request['allow_discount'] == true || $request['allow_discount'] == 1) {
            $request['discount_type'] = $request['allow_discount_type'];
            $request['discount_val'] = $request['allow_discount_value'];
            $request['discount'] = $request['discount_val'];

            if ($request['discount_type'] == "percentage") {
                $totaldescuento = $request['subtotal'] * ($request['discount_val'] / 100);
            } else {
                $totaldescuento = $request['subtotal'] - $request['discount_val'];
            }
        } else {
            $request['discount_type'] = null;
            $request['discount_val'] = null;
            $request['discount'] = null;
        }
        $request['subtotal'] = $request['subtotal'] - $totaldescuento;

        $taxsum = 0;
        if (count($request['taxes']) > 0) {
            $cont = 0;
            foreach ($request['taxes'] as $taxindi) {

                $taxval = ($request['subtotal'] * ($taxindi['percent'] / 100));
                $taxsum += ($request['subtotal'] * ($taxindi['percent'] / 100));
                $taxindi['amount'] = $taxval;
                $request['taxes'][$cont] = $taxindi;
                $cont++;
            }
        }

        $request['subtotalpro'] = $request['subtotal'];
        $var = $request['subtotal'] + $taxsum;
        $request['tax'] = $taxsum;
        $request['total'] = $var;
        $request['due_amount'] = $var;
        $request['pbxservice_date_renewal'] = $request['renewal_date'];
        $request['user_id'] = $request['customer_id'];
        $request['invoice_template_id'] = $request['customer_id'] || 1;

        $invoice = Invoice::createInvoiceProrate(collect($request));

        foreach ($request['dids'] as $did) {

            if (isset($did['prorate'])) {
                $did['invoice_id'] = $invoice->id;
                //Log::debug("---pbxdid 1 ");
                //Log::debug($did);
                $obj = PbxServicesDID::where('pbx_did_id', $did['id'])->where('pbx_service_id', $pbxServiceId)->whereNULL('deleted_at')->where('invoiced_prorate', 0)->whereNotNull('date_prorate')->first();

                if ($obj != null) {
                    //Log::debug("-----pbxdid 2");
                    //Log::debug($obj);
                    $this->saveInvoiceDid($did, $obj);
                    $obj->invoiced_prorate = 1;
                    $obj->save();
                }

            }
        }

        foreach ($request['extensions'] as $ext) {

            if (isset($ext['prorate'])) {
                $ext['invoice_id'] = $invoice->id;

                $obj = PbxServicesExtensions::where('pbx_extension_id', $ext['id'])->where('pbx_service_id', $pbxServiceId)->whereNULL('deleted_at')->where('invoiced_prorate', 0)->whereNotNull('date_prorate')->first();
                if ($obj != null) {
                    //Log::debug("-----pbxext");
                    //Log::debug($ext);
                    //Log::debug($obj);
                    $this->saveInvoiceExtension($ext, $obj);
                    $obj->invoiced_prorate = 1;
                    $obj->save();
                }

            }
        }

        foreach ($request['taxes'] as $tax) {
            //Log::debug('------- taxes --------');
            // $tax[''];
            $tax['invoice_id'] = $invoice->id;

            $tax['tax_type_id'] = $tax['tax_types_id'];
            Tax::create($tax);
        }

        $pbxservice = PbxServices::where('id', $pbxServiceId)->first();
        if ($pbxservice != null) {
            $pbxservice->total_prorate = 0;
            $pbxservice->save();
        }
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

    public function packageInfo(Request $request, $idPbxPackage)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "packageInfo");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resPbxPackage = PbxPackages::where('pbx_packages.id', $idPbxPackage)
            ->join('pbx_servers', 'pbx_servers.id', '=', 'pbx_packages.pbx_server_id')
            ->leftJoin('profile_did', 'profile_did.id', '=', 'pbx_packages.template_did_id')
            ->leftJoin('profile_extensions', 'profile_extensions.id', '=', 'pbx_packages.template_extension_id')
            ->select('pbx_servers.server_label', 'profile_did.name as did_name', 'profile_extensions.name as extension_name', 'pbx_packages.call_ratings as call_ratings')
            ->first();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'tenantList' => $resPbxPackage,
        ], "message" => "PbxServicesController listTenant"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController listTenant");
        ///////////////////////////////////////////////////////////////////////////////

        // success
        return response()->json([
            'pbxPackage' => $resPbxPackage,
        ]);

    }

    public function getAdditionalCharges(Request $request, $pbx_service_id)
    {
        try {
            $pbxService = PbxServices::findOrFail($pbx_service_id);
            $time = microtime(true);

            // Init log
            $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "getAdditionalCharges");

            $ext_charges = \DB::table('pbx_services')
                ->where('pbx_services.id', $pbx_service_id)
                ->where('aditional_charges.status', 1)
                ->wherenull('aditional_charges.deleted_At')
                ->join('pbx_packages', 'pbx_packages.id', '=', 'pbx_services.pbx_package_id')
                ->join('profile_extensions', 'pbx_packages.template_extension_id', '=', 'profile_extensions.id')
                ->join('aditional_charges', 'profile_extensions.id', '=', 'aditional_charges.profile_extension_id')
                ->select('aditional_charges.*', 'profile_extensions.name as profile_name')
                ->distinct();

            $did_charges = \DB::table('pbx_services')
                ->where('pbx_services.id', $pbx_service_id)
                ->where('aditional_charges.status', 1)
                ->wherenull('aditional_charges.deleted_At')
                ->join('pbx_packages', 'pbx_packages.id', '=', 'pbx_services.pbx_package_id')
                ->join('profile_did', 'pbx_packages.template_did_id', '=', 'profile_did.id')
                ->join('aditional_charges', 'profile_did.id', '=', 'aditional_charges.profile_did_id')
                ->select('aditional_charges.*', 'profile_did.name as profile_name')
                ->distinct();

            $charges = $ext_charges
                ->union($did_charges)
                ->orderBy(request('orderByField', 'description'), request('orderBy', 'asc'))
                ->paginate(request('limit', 10));

            $ext_charges_copy = $ext_charges;

            $did_charges_copy = $did_charges;

            $all_charges = $ext_charges_copy
                ->union($did_charges_copy)
                ->get();

            $count = $all_charges->count();
            $total_amount = $all_charges->sum('amount');

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'charges' => $charges,
                        'count' => $count,
                        'total_amount' => $total_amount,
                        'success' => true,
                    ],
                    "message" => "Lista de cargos adicionales asociados a un servicio pbx",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin lista de cargos adicionales asociados a un servicio pbx");

            // Module log
            LogsModule::createLog(
                "pbx-services",
                "getAdditionalCharges",
                "admin/customers/id/edit",
                $pbxService->id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id,
                "Pbx Service: ".$pbxService->pbx_services_number
            );

            return response()->json([
                'charges' => $charges,
                'count' => $count,
                'total_amount' => $total_amount,
                'success' => true,
            ]);

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function showPbxTaxType($pbx_service_id)
    {
        $resPbxTaxType = PbxServicesTaxTypes::where('pbx_services_id', $pbx_service_id)->get();

        // success
        return response()->json([
            'response' => $resPbxTaxType,
        ]);
    }

    public function listPrefixRateGroups()
    {
        $resPbxTaxType = PrefixRateGroups::all();

        // success
        return response()->json([
            'response' => $resPbxTaxType,
        ]);
    }

    //
    public function getDaysToRenewal(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServicesController", "daysToRenewal");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $term = $request['params']['term'];
        $activation_date = $request['params']['activation_date'];
        $renewal_date = $request['params']['renewal_date'];
        // calcular fecha de renovacion
        // $renewal_date = $this->getRenewalDate($activation_date, $term);

        // parse string tio date object
        $renewal_type_date = Carbon::parse($renewal_date);
        // identificar el tipo de term
        switch ($term) {
            case 'daily':
                $renewal_type_date->subDay();

                break;

            case 'weekly':
                $renewal_type_date->subDays(7);

                break;

            case 'monthly':
                $renewal_type_date->subDays(30);

                break;

            case 'bimonthly':
                $renewal_type_date->subDays(60);

                break;

            case 'quarterly':
                $renewal_type_date->subDays(90);

                break;

            case 'yearly':
                $renewal_type_date->subDays(365);

                break;

            case 'biannual':
                $renewal_type_date->subDays(730);

                break;

            default:
                # code...
                break;
        }

        $initDate = $renewal_type_date->format('Y-m-d');
        // obtener nro de dias para renovar
        $now = time(); // or your date as well
        // $your_date = strtotime($dateToRenewal);
        $datediff = $now - strtotime($initDate);
        $datediff2 = strtotime($renewal_date) - $now;

        $daysOfTerm = round($datediff / (60 * 60 * 24));
        $daysToRenewal = round($datediff2 / (60 * 60 * 24));

        /*  //Log::debug('-------');
        //Log::debug($initDate);
        //Log::debug($daysOfTerm);
        //Log::debug($daysToRenewal);
        //Log::debug($renewal_date);
        //Log::debug('-------'); */

        $response = [
            'activation_date' => $activation_date,
            'days_of_term' => $daysOfTerm,
            'days_to_renewal' => $daysToRenewal,
            'renewal_date' => $renewal_date,
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'response' => $response,
        ], "message" => "PbxServicesController listTenant"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServicesController listTenant");
        ///////////////////////////////////////////////////////////////////////////////

        // success
        return response()->json([
            $response,
        ]);
    }

    public function addPbxServicesTaxTypesCdrs($tax, $pbx_services_id, $company_id)
    {
        $res = [];
        $newPackageTaxTypes = new PbxServicesTaxTypesCdr();
        $newPackageTaxTypes->pbx_services_id = $pbx_services_id;
        $newPackageTaxTypes->company_id = $company_id;
        $newPackageTaxTypes->tax_types_id = $tax['id'];
        $newPackageTaxTypes->name = $tax['name'];
        $newPackageTaxTypes->percent = $tax['percent'];
        $newPackageTaxTypes->compound_tax = $tax['compound_tax'];
        $res[] = $newPackageTaxTypes->save();

        return $res;
    }

    public function updatePbxServicesCustomApp($customApp)
    {
        if ($customApp['id']) {
            $newPackageCustomApp = PbxServicesAppRate::find($customApp['id']);
        } else {
            $newPackageCustomApp = new PbxServicesAppRate();
        }

        $newPackageCustomApp->pbx_service_id = $customApp['pbx_service_id'];
        $newPackageCustomApp->pbx_package_id = $customApp['pbx_package_id'];
        $newPackageCustomApp->app_name = $customApp['app_name'];
        $newPackageCustomApp->quantity = $customApp['quantity'];
        $newPackageCustomApp->costo = $customApp['costo'];

        $res[] = $newPackageCustomApp->save();

        return $res;
    }

    public function getAdditionalChargesService($serviceId, Request $request)
    {
        try {

            $extensions = PbxAdditionalCharge::select(
                'profile_extensions.name as profile_name',
                'pbx_additional_charges.description as description',
                'pbx_additional_charges.amount as amount',
                'pbx_additional_charges.total as total',
                'pbx_additional_charges.quantity as quantity',
                'pbx_additional_charges.created_at as created_at',
                'pbx_additional_charges.profile_extension_id as profile_extension_id'
            )
                ->join('profile_extensions', 'pbx_additional_charges.profile_extension_id', '=', 'profile_extensions.id')
                ->where('pbx_additional_charges.pbx_service_id', $serviceId)
                ->where('pbx_additional_charges.profile_did_id', null)
                ->addSelect(DB::raw("'Extension' as type_from"))->get();

            $did = PbxAdditionalCharge::select(
                'profile_did.name as profile_name',
                'pbx_additional_charges.description as description',
                'pbx_additional_charges.amount as amount',
                'pbx_additional_charges.total as total',
                'pbx_additional_charges.quantity as quantity',
                'pbx_additional_charges.created_at as created_at',
                'pbx_additional_charges.profile_did_id as profile_did_id'
            )
                ->join('profile_did', 'pbx_additional_charges.profile_did_id', '=', 'profile_did.id')
                ->where('pbx_additional_charges.pbx_service_id', $serviceId)
                ->where('pbx_additional_charges.profile_extension_id', null)
                ->addSelect(DB::raw("'DID' as type_from"))->get();

            $data = [];

            if ($request['all']) {

                foreach ($extensions as $extension) {
                    array_push($data, $extension);
                }
                foreach ($did as $item) {
                    array_push($data, $item);
                }

                //ordenamiento

                if ($request['orderByField']) {
                    if ($request['orderByField'] == "description") {
                        if ($request['orderBy'] == "asc") {
                            $columna = array_column($data, 'description');
                            array_multisort($columna, SORT_ASC, $data);
                        } else {
                            $columna = array_column($data, 'description');
                            array_multisort($columna, SORT_DESC, $data);
                        }
                    }

                    if ($request['orderByField'] == "amount") {
                        if ($request['orderBy'] == "asc") {
                            $columna = array_column($data, 'amount');
                            array_multisort($columna, SORT_ASC, $data);
                        } else {
                            $columna = array_column($data, 'amount');
                            array_multisort($columna, SORT_DESC, $data);
                        }
                    }

                    if ($request['orderByField'] == "type_from") {
                        if ($request['orderBy'] == "asc") {
                            $columna = array_column($data, 'type_from');
                            array_multisort($columna, SORT_ASC, $data);
                        } else {
                            $columna = array_column($data, 'type_from');
                            array_multisort($columna, SORT_DESC, $data);
                        }
                    }

                    if ($request['orderByField'] == "profile_name") {
                        if ($request['orderBy'] == "asc") {
                            $columna = array_column($data, 'profile_name');
                            array_multisort($columna, SORT_ASC, $data);
                        } else {
                            $columna = array_column($data, 'profile_name');
                            array_multisort($columna, SORT_DESC, $data);
                        }
                    }

                    if ($request['orderByField'] == "quantity") {
                        if ($request['orderBy'] == "asc") {
                            $columna = array_column($data, 'quantity');
                            array_multisort($columna, SORT_ASC, $data);
                        } else {
                            $columna = array_column($data, 'quantity');
                            array_multisort($columna, SORT_DESC, $data);
                        }
                    }

                    if ($request['orderByField'] == "total") {
                        if ($request['orderBy'] == "asc") {
                            $columna = array_column($data, 'total');
                            array_multisort($columna, SORT_ASC, $data);
                        } else {
                            $columna = array_column($data, 'total');
                            array_multisort($columna, SORT_DESC, $data);
                        }
                    }

                }

                //\Log::debug($data);
                return $this->paginate($data);
            }

            return response()->json([
                'data' => [
                    'extensions' => $extensions,
                    'dids' => $did,
                    'total_did' => $did->sum('total'),
                    'total_extension' => $extensions->sum('total'),
                ],
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th,
            ]);
        }
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function addPbxServiceItem(Request $request)
    {
        \Log::debug($request->input());
        $data = $request->only(['items_id', 'name', 'description', 'price', 'end_period_act', 'end_period_number', 'pbx_service_id']);
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::user()->id;
        $data['tax'] = 0;

        try {
            $pbxservice = PbxServices::findOrFail($data['pbx_service_id']);
            $item = Item::find($data['items_id']);

            $data['items_id'] = $item ? $item->id : null;
            $data['end_period_act'] = $data['end_period_act'] ? 1 : 0;

            $newPbxServicesItems = new PbxServicesItems($data);
            $newPbxServicesItems->fill([
                'item_group_id' => 0,
                'discount_type' => 'fixed',
                'quantity' => 1,
                'discount' => 0,
                'discount_val' => 0,
                'total' => $data['price'],
                'price' => $data['price'],
                'pbx_services_id' => $data['pbx_service_id'],
            ]);
            $newPbxServicesItems->save();

            return response()->json([
                'message' => '¡Registro Exitoso!',
                'success' => true,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'services_no_found',
                'success' => false,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());

            return response()->json([
                'message' => 'unexpected_error',
                'success' => false,
            ]);
        }
    }
}
