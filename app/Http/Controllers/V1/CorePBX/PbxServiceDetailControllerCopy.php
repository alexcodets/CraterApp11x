<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\CallHistoryIndi;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesItems;
use Crater\Models\ProfileDidCustomDidGroups;
use Crater\Models\ProfileDidTollFree;
use Crater\Models\TollFreeCustomDidGroup;
use Crater\Pbxware\Service\PbxTenantSuspendService;
use Crater\Pbxware\Service\ServiceDetail;
use Crater\Traits\SendEmailsTrait;
//Traits
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PbxServiceDetailControllerCopy extends Controller
{
    use SendEmailsTrait;

    /**
     * @param Request $request
     * @param PbxServices $service
     * @return JsonResponse
     */
    public function getService(Request $request, $pbx_service_id)
    {
        $time = microtime(true);

        /* @var $service Crater\Models\PbxServices  */
        /* @var Crater\Models\PbxServices $service   */
        $service = PbxServices::findOrFail($pbx_service_id);

        $request->merge(['service' => $service]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "getService");

        $service->load([
            'user',
            'pbxPackage',
            "pbxServiceTaxTypes",
            "pbxPackage.server",
            "pbxPackage.profileDid",
            "pbxPackage.profileDid2",
            "pbxPackage.profileExtensions",
            "tenant",
            "getItems.taxes",
            "pbxPackage.profileDid.aditionalChargesA",
            "pbxPackage.profileExtensions.aditionalChargesA",
            "pbxServiceExtensions.extension",
            "pbxServiceDids.did",
            "getCallDetailRegisterTotal",
            "taxes",
        ]);

        $vari = $service->toArray();
        //Calculos para Invoices Pxb

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

        }

        $CountServicesExtension = count($vari['pbx_service_extensions']);
        $TotalServicesExtension = $CountServicesExtension * $priceextension;
        $TotalServicesDid = 0;
        $CountServicesDid = count($vari['pbx_service_dids']);
        $return_did = [];
        if ($CountServicesDid > 0) {
            $custom_did_id = [];
            $return_did = [];

            foreach ($vari['pbx_service_dids'] as $item) {
                $custom_did_id[] = $item['custom_did_id'] ? $item['custom_did_id'] : 0;
            }

            $collection = collect($custom_did_id);

            $counted_collection = $collection->countBy();
            $resul = $counted_collection->toArray();

            foreach ($resul as $key => $value) {
                $resul = 0;
                if ($key > 0) {
                    $buscador = ProfileDidTollFree::find($key);
                    $resul = $buscador['rate_per_minute'] * $value;
                    $TotalServicesDid += $resul;
                    $return_did[] = [$key, $value, round(floatval($buscador['rate_per_minute']), 2), $resul, $vari['pbx_package']['profile_did']['name']];
                } else {
                    $resul = $pricedid * $value;
                    $TotalServicesDid += $resul;
                    $return_did[] = [$key, $value, round($pricedid, 2), $resul, $vari['pbx_package']['profile_did']['name']];
                }
                /* print_r("The index is = " . $key . ", and value is = ". $value); */
            }
        }

        /* $TotalServicesDid = $CountServicesDid * $pricedid; */

        $contador = 0;
        $contadoraddi = 0;
        if ($vari['pbx_package']['profile_extensions'] != null) {
            foreach ($vari['pbx_package']['profile_extensions']['aditional_charges_a'] as $total) {
                $contador += $total['amount'];
            }
            $contadoraddi = count($vari['pbx_package']['profile_extensions']['aditional_charges_a']);
        }
        /* $addicionalExtension = $contador *  $contadoraddi ; */
        $addicionalExtension = $contador * $CountServicesExtension;

        $contador1 = 0;
        $contadoraddi1 = 0;
        if ($vari['pbx_package']['profile_did'] != null) {
            foreach ($vari['pbx_package']['profile_did']['aditional_charges_a'] as $total) {
                $contador1 += $total['amount'];
            }
            $contadoraddi1 = count($vari['pbx_package']['profile_did']['aditional_charges_a']);
        }
        /* $addicionalDid = $contador1 *$contadoraddi1 ; */
        $addicionalDid = $contador1 * $CountServicesDid;
        $TotalAddicionalCharges = $addicionalExtension + $addicionalDid;

        $TotalCdrBilling = 0;
        $TotalDuration = 0;
        foreach ($vari['get_call_detail_register_total'] as $total) {
            $TotalDuration += $total['total_duration'];
            $TotalCdrBilling += $total['exclusive_cost'];
        }

        /* //Log::debug("return_did: " . $return_did);
        //Log::debug("return_did: ///////////////////////////////////////////////"); */

        $service->CountServicesExtension = $CountServicesExtension;
        $service->CountServicesDid = $CountServicesDid;
        $service->TotalServicesExtension = $TotalServicesExtension;
        $service->TotalServicesDid = $TotalServicesDid;
        $service->addicionalExtension = $addicionalExtension;
        $service->addicionalDid = $addicionalDid;
        $service->TotalAddicionalCharges = $TotalAddicionalCharges;
        $service->TotalCdrBilling = $TotalCdrBilling;
        $service->TotalDuration = $TotalDuration;
        $service->InfoDid = $return_did;

        /// totales sin facturar
        $cdr_total = $service->pbxCdrTotalsCurrent()->get();

        $cdrTotals = DB::table('call_detail_register_totals')->select(DB::raw('(exclusive_cost - exclusive_cost_paid) as amount_due'), )->where('pbx_services_id', 1)->get();

        //DB::table('call_detail_register_totals')->selectRaw('(exclusive_cost - exclusive_cost_paid) as amount_due')->select(['id','amount_due'])->where('pbx_services_id', 1)->get();



        $totals = CallDetailRegisterTotal::toBase()
            ->selectRaw("sum(case when exclusive_cost > exclusive_cost_paid then exclusive_cost - exclusive_cost_paid end) as unpaid_debt")
            ->selectRaw("sum(case when 1 > 0 then exclusive_cost end) as debt")
            ->selectRaw("sum(case when 2 = 2 then exclusive_cost_paid end) as paid_debt")->first();

        CallDetailRegisterTotal::toBase()->selectRaw("sum(case when exclusive_cost > exclusive_cost_paid then exclusive_cost - exclusive_cost_paid end) as unpaid_debt")->selectRaw("sum(case when 1 > 0 then exclusive_cost end) as debt")->selectRaw("sum(case when 2 = 2 then exclusive_cost_paid end) as paid_debt")->first();



        //total de lo adeudado de todo el servicio
        $cdr_total2 = DB::table('call_detail_register_totals')->selectRaw('(exclusive_cost - exclusive_cost_paid) as amount_due')->where('pbx_services_id', '=', $pbx_service_id)->whereColumn('exclusive_cost', '>', 'exclusive_cost_paid')->get();
        //total de lo adeudado de todo el servicio
        $cdr_total3 = DB::table('call_detail_register_totals')->select('*')->where('pbx_services_id', '=', $pbx_service_id)->whereNull("invoice_id")->get('id')->pluck('id')->toArray();
        //total de lo adeudado de todo el servicio
        $cdr_total4 = DB::table('call_detail_register_totals')->selectRaw('(exclusive_cost) as amount_due')->where('pbx_services_id', '=', $pbx_service_id)->whereColumn('exclusive_cost', '=', 'exclusive_cost_paid')->whereNull("invoice_id")->get();

        $total_deb = $cdr_total2->sum('amount_due');
        $total_consume = $cdr_total->sum('exclusive_cost');
        $total_cosnsume_paid = $cdr_total->sum('exclusive_cost_paid');

        //se quiere que en las variables $total_consume  y $paid_consume  esten incluido los impuestos.

        $paid_consume = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $cdr_total3)->sum('amoutbruto');
        $pain_consume = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $cdr_total3)->sum('amout');
        ;
        $taxconsume = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $cdr_total3)->sum('taxamount');
        ;

        $total_consume = $total_consume + $taxconsume;
        $paid_consume = $paid_consume + $taxconsume;

        if ($paid_consume >= $total_consume) {
            $total_consume = $paid_consume;
        }
        $unpaid_consume = $total_consume - $paid_consume;

        $service->total_deb = $total_deb;
        $service->total_consume = $total_consume;
        $service->paid_consume = $paid_consume;
        $service->unpaid_consume = $unpaid_consume;

        if ($service->total_deb == 0) {
            $service->unpaid_consume = $service->total_deb;
            $service->paid_consume = $service->total_consume;
        }

        $res = [
            "success" => true,
            "response" => ['pbx_service' => $service],
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

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param ServiceDetail $service_detail
     * @param $pbx_service_id
     * @return JsonResponse
     */
    public function serviceDetailExtensions(Request $request, ServiceDetail $service_detail, $pbx_service_id)
    {
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailController", "serviceDetailExtensions(");
        $limit = $request->input('limit', 'all');

        $extensions = $service_detail->getServiceExtensions($pbx_service_id, $limit);

        $res = [
            "success" => true,
            "response" => ['service_extensions' => $extensions['data'], 'totals' => $extensions['totals']],
            "message" => "List service detail success",
        ];

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailExtensions");

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

        try {
            $crds = $service_detail->getServiceCdrs($pbx_service_id);
            $res = [
                "success" => true,
                "response" => ['service_cdrs' => $crds['service_cdrs'], 'totals' => $crds['totals']],
                "message" => "List service detail success",
            ];
        } catch (\Throwable $th) {
            $res = [
                "success" => false,
                "response" => ['service_cdrs' => [], 'totals' => []],
                "message" => $th->getMessage(),
            ];
        }

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailCdrs");

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
            $response = $controller->unsuspend($pbx_service->id);
            // enviar email
            $this->sendEmailToCustomer($pbx_service->customer_id, $pbx_service->company_id, 'reactivation', 'PBX Service Reactivation', $pbx_service);
        }

        if ($pbx_service->status === 'S') {

            if ($pbx_service->auto_suspension == 1 || $pbx_service->auto_suspension == true) {
                $controller = new PbxTenantSuspendService();
                $response = $controller->suspend($pbx_service->id);
                //Log::debug($response);
            }
            // enviar email
            $this->sendEmailToCustomer($pbx_service->customer_id, $pbx_service->company_id, 'suspend', 'PBX Service Suspended', $pbx_service);
        }
        if ($pbx_service->status === 'C') {
            // liberar ext y did
            PbxServices::deleteExtensionsAndDids($pbx_service);

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
}
