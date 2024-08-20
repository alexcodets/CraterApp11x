<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomerServiceIndexRequest;
use Crater\Models\CustomerPackage;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\User;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CustomerServiceController extends Controller
{
    public function index(CustomerServiceIndexRequest $request): \Illuminate\Http\JsonResponse
    {

        try {
            $time = microtime(true);
            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "getServices");

            $customer = $request->user_id ? User::findOrFail($request->user_id) : Auth::user();

            $limit = $request->has('limit') ? $request->limit : 10;

            $packages = CustomerPackage::where('company_id', $customer->company_id)
                ->where('customer_id', $customer->id)
                ->when($request->service_number, function ($query) use ($request) {
                    return $query->where('code', 'like', '%'.$request->service_number.'%');
                })
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('code', 'like', '%'.$request->search.'%');
                })
                ->when($request->status, function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->when($request->date, function ($query) use ($request) {
                    $query->when($request->from_date, function ($query) use ($request) {
                        return $query->where($request->date, '>=', $request->from_date);
                    });
                    $query->when($request->to_date, function ($query) use ($request) {
                        return $query->where($request->date, '<=', $request->to_date);
                    });
                })
                ->with(['package', 'user'])
                ->paginateData($limit);

            $res = [
                "success" => true,
                'code' => 200,
                "response" => [
                    "datamesage" => [
                        'packages' => $packages,
                        'success' => true,
                    ],
                    "message" => "Lista de paquetes asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de paquetes asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Profile Customer",
                "Dashboard - services",
                "services",
                0,
                $customer->name,
                $customer->email,
                $customer->role,
                $customer->company_id
            );

            return response()->json([
                'packages' => $packages,
                'success' => true,
            ]);

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

    }
}
