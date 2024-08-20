<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\PbxServicesAppRateCollection;
use Crater\Models\AvalaraInvoice;
use Crater\Models\AvalaraTax;
use Crater\Models\CallHistoryIndi;
use Crater\Models\customAppRate;
use Crater\Models\CustomDidGroup;
use Crater\Models\Invoice;
use Crater\Models\Item;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxAdditionalCharge;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesAppRate;
use Crater\Models\PbxServicesDID;
use crater\Models\PbxServicesExtensions;
use Crater\Models\PbxServicesItems;
use Crater\Models\PbxServicesTaxTypes;
use Crater\Models\PbxServicesTaxTypesCdr;
use Crater\Models\PrefixRateGroups;
use Crater\Models\ProfileDidCustomDidGroups;
use Crater\Models\ProfileDidTollFree;
use Crater\Models\Tax;
//
use Crater\Models\TollFreeCustomDidGroup;
use Crater\Models\User;
//

use Crater\Pbxware\Service\PbxExtensionSuspendService;
use Crater\Pbxware\Service\PbxTenantSuspendService;
use Crater\Pbxware\Service\ServiceDetail;
use Crater\Traits\PbxServicesReCalculateTrait;
use Crater\Traits\SendEmailsTrait;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class PbxServiceDetailController extends Controller
{
    use SendEmailsTrait;
    use PbxServicesReCalculateTrait;

    /**
     * Service Details: Extension
     *
     * Return A List Of Extension from a specified PbxService.
     *
     * @responseField success The success of the response (true or false).
     * @responseField service_extension list of paginate items ('data': []).
     * @group Service Details
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailExtensions(Request $request, ServiceDetail $service_detail, $pbx_service_id): JsonResponse
    {
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "serviceDetailExtensions(");
        $limit = $request->input('limit', 'all');

        $extensions = $service_detail->getServiceExtensions($pbx_service_id, $limit);

        $res = [
            "success" => true,
            "response" => ['service_extensions' => $extensions['data'],
                'totals' => $extensions['totals'],
                'pbx_services_extensions' => $extensions['pbx_services_extensions'],
            ],
            "message" => "List service detail success",
        ];

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailExtensions");

        return response()->json($res);
    }

    /**
     * @param Request $request
     * @param PbxServices $service
     * @return JsonResponse
     */
    public function getService(Request $request, $pbx_service_id)
    {
        \Log::debug("Get Service");
        /////////////////////////// Items with Taxes
        $itemsService = PbxServicesItems::where("pbx_services_id", "=", $pbx_service_id)->get();
        foreach ($itemsService as $key => $itemService) {
            $itemsService[$key]["taxes"] = Tax::where("pbx_service_item_id", "=", $itemService->id)->get();
        }
        ///////////////////////////

        $time = microtime(true);

        $service = PbxServices::findOrFail($pbx_service_id);

        $request->merge(['service' => $service]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "getService");

        $service->load([
            'user',
            'pbxPackage',
            "pbxServiceTaxTypes",
            "pbxPackage.server.cdrStatus",
            "pbxPackage.profileDid",
            "pbxPackage.profileDid2",
            "pbxPackage.profileExtensions",
            "pbxPackage.cdrStatus",
            "tenant",
            "getItems.taxes_per_item",
            "pbxPackage.profileDid.aditionalChargesA",
            "pbxPackage.profileExtensions.aditionalChargesA",
            "pbxServiceExtensions.extension",
            "pbxServiceDids.did",
            "getCallDetailRegisterTotal",
            "taxes",
            "taxTypesCdr",
            "pbxServicesAppRate",
            'address.state',
        ]);

        $vari = $service->toArray();

        // Verificar si el objeto $service contiene un objeto tenant y las variables requeridas
        if (isset($service->tenant) && isset($service->tenant->tenantid) && isset($service->tenant->pbx_server_id)) {
            // Realizar la consulta en la base de datos
            $pbxServerTenant = PbxServerTenant::where('tenant_id', $service->tenant->tenantid)
                ->where('pbx_server_id', $service->tenant->pbx_server_id)
                ->first();

            // Verificar si se encontró un registro
            if ($pbxServerTenant) {
                // Agregar el campo pbxservertenant_id al objeto tenant con el valor del id encontrado
                $service->tenant->pbxservertenant_id = $pbxServerTenant->id;
            } else {
                // Si no se encuentra un registro, asignar null al campo pbxservertenant_id
                $service->tenant->pbxservertenant_id = null;
            }
        } else {
            // Si el objeto tenant no existe o no tiene las variables necesarias, asignar null
            $service->tenant->pbxservertenant_id = null;
        }

        $models = [];

        $priceextension = 0;
        $pricedid = 0;
        $customgroup = null;
        if ($vari['pbx_package']['profile_extensions'] != null) {
            $priceextension = $vari['pbx_package']['profile_extensions']['rate'];
        }

        if ($vari['pbx_package']['profile_did'] != null) {
            $pricedid = $vari['pbx_package']['profile_did']['did_rate'];

            $vec1 = ProfileDidCustomDidGroups::where("profile_did_id", $vari['pbx_package']['profile_did']['id'])->whereNULL("deleted_at")->pluck('custom_did_group_id');

            $custom_dids = TollFreeCustomDidGroup::whereIN("custom_did_group_id", $vec1)->whereNULL("deleted_at")->pluck('toll_free_did_id');

            $service->custom_did_groups = ProfileDidTollFree::whereIN('id', $custom_dids)->orderBy('prefijo', 'desc')->get();

            foreach ($service->custom_did_groups as $prefix) {
                $first = TollFreeCustomDidGroup::whereIN("custom_did_group_id", $vec1)->where("toll_free_did_id", $prefix->id)->whereNULL("deleted_at")->first();

                if ($first != null) {

                    $second = CustomDidGroup::where("id", $first->custom_did_group_id)->first();
                    if ($second != null) {
                        $prefix["name_prefix"] = $second->name;
                    }
                }
            }

        }

        //
        $CountServicesExtension = count($vari['pbx_service_extensions']);
        $exts = [];
        $TotalServicesExtension = 0;

        if ($CountServicesExtension > 0) {
            $groups = PbxServicesExtensions::where('pbx_service_id', '=', $vari['id'])
                ->groupBy('price')
                ->select(
                    '*',
                    DB::raw('count(*) as qty'),
                    DB::raw('GROUP_CONCAT(pbx_extension_id) AS pbx_extensions_id')
                )
                ->get();

            \Log::debug("Groups Exts");
            \Log::debug($groups);

            // Template name and rate
            $template_name = $vari['pbx_package']['profile_extensions']["name"];
            $template_rate = $vari['pbx_package']['profile_extensions']["rate"];

            foreach ($groups as $key => $group) {
                if ($group['price'] != null) {
                    $total = $group['price'] * $group['qty'];
                    $TotalServicesExtension += $total;
                    $pbx_extensions_id = array_map('intval', explode(',', $group['pbx_extensions_id']));

                    $exts[] =
                        [
                        $key,
                        $group['qty'],
                        round(floatval($group['price']), 2),
                        $total,
                        $template_name,
                        $pbx_extensions_id,
                    ];
                } else {
                    $total = $template_rate * $group['qty'];
                    $TotalServicesExtension += $total;
                    $pbx_extensions_id = array_map('intval', explode(',', $group['pbx_extensions_id']));
                    $exts[] =
                        [
                        $key,
                        $group['qty'],
                        round(floatval($template_rate), 2),
                        $total,
                        $template_name,
                        $pbx_extensions_id,
                    ];
                }
            }
        }

        \Log::debug("Exts:");
        \Log::debug($exts);

        $CountServicesDid = count($vari['pbx_service_dids']);
        $dids = [];
        $TotalServicesDid = 0;

        if ($CountServicesDid > 0) {
            $groups = PbxServicesDID::where('pbx_service_id', '=', $vari['id'])
                ->groupBy('custom_did_id')->groupBy('price')
                ->select(
                    'custom_did_id',
                    'price',
                    'name_prefix',
                    DB::raw('count(*) as qty'),
                    DB::raw('GROUP_CONCAT(pbx_did_id) AS pbx_dids_id')
                )
                ->get();

            foreach ($groups as $key => $group) {
                $name_prefix = "";

                // Validate Price
                if ($group['price'] != null) {
                    // Validate Name Prefix
                    if ($group['name_prefix'] != null) {
                        $name_prefix = $group['name_prefix'];
                    } else {
                        if ($group['custom_did_id'] != null) {
                            $custom_did_group = CustomDidGroup::where('id', $group['custom_did_id'])->first();
                            $name_prefix = $custom_did_group['name'];
                        } else {
                            $name_prefix = $vari['pbx_package']['profile_did']['name'];
                        }
                    }
                    //

                    $total = $group['price'] * $group['qty'];
                    $TotalServicesDid += $total;
                    $pbx_dids_id = array_map('intval', explode(',', $group['pbx_dids_id']));

                    $dids[] =
                        [
                        $key,
                        $group['qty'],
                        round(floatval($group['price']), 2),
                        $total,
                        $name_prefix,
                        $pbx_dids_id,
                    ];

                } else {
                    // Validate Name Prefix
                    if ($group['name_prefix'] != null) {
                        $name_prefix = $group['name_prefix'];
                    } else {
                        if ($group['custom_did_id'] != null) {
                            $custom_did_group = CustomDidGroup::where('id', $group['custom_did_id'])->first();
                            $name_prefix = $custom_did_group['name'];
                        } else {
                            $name_prefix = $vari['pbx_package']['profile_did']['name'];
                        }
                    }

                    if ($group['custom_did_id'] != null) {
                        $profile_did_toll_frees = ProfileDidTollFree::find($group['custom_did_id']);

                        $rate_per_minute = $profile_did_toll_frees != null
                        ? $profile_did_toll_frees['rate_per_minute']
                        : $pricedid;

                        $total = $rate_per_minute * $group['qty'];
                        $TotalServicesDid += $total;
                        $pbx_dids_id = array_map('intval', explode(',', $group['pbx_dids_id']));

                        $dids[] =
                            [
                            $key,
                            $group['qty'],
                            round(floatval($rate_per_minute), 2),
                            $total,
                            $name_prefix,
                            $pbx_dids_id,
                        ];
                    } else {
                        $total = $vari['pbx_package']['profile_did']['did_rate'] * $group['qty'];
                        $TotalServicesDid += $total;
                        $pbx_dids_id = array_map('intval', explode(',', $group['pbx_dids_id']));

                        $dids[] =
                            [
                            $key,
                            $group['qty'],
                            round(floatval($pricedid), 2),
                            $total,
                            $name_prefix,
                            $pbx_dids_id,
                        ];
                    }
                }
            }

            /*
        $return_did = array();
        $custom_did_id= array();

        foreach ($vari['pbx_service_dids'] as $item)
        {
        $custom_did_id[] = $item['custom_did_id'] ? $item['custom_did_id'] : 0;
        }

        $collection = collect($custom_did_id);
        $counted_collection = $collection->countBy();
        $resul = $counted_collection->toArray();

        $name_prefix = PbxServicesDID::where('pbx_service_id', '=', $vari['id'])->orderBy('id')->groupBy('custom_did_id')->groupBy('price')->pluck('name_prefix');

        $i=0;

        foreach ($resul as $key => $value) {
        $resul = 0;
        if ($key > 0) {
        //Log::debug($key);
        if($name_prefix[$i] != null)
        {
        $buscador = ProfileDidTollFree::find($key);
        $resul = $buscador['rate_per_minute'] * $value;
        $TotalServicesDid += $resul;
        $return_did[] = [$key, $value, round(floatval($buscador['rate_per_minute']), 2), $resul, $name_prefix[$i]];
        }else{
        $buscador = ProfileDidTollFree::find($key);
        $resul = $buscador['rate_per_minute'] * $value;
        $TotalServicesDid += $resul;
        $return_did[] = [$key, $value, round(floatval($buscador['rate_per_minute']), 2), $resul, 'Custom DID'];
        }

        } else {

        if($name_prefix[$i] != null)
        {
        $resul = $pricedid * $value;
        $TotalServicesDid += $resul;
        $return_did[] = [$key, $value, round($pricedid, 2), $resul, $name_prefix[$i]];
        }else{
        $resul = $pricedid * $value;
        $TotalServicesDid += $resul;
        $return_did[] = [$key, $value, round($pricedid, 2), $resul, 'Custom DID'];
        }

        }
        $i++;
        }
         */
        }

        //

        $contador = 0;
        $contadoraddi = 0;

        if ($vari['pbx_package']['profile_extensions'] != null) {
            foreach ($vari['pbx_package']['profile_extensions']['aditional_charges_a'] as $total) {
                $contador += $total['amount'];
            }
            $contadoraddi = count($vari['pbx_package']['profile_extensions']['aditional_charges_a']);
        }

        $addicionalExtension = $contador * $CountServicesExtension;

        $contador1 = 0;
        $contadoraddi1 = 0;
        if ($vari['pbx_package']['profile_did'] != null) {

            foreach ($vari['pbx_package']['profile_did']['aditional_charges_a'] as $total) {

                $contador1 += $total['amount'];
            }
            $contadoraddi1 = count($vari['pbx_package']['profile_did']['aditional_charges_a']);
        }

        $addicionalDid = $contador1 * $CountServicesDid;

        $TotalAddicionalCharges = PbxAdditionalCharge::where('pbx_service_id', $pbx_service_id)->whereNull('deleted_at')->sum('total');
        $additionalChargesData = PbxAdditionalCharge::where('pbx_service_id', $pbx_service_id)->whereNull('deleted_at')->get();

        $TotalCdrBilling = 0;
        $TotalDuration = 0;
        foreach ($vari['get_call_detail_register_total'] as $total) {
            $TotalDuration += $total['total_duration'];
            $TotalCdrBilling += $total['exclusive_cost'];
        }

        //
        $service->CountServicesExtension = $CountServicesExtension;
        $service->TotalServicesExtension = $TotalServicesExtension;
        $service->InfoExts = $exts;
        $service->CountServicesDid = $CountServicesDid;
        $service->TotalServicesDid = $TotalServicesDid;
        $service->InfoDid = $dids;
        //
        $service->addicionalExtension = $addicionalExtension;
        $service->addicionalDid = $addicionalDid;
        $service->TotalAddicionalCharges = $TotalAddicionalCharges;
        $service->TotalCdrBilling = $TotalCdrBilling;
        $service->TotalDuration = $TotalDuration;
        $service->additionalChargesData = $additionalChargesData;

        ///////////////////////////calculo de cdrs
        /// totales sin facturar
        $cdr_total = $service->pbxCdrTotalsCurrent()->get();

        //total de lo adeudado de todo el servicio
        $cdr_total2 = DB::table('call_detail_register_totals')->selectRaw('(exclusive_cost - exclusive_cost_paid) as amount_due')->where('pbx_services_id', '=', $pbx_service_id)->whereColumn('exclusive_cost', '>', 'exclusive_cost_paid')->get();
        //total de lo adeudado de todo el servicio
        $cdr_total3 = DB::table('call_detail_register_totals')->select('*')->where('pbx_services_id', '=', $pbx_service_id)->whereNull("invoice_id")->get();
        //total de lo adeudado de todo el servicio
        $cdr_total4 = DB::table('call_detail_register_totals')->selectRaw('(exclusive_cost) as amount_due')->where('pbx_services_id', '=', $pbx_service_id)->whereColumn('exclusive_cost', '=', 'exclusive_cost_paid')->whereNull("invoice_id")->get();

        //calculo unicial
        $total_deb = $cdr_total2->sum('amount_due');
        $total_consume = $cdr_total->sum('exclusive_cost');
        $paid_consume = $cdr_total->sum('exclusive_cost_paid');

        $paid_consume = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $cdr_total3->pluck('id')->toArray())->sum('amoutbruto');
        $pain_consume = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $cdr_total3->pluck('id')->toArray())->sum('amout');
        $taxconsume = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $cdr_total3->pluck('id')->toArray())->sum('taxamount');

        $total_consume = $total_consume + $taxconsume;
        $paid_consume = $paid_consume + $taxconsume;

        if ($paid_consume >= $total_consume) {
            $total_consume = $paid_consume;
        }
        $unpaid_consume = $total_consume - $paid_consume;

        $service->total_deb = number_format($total_deb, 2, '.', '');
        $service->total_consume = number_format($total_consume, 2, '.', '');
        $service->paid_consume = number_format($paid_consume, 2, '.', '');
        $service->unpaid_consume = number_format($unpaid_consume, 2, '.', '');
        // $service->pbxpackages_price = number_format($service->pbxpackages_price, 2, '.', '');
        $service->allow_discount_value = number_format($service->allow_discount_value, 2, '.', '');

        if ($service->total_deb == 0) {
            $service->unpaid_consume = $service->total_deb;
            $service->paid_consume = $service->total_consume;
        }

        ///////////////////////////calculo de cdrs

        ////////////custom app rate
        $service->custom_app_rate_name = "Not Selected";
        if ($service->custom_app_rate_id != null) {
            $obj = customAppRate::where("id", $service->custom_app_rate_id)->first();
            if ($obj != null) {
                $service->custom_app_rate_name = $obj->name;
            }
        }

        ////////////prefixrate_groups_id inbound
        $service->prefixrate_groups_name_inb = "Not Selected";
        if ($service->prefixrate_groups_id != null) {
            $obj = PrefixRateGroups::where("id", $service->prefixrate_groups_id)->first();
            if ($obj != null) {
                $service->prefixrate_groups_name_inb = $obj->name;
            }
        }

        ////////////prefixrate_groups_id toubound
        $service->prefixrate_groups_name_out = "Not Selected";
        if ($service->prefixrate_groups_outbound_id != null) {
            $obj = PrefixRateGroups::where("id", $service->prefixrate_groups_outbound_id)->first();
            if ($obj != null) {
                $service->prefixrate_groups_name_out = $obj->name;
            }
        }

        $models['pbx_services_app_rate'] = new PbxServicesAppRateCollection(
            PbxServicesAppRate::where('pbx_service_id', '=', $service->id)
                ->paginate($request->get('app_rate_paginate', 10), ['*'], 'app_rate_page')
        );

        $service->relations = $models;

        // Avalara Bools (Customer And Package)

        $avalara_bool_package = PbxPackages::where('id', '=', $service->pbx_package_id)->first();
        $avalara_bool_customer = User::where('id', '=', $service->customer_id)->first('avalara_bool');

        $pbx_avalara = [];
        if ($avalara_bool_package->avalara_options == 1 && $avalara_bool_package->avalaraBundle == 0) {
            // [0]
            $avalara_services_price_item = Item::where("id", "=", $avalara_bool_package->avalara_services_price_item_id)->first();
            array_push($pbx_avalara, $avalara_services_price_item);

            // [1]
            $avalara_extension = Item::where("id", "=", $avalara_bool_package->avalara_extension_item_id)->first();
            array_push($pbx_avalara, $avalara_extension);

            // [2]
            $avalara_bool_package->avalara_did == 1
            ? $avalara_did = Item::where("id", "=", $avalara_bool_package->avalara_did_item_id)->first()
            : $avalara_did = null;

            array_push($pbx_avalara, $avalara_did);

            // [3]
            $avalara_additional_charges = Item::where("id", "=", $avalara_bool_package->avalara_additional_charges_item_id)->first();
            array_push($pbx_avalara, $avalara_additional_charges);

            // [4]
            $avalara_custom_app_rate = Item::where("id", "=", $avalara_bool_package->avalara_custom_app_rate_item_id)->first();
            array_push($pbx_avalara, $avalara_custom_app_rate);
        }

        // Load Custom Destinations Groups (PbxPackage)
        $custom_destination_groups = [];

        $prefixrate_groups_ids = DB::table('pbx_services_prefixrate_groups')->where("pbx_service_id", $pbx_service_id)
            ->where('deleted_at', null)
            ->pluck('prefixrate_group_id');

        $custom_destination_groups = DB::table('prefixrate_groups')
            ->whereIn("id", $prefixrate_groups_ids)
            ->where('deleted_at', null)
            ->get();
        //
        $res = [
            "success" => true,
            "response" =>
            ['pbx_service' => $service, "itemsService" => $itemsService,
                //
                'avalara_bool_package' => $avalara_bool_package, 'avalara_bool_customer' => $avalara_bool_customer,
                'pbx_avalara' => $pbx_avalara,
                //
                'custom_destination_groups' => $custom_destination_groups,
            ],
            "message" => "Show service success",
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End show service");

        // Module log
        LogsModule::createLog(
            "Pbx Services",
            "Show",
            "admin/services/:id/view",
            $service->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Pbx service: ".$service->pbx_services_number
        );

        return response()->json($res);
    }

    public function getAvalaraTaxesItems(Request $request, $pbx_package_id)
    {
        // Avalara Bools (Customer And Package)
        $avalara_bool_package = PbxPackages::where('id', '=', $pbx_package_id)->first();

        $pbx_avalara = [];
        if ($avalara_bool_package->avalara_options == 1 && $avalara_bool_package->avalaraBundle == 0) {
            // [0]
            $avalara_services_price_item = Item::where("id", "=", $avalara_bool_package->avalara_services_price_item_id)->first();
            array_push($pbx_avalara, $avalara_services_price_item);

            // [1]
            $avalara_extension = Item::where("id", "=", $avalara_bool_package->avalara_extension_item_id)->first();
            array_push($pbx_avalara, $avalara_extension);

            // [2]
            $avalara_bool_package->avalara_did == 1
            ? $avalara_did = Item::where("id", "=", $avalara_bool_package->avalara_did_item_id)->first()
            : $avalara_did = null;
            array_push($pbx_avalara, $avalara_did);

            // [3]
            $avalara_additional_charges = Item::where("id", "=", $avalara_bool_package->avalara_additional_charges_item_id)->first();
            array_push($pbx_avalara, $avalara_additional_charges);

            // [4]
            $avalara_custom_app_rate = Item::where("id", "=", $avalara_bool_package->avalara_custom_app_rate_item_id)->first();
            array_push($pbx_avalara, $avalara_custom_app_rate);
        }

        $res = [
            "success" => true,
            "response" =>
            [
                'avalara_bool_package' => $avalara_bool_package, 'pbx_avalara' => $pbx_avalara,
            ],
            "message" => "Show service success",
        ];

        return response()->json($res);
    }

    public function getAvalaraTaxes(Request $request, $invoice_id)
    {
        $query = AvalaraInvoice::where('invoice_id', '=', $invoice_id)->first();
        ////////////////////////////////////////////////////////
        $items_with_avalara_taxes = [];
        $data = [];

        if ($query != null) {
            $query_test_1 = AvalaraTax::where('avalara_invoice_id', '=', $query->id)
                ->pluck("item_id")->toarray();

            $items_ids = array_unique($query_test_1);

            foreach ($items_ids as $i => $item_id) {
                $total_tax = 0;
                $items_with_avalara_taxes[$i]["name"] = Item::where('id', $item_id)->first()->name;
                $taxes_avalara = AvalaraTax::where('avalara_invoice_id', '=', $query->id)->where('item_id', $item_id)->get()->toArray();
                $items_with_avalara_taxes[$i]["taxes"] = $taxes_avalara;

                foreach ($taxes_avalara as $tax_avalara) {
                    $total_tax += $tax_avalara["tax"];
                }
                $items_with_avalara_taxes[$i]["total_tax"] = $total_tax;

                array_push($data, $items_with_avalara_taxes[$i]);

            }

        }

        $res = [
            "success" => true,
            "response" => [
                'data' => $data,
            ],
            "message" => "Avalara Taxes",
        ];

        return response()->json($res);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function listServiceDetail(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        $log = LogsDev::initLog($request, "", "D", "PbxDIDController", "show");

        $extensions = $service_detail->getServiceExtensions($pbx_service_id, 'all');

        $service_did = $service_detail->getServiceDids($pbx_service_id, 'all');

        $res = [
            "success" => true,
            "response" => ['service_extensions' => $extensions, 'service_did' => $service_did],
            "message" => "List service detail success",
        ];

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController listServiceDetail");

        return response()->json($res);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailDids(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        $log = LogsDev::initLog($request, "", "D", "PbxDIDController", "show");
        $limit = $request->input('limit', 'all');
        $service_did = $service_detail->getServiceDids($pbx_service_id, $limit);

        $res = [
            "success" => true,
            "response" => ['service_did' => $service_did['data'], 'totals' => $service_did['totals']],
            "message" => "List service detail success",
        ];

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailDids");

        return response()->json($res);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailCdrs(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "serviceDetailCdrs");
        $res2 = "";

        try {
            $crds = $service_detail->getServiceCdrs($pbx_service_id, $request->filters);
            $res = [
                "success" => true,
                "response" => ['service_cdrs' => $crds['service_cdrs'], 'totals' => $crds['totals']],
                "message" => "List service detail success",
            ];
            $res2 = [
                "success" => true,
                "response" => ['totals' => $crds['totals']],
                "message" => "List service detail success  - only message",
            ];
        } catch (\Throwable $th) {
            $res = [
                "success" => false,
                "response" => ['service_cdrs' => [], 'totals' => []],
                "message" => $th->getMessage(),
            ];
        }

        LogsDev::finishLog($log, $res2, microtime(true), 'D', "PbxServiceDetailController serviceDetailCdrs");

        if (request()->csv) {
            //return response()
        }

        return response()->json($res);
    }

    public function serviceDetailCdrsWithFilter(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        //TODO: Base metodo con filtros y opciones extras, de momento solo es una idea y no es requerido.
        // limit: int (default 20)
        // type: [inbound,outbound,any]
        // billed: [true,false,any]
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailCdrsInbound(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "serviceDetailCdrs");

        $limit = $request->input('limit', 20);
        $crds = $service_detail->getServiceInboundCdrs($pbx_service_id, $limit);

        $res = [
            "success" => true,
            "response" => ['service_cdrs' => $crds],
            "message" => "List service detail success",
        ];

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailCdrs");

        return response()->json($res);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailCdrsOutbound(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {

        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "serviceDetailCdrs");

        $limit = $request->input('limit', 20);
        $crds = $service_detail->getServiceOutboundCdrs($pbx_service_id, $limit);

        $res = [
            "success" => true,
            "response" => ['service_cdrs' => $crds],
            "message" => "List service detail success",
        ];

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailCdrs");

        return response()->json($res);
    }

    /**
     * @param Request $request
     * @param PbxServices $pbx_service
     * @return JsonResponse
     */
    public function updateServiceStatus(Request $request, $pbx_service_id)
    {
        //Log::debug('PBXSERVICES DETAILCONTROLLER UPDATESERVICESTATUS');
        $pbx_service = PbxServices::findOrFail($pbx_service_id);
        $pbx_service->status = $request->get('status');
        $pbx_service->save();

        // obtener info del pbx package
        $pbx_package = PbxPackages::findOrFail($pbx_service->pbx_package_id);
        // incluir en data del servicio
        $pbx_service->pbx_package = $pbx_package;
        //Log::debug("cambio de estatus");

        //Log::debug($pbx_service->status);

        if ($pbx_service->status === 'A') {
            $controller = new PbxTenantSuspendService();
            if ($pbx_service->suspension_type == "T") {
                $controller = new PbxTenantSuspendService();
                $response = $controller->unsuspend($pbx_service->id);
            } else {
                $controller = new PbxExtensionSuspendService();
                $response = $controller->unsuspend($pbx_service->id);
            }
            // enviar email
            $this->sendEmailToCustomer($pbx_service->customer_id, $pbx_service->company_id, 'reactivation', 'PBX Service Reactivation', $pbx_service);
        }

        if ($pbx_service->status === 'S') {

            if ($pbx_service->auto_suspension == 1 || $pbx_service->auto_suspension == true) {
                if ($pbx_service->suspension_type == "T") {
                    $controller = new PbxTenantSuspendService();
                    $response = $controller->suspend($pbx_service->id);
                } else {
                    $controller = new PbxExtensionSuspendService();
                    $response = $controller->suspend($pbx_service->id);
                }

                //Log::debug($response);
            }
            // enviar email
            $this->sendEmailToCustomer($pbx_service->customer_id, $pbx_service->company_id, 'suspend', 'PBX Service Suspended', $pbx_service);
        }
        if ($pbx_service->status === 'C') {
            // liberar ext y did
            PbxServices::deleteExtensionsAndDids($pbx_service);

            // eliminar con soft delete todo relacionado a PbxServicesAppRate update delete_at
            PbxServicesAppRate::where('pbx_service_id', $pbx_service->id)->delete();

            // Elimina los cargos adicionales
            PbxAdditionalCharge::deleteAdditionalCharges($pbx_service->id);

            // Elimina items
            PbxServicesItems::where('pbx_services_id', $pbx_service->id)
                ->update([
                    'deleted_at' => Carbon::now()->format('Y-m-d'),
                ]);

            // Eliminar prefixrate groups
            DB::table('pbx_services_prefixrate_groups')->where('pbx_service_id', $pbx_service->id)
                ->update([
                    'deleted_at' => Carbon::now()->format('Y-m-d'),
                ]);

            // Eliminar services tax types
            PbxServicesTaxTypes::where('pbx_services_id', $pbx_service->id)
                ->update([
                    'deleted_at' => Carbon::now()->format('Y-m-d'),
                ]);

            // Eliminar services tax types cdrs
            PbxServicesTaxTypesCdr::where('pbx_services_id', $pbx_service->id)
                ->update([
                    'deleted_at' => Carbon::now()->format('Y-m-d'),
                ]);

            // enviar email
            $this->sendEmailToCustomer($pbx_service->customer_id, $pbx_service->company_id, 'cancel', 'PBX Service Cancelled', $pbx_service);
        }

        return response()->json([
            'pbx_service' => $pbx_service,
            'success' => true,
        ]);
    }

    /**
     * Delete Service Pbx
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        //// Log init
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "delete");

        foreach ($request->ids as $id) {
            $service = PbxServices::findOrFail($id);

            // Module log
            LogsModule::createLog(
                "Pbx Services",
                "delete",
                "/pbx/service-detail/delete",
                $id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id,
                "Pbx Service: ".$service->pbx_services_number
            );

            $service->delete(); // Eliminar servicio
        }

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Delete pbx service",
            ],
        ];

        //// Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "Delete pbx service");

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Display a listing of the Service Pbx Items.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailItems(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "serviceDetailItems");

        $limit = $request->input('limit') ?: 10;
        $items = $service_detail->getServiceItems($pbx_service_id, $limit);

        $totals = PbxServicesItems::where('pbx_services_id', $pbx_service_id)
            ->select(\DB::raw('SUM(total) as total_amount, COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->first();

        $res = [
            "success" => true,
            "response" => [
                'service_items' => $items,
                'totals' => $totals,
            ],
            "message" => "List pbx service items success",
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End list pbx service items success");

        // Module log
        LogsModule::createLog(
            "pbx-services",
            "serviceDetailItems",
            "/pbx/service-detail/item/:pbx_service_id",
            $pbx_service_id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "PbxServiceDetailController serviceDetailItems"
        );

        return response()->json($res);
    }

    // <Invoices

    public function invoicesPerServicePbx(Request $request, $pbx_service_id)
    {
        $limit = $request->input('limit', 'all');

        $response = $this->getServicePbxInvoices($pbx_service_id, $limit);

        foreach ($response["data"] as $i => $res) {
            $response["data"][$i]["invoice_date"] = \Carbon\Carbon::parse($res["invoice_date"])->format('Y-m-d');
        }

        return response()->json(["invoices" => $response, "totals" => ["count" => $response['total']]]);
    }

    public function getServicePbxInvoices($pbx_service_id, $limit): array
    {
        $serviceInvoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->join('pbx_services', 'invoices.pbx_service_id', '=', 'pbx_services.id')
            ->select('invoices.*', 'users.name')->where('pbx_service_id', '=', $pbx_service_id)
            ->orderData('invoices', 'id', 'desc')->paginateData($limit);

        $response = $serviceInvoices->toArray();

        return $response;
    }

    // Invoices />

    // <Custom App Rate

    public function customAppRate(Request $request, $pbx_service_id)
    {

        $response = PbxServicesAppRate::where('pbx_service_id', '=', $pbx_service_id)
            ->where('costo', '>', 0)->get();

        $sum_quantity = 0;
        $sum_costo = 0;

        foreach ($response as $res) {
            $sum_quantity += $res['quantity'];
            $sum_costo += $res['costo'];
        }

        return response()->json(["custom_app_rate" => $response,
            "totals" => ["total_quant" => $sum_quantity,
                "total_cost" => $sum_costo],
        ]);

    }

    public function customAppRateForPbxService(Request $request, $pbx_service_id)
    {
        $response = PbxServicesAppRate::where('pbx_service_id', '=', $pbx_service_id)
            ->where('costo', '>', 0)->get();

        return response()->json(["custom_app_rate" => $response]);
    }

    // Custom App Rate/>

    public function getServiceTotal(ServiceDetail $serviceDetail, $pbx_service_id)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog(request(), "", "D", "PbxServiceDetailController", "getServiceTotal");

        $response = $serviceDetail->getServiceTotals($pbx_service_id);

        $res = [
            "success" => true,
            "response" => [
                $response,
            ],
            "message" => "Totals",
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End Service totals success");

        // Module log
        LogsModule::createLog(
            "pbx-services",
            "ServiceDetail",
            "/pbx/service-detail/cdr/:pbx_service_id/totals",
            $pbx_service_id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "PbxServiceDetailController getServiceTotal"
        );

        return $res;

    }

    public function downloadCalls(Request $request)
    {
        $service = PbxServices::findOrFail($request->service_id);

        $exitCode = \Artisan::call('pbx:importCDRs', [
            '--start_date' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->start_date)->format('Y-m-d H:i:s')),
            '--end_date' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->end_date)->format('Y-m-d H:i:s')),
            '--service' => $service->id,
        ]);

        return response()->json([
            'artisan' => \Artisan::output(),
            'exitCode' => $exitCode,
            'success' => true,
        ]);
    }

    // updatePriceExtension
    public function updatePriceExtension(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "updatePriceExtension");

        // pbx_services_extensions_id
        // pbx_services_id
        // price

        // actualizar el precio en la tabla pbx_services_extensions
        DB::table('pbx_services_extensions')
            ->where('id', $request->pbx_services_extensions_id)
            ->update(['price' => $request->price]);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Update price extension",
            ],
        ];

        $extension = DB::table('pbx_services_extensions')->where('id', $request->pbx_services_extensions_id)->first();
        $service = PbxServices::find($extension->pbx_service_id);
        $this->calculatePriceService($service, false);
        // Finish log

        LogsDev::finishLog($log, $res, $time, 'D', "End update price extension");

        // Module log
        LogsModule::createLog(
            "pbx-services",
            "updatePriceExtension",
            "/pbx/service-detail/update-price-extension/:pbx_services_extensions_id",
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "PbxServiceDetailController updatePriceExtension"
        );

        return response()->json($res);
    }

    // updatePriceExtension
    public function updatePriceDid(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "updatePriceDID");

        // pbx_services_extensions_id
        // pbx_services_id
        // price

        // actualizar el precio en la tabla pbx_services_extensions
        DB::table('pbx_services_did')
            ->where('id', $request->pbx_services_did_id)
            ->update(['price' => round($request->price, 2)]);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Updated Price DID",
            ],
        ];

        $did = DB::table('pbx_services_did')->where('id', $request->pbx_services_did_id)->first();
        $service = PbxServices::find($did->pbx_service_id);
        $this->calculatePriceService($service, false);

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End update price did");

        // Module log
        LogsModule::createLog(
            "pbx-services",
            "updatePriceExtension",
            "/pbx/service-detail/update-price-extension/:pbx_services_dids_id",
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "PbxServiceDetailController updatePricedid"
        );

        return response()->json($res);
    }

    public function recalculateTotalsPbxService($id)
    {

        $service = PbxServices::find($id);
        $response = $this->calculatePriceService($service);

        return $response;
    }
}
