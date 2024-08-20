<?php

namespace Crater\Http\Controllers\V1\Customer;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\CustomerConfig;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\User;
use Illuminate\Http\Request;
use Throwable;

class CustomerConfigController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setConfig(Request $request)
    {
        try {
            if($request['customer_id'] > 0) {
                $customer = User::findOrFail($request['customer_id']);
            }

            $time = microtime(true);
            $request->merge([
                'company_id' => $request->header('company'),
                'creator_id' => Auth::id()
            ]);


            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomerConfigController", "setConfig");

            $config = CustomerConfig::updateOrCreate(
                [
                    'customer_id' => $request['customer_id'],
                    'company_id' => $request->header('company'),
                ],
                $request->all()
            );

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'config' => $config,
                        'success' => true,
                    ],
                    "message" => "Customer config"
                ]
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Customer config");

            // Module log
            LogsModule::createLog(
                "CustomerConfig",
                "setConfig",
                "admin/customers/set-config",
                $config->id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'config' => $config,
                'success' => true,
            ]);

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getConfig(Request $request, $customer_id)
    {
        $time = microtime(true);

        $config = CustomerConfig::where('company_id', $request->header('company'))
            ->where('customer_id', $customer_id)
            ->first();

        if (! $config) {
            return response()->json(['message' => 'Config not found']);
        }

        $request->merge(['config' => $config]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "CustomerConfigController", "getConfig");

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'config' => $config,
                ],
                "message" => "Get customer config"
            ]
        ];
        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Get customer config");

        // Module log
        LogsModule::createLog(
            "Services",
            "Show",
            "admin/services/:id/view",
            $config->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id
        );

        return response()->json([
            'config' => $config,
            'success' => true,
        ]);
    }

    public function customerConfig(Request $request)
    {
        try {
            $time = microtime(true);
            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomerConfigController", "customerConfig");

            $config = new CustomerConfig($request->request->all());
            $config->save();

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'config' => $config,
                        'success' => true,
                    ],
                    "message" => "Customer config"
                ]
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Customer config");

            // Module log
            LogsModule::createLog(
                "CustomerConfig",
                "customerConfig",
                "admin/customers/set-config",
                $config->id,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'config' => $config,
                'success' => true,
            ]);

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
