<?php

namespace Crater\Http\Controllers\V1\Service;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\CustomerPackage;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxServices;
use Crater\Models\PbxTenant;
use Crater\Traits\SendEmailsTrait;
use DB;
use Illuminate\Http\Request;
//Traits
use Log;
use Throwable;

class ServiceController extends Controller
{
    use SendEmailsTrait;

    /**
     * @param Request $request
     * @param CustomerPackage $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, CustomerPackage $service)
    {
        $time = microtime(true);
        $request->merge(['service' => $service]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "ServiceController", "show");

        $service->load([
            'user',
            'package',
            'items',
            'items.taxes',
            'taxes',
            'discounts',
        ]);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'service' => $service,
                ],
                "message" => "Service show",
            ],
        ];
        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Service show");

        // Module log
        $servicename = "N/A";
        if ($service->package != null) {
            $servicename = $service->package->name;
        }
        LogsModule::createLog(
            "Services",
            "Show",
            "admin/services/:id/view",
            $service->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Service: ".$servicename
        );

        return response()->json([
            'service' => $service,
            'success' => true,
        ]);
    }

    public function invoicesPerService(Request $request, $customer_package_id)
    {
        $limit = $request->input('limit', 'all');

        $response = $this->getServiceInvoices($customer_package_id, $limit);

        foreach ($response["data"] as $i => $res) {
            $response["data"][$i]["invoice_date"] = \Carbon\Carbon::parse($res["invoice_date"])->format('Y-m-d');
        }

        return response()->json(["invoices" => $response, "totals" => ["count" => $response['total']]]);
    }

    public function getServiceInvoices($pbx_service_id, $limit): array
    {
        $serviceInvoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->join('customer_packages', 'invoices.customer_packages_id', '=', 'customer_packages.id')
            ->select('invoices.*', 'users.name')->where('customer_packages_id', '=', $pbx_service_id)
            ->orderData('invoices', 'id', 'desc')->paginateData($limit);

        $response = $serviceInvoices->toArray();

        return $response;
    }

    /**
     * @param Request $request
     * @param CustomerPackage $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CustomerPackage $service)
    {
        $time = microtime(true);
        $request->merge(['service' => $service]);
        $company_id = Auth::user()->company_id;
        // Init log
        $log = LogsDev::initLog($request, "", "D", "ServiceController", "update");
        // $service = CustomerPackage::updateCustomerPackage($request, $service);
        // obtener info del pbx package
        // $package = Packages::findOrFail($service->package_id);
        // incluir data
        // $service['packageCustomer'] = $package;
        //Log::debug("Linea 118");
        //Log::debug($service);
        $prevStatus = $service->status;
        $service = CustomerPackage::updateCustomerPackage($request, $service);

        try {
            // evaluar status actualizado
            if ($service->status === 'A' && $prevStatus === 'S') {
                $this->sendEmailToCustomer($service->customer_id, $company_id, 'reactivation', 'Service Reactivation', $service);

            }
            if ($service->status === 'P' && $prevStatus === 'A') {
                $this->sendEmailToCustomer($service->customer_id, $company_id, 'create', 'Service Activation', $service);

            }
            if ($service->status === 'S') {
                // enviar email
                $this->sendEmailServices($service->customer_id, $company_id, 'suspend', 'Service Suspended', $service);
            }
            if ($service->status === 'C') {
                // enviar email
                $this->sendEmailServices($service->customer_id, $company_id, 'cancel', 'Service Cancelled', $service);
            }

        } catch (Throwable $e) {
            // return response()->json(['message' => $e->getMessage()]);
            //Log::debug('---- error email update service ---');
            //Log::debug($e);
        }

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'service' => $service,
                    'success' => true,
                ],
                "message" => "Actualizacion de un servicio",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion de un servicio");

        // Module log
        LogsModule::createLog(
            "Services",
            "Update",
            "admin/services/:id/edit",
            $service->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Service: ".$service->package->name
        );

        return response()->json([
            'service' => $service,
            'success' => true,
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        //// Log init
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ServiceController", "delete");

        CustomerPackage::deleteCustomerPackage($request->ids);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Delete service",
            ],
        ];

        //// Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "Delete service");

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function servicesAll(Request $request)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "ServiceController", "servicesAll");

        // FILTERS
        // Service Nº   : Aqui debe ir el numero de servicio y debe ser clickeable que te redirija a la vista del servicio
        // Customer     : Aqui debe ir el nombre del customer, y debe ser clickeable que te redirija a la vista del customer.
        // tipo         : Aqui debe mostrar normal si es un servicio normal y pbx si es un servicio pbx.
        // status       : Indicar el status del servicio: A, S, P, C
        // Activation   : date fecha de activacion
        // Renewal      : date fecha de renovacion.

        $limit = $request->limit ?? 10;
        // unir dos tablas en la misma consulta
        $fieldsServices = [
            'id',
            'code as service_number',
            'package_id',
            'customer_id',
            'status',
            'activation_date',
            'renewal_date',
            'created_at',
        ];
        $fieldsPbxServices = [
            'id',
            'pbx_services_number as service_number',
            'pbx_package_id',
            'customer_id',
            'status',
            'date_begin as activation_date',
            'renewal_date',
            'created_at',
        ];

        $userRole = $request->user()->role;
        $userId = $request->user()->id;

        $services = CustomerPackage::select($fieldsServices)
            ->where('company_id', $request->header('company'))
            ->addSelect(DB::raw("'NORMAL' as type"))
            ->when($userRole == 'customer', function ($query) use ($userId) {
                return $query->where('customer_id', $userId);
            })
            ->when($request->has('service_number'), function ($query) use ($request) {

                if ($request->service_number) {

                    return $query->where('code', 'like', '%'.$request->service_number.'%');
                }
            })
            ->when($request->has('name_customer'), function ($query) use ($request) {
                return $query->whereHas('user', function ($query) use ($request) {

                    return $query->where('name', 'like', '%'.$request->name_customer.'%');
                });
            })
            ->when($request->has('type'), function ($query) use ($request) {
                if ($request->type && $request->type !== 'NORMAL') {

                    return $query->whereNull('created_at');
                }
            })
            ->when($request->has('status'), function ($query) use ($request) {

                if ($request->status) {

                    return $query->where('status', $request->status);
                }
            })
            ->when($request->has('activation_date'), function ($query) use ($request) {

                if ($request->activation_date) {

                    return $query->where('activation_date', 'like', '%'.$request->activation_date.'%');
                }
            })
            ->when($request->has('tenant'), function ($query) use ($request) {
                //Log::debug("jpapon");
                //Log::debug($request->tenant);

                $arr = $request->tenant;
                \Log::debug(gettype($arr));
                if ($arr != "{}" && $arr != null) {

                    return $query->whereNull('created_at');
                }
            })
            ->when($request->has('renewal_date'), function ($query) use ($request) {

                if ($request->renewal_date) {

                    return $query->where('renewal_date', $request->renewal_date);
                }
            });

        $pbx_services = PbxServices::select($fieldsPbxServices)
            ->where('company_id', $request->header('company'))
            ->addSelect(DB::raw("'PBX' as type"))
            ->when($userRole == 'customer', function ($query) use ($userId) {
                return $query->where('customer_id', $userId);
            })
            ->when($request->has('service_number'), function ($query) use ($request) {
                return $query->where('pbx_services_number', 'like', '%'.$request->service_number.'%');
            })
            ->when($request->has('name_customer'), function ($query) use ($request) {
                return $query->whereHas('user', function ($query) use ($request) {
                    return $query->where('name', 'like', '%'.$request->name_customer.'%');
                });
            })
            ->when($request->has('type'), function ($query) use ($request) {
                if ($request->type && $request->type !== 'PBX') {
                    $query->whereNull('created_at');
                }
            })
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->has('activation_date'), function ($query) use ($request) {
                if ($request->activation_date) {
                    return $query->where('date_begin', $request->activation_date);
                }
            })
            ->when($request->has('tenant'), function ($query) use ($request) {
                //Log::debug("jpapon");
                //Log::debug($request->tenant);
                $arr = $request->tenant;
                //Log::debug( gettype($arr) );
                if ($arr != "") {
                    //Log::debug("jpapon");
                    $manage = json_decode($request->tenant);

                    $obj = PbxTenant::where("tenantid", $manage->tenantid)->where("pbx_server_id", $manage->pbx_server_id)->get()->pluck("id");
                    if (count($obj)) {
                        //Log::debug($obj);
                        return $query->whereIN('pbx_tenant_id', $obj);
                    }
                }
            })
            ->when($request->has('renewal_date'), function ($query) use ($request) {
                if ($request->renewal_date) {
                    return $query->where('renewal_date', $request->renewal_date);
                }
            });

        $services = $services->union($pbx_services)
            ->with('user')
            ->orderBy($request->orderByField, $request->orderBy)
            ->paginate($limit);

        // Finish log
        LogsDev::finishLog($log, $services, $time, 'D', "Services All");

        // Module log
        LogsModule::createLog(
            "Services",
            "All",
            "admin/services/all",
            "",
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Services All"
        );

        return response()->json([
            'services' => $services,
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

}
