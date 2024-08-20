<?php

namespace Crater\Http\Controllers\V1\Customer;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Estimate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CustomerEstimateController extends Controller
{
    public function index(Request $request)
    {
        try {
            $customer = Auth::user();
            //$customer = User::find(4);
            $time = microtime(true);
            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "getEstimates");

            $limit = $request->has('limit') ? $request->limit : 10;

            $estimates = Estimate::where('company_id', $customer->company_id)
                ->where('user_id', $customer->id)
                ->where('status', "!=", "DRAFT")
                ->when($request->filled('status'), function (Builder $query) use ($request) {
                    return $query
                        ->when($request->status == 'PENDING', function ($q) {
                            return $q->where('estimate_date', '>', Carbon::now());
                        })
                        ->when($request->status != 'PENDING', function ($q) use ($request) {
                            return $q->where('status', $request->status);
                        });
                })
                ->when($request->filled('estimate_number'), function (Builder $query) use ($request) {
                    return $query->where('estimate_number', 'LIKE', '%'.$request->get('estimate_number').'%');
                })
                ->when($request->get('search'), function (Builder $query) use ($request) {
                    return $query->where(function (Builder $query) use ($request) {
                        return $query->where('estimate_number', 'LIKE', '%'.$request->get('search').'%')
                            ->orWhereHas('user', function (Builder $query) use ($request) {
                                $query->where('name', 'LIKE', '%'.$request->get('search').'%');
                            });
                    });
                })

                ->when($request->from_date, function ($query) use ($request) {
                    return $query->where('estimate_date', '>=', $request->from_date);
                })
                ->when($request->to_date, function ($query) use ($request) {
                    return $query->where('estimate_date', '<=', $request->to_date);
                })
                ->with('user')
                ->orderBy(
                    $request->input('orderByField', 'created_at'),
                    $request->input('orderBy', 'desc')
                )->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'estimates' => $estimates,
                        'success' => true,
                    ],
                    "message" => "Lista de estimados asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de estimados asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Profile Customer",
                "Dashboard - Estimates",
                "estimates",
                0,
                $customer->name,
                $customer->email,
                $customer->role,
                $customer->company_id
            );

            return response()->json([
                'estimates' => $estimates,
                'success' => true,
                'status' => 200,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
