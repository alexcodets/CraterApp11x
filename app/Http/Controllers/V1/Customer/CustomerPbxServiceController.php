<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomerPbxServiceIndexRequest;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Log;
use Throwable;

class CustomerPbxServiceController extends Controller
{
    public function index(CustomerPbxServiceIndexRequest $request): JsonResponse
    {
        //return response()->json($request);
        // Log::debug($request);
        try {
            //Log::debug('GetPbx');
            $time = microtime(true);
            // Init Log
            $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "getPbxServices");

            // $customer = Auth::user();
            $customer = $request->user_id ? User::findOrFail($request->user_id) : Auth::user();

            $limit = $request->has('limit') ? $request->limit : 10;

            //to_date, from_date
            $pbx_services = PbxServices::where('pbx_services.company_id', $customer->company_id)
            ->join('pbx_packages', 'pbx_services.pbx_package_id', '=', 'pbx_packages.id')
            ->select('pbx_services.*', 'pbx_packages.pbx_package_name')
                ->where('pbx_services.customer_id', $customer->id)
                ->when($request->service_number, fn ($query) => $query->where('pbx_services.pbx_services_number', 'like', '%'.$request->service_number.'%'))
                ->when($request->search, fn ($query) => $query->where('pbx_services.pbx_services_number', 'like', '%'.$request->search.'%'))
                ->when($request->status, fn ($query) => $query->where('pbx_services.status', $request->status))

                ->when($request->date, function ($query) use ($request) {
                    $query->when($request->from_date, function ($query) use ($request) {
                        return $query->where($request->date, '>=', $request->from_date);
                    });
                    $query->when($request->to_date, function ($query) use ($request) {
                        return $query->where($request->date, '<=', $request->to_date);
                    });
                })
                ->with([ 'user', 'pbxServiceExtensions', 'pbxServiceDids'])
                ->applyFilters($request->only([
                    'orderByField',
                    'orderBy',
                ]))
                ->paginateData($limit);

            $res = [
                "success" => true,
                'code' => 200,
                "response" => [
                    "datamesage" => [
                        "pbxServices" => $pbx_services,
                        "success" => true,
                    ],
                    "message" => "Servicios PBX asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, "D", "Fin Servicios PBX asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Profile Customer",
                "Dashboard - Pbx Services",
                "pbx_services",
                0,
                $customer->name,
                $customer->email,
                $customer->role,
                $customer->company_id
            );

            return response()->json([
                "pbxServices" => $pbx_services,
                "success" => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
