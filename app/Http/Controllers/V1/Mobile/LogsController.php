<?php

namespace Crater\Http\Controllers\V1\Mobile;

use Auth;
// use Crater\Models\MobileSettings;
use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\MobileLoginLogs;
use Crater\Models\User;
use Illuminate\Http\Request;
use Log;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "LogsController", "index");
        // $usercurrent = User::where("id", Auth::id())->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        // $mobileLoginLogs = MobileLoginLogs::select('*')->leftJoin('users', 'mobile_login_logs.customer_id', 'users.id')->paginateData($limit);

        $mobileLoginLogs = MobileLoginLogs::with('customer')->select('mobile_login_logs.*')
        ->applyFilters($request->only([
            'customer',
            'session_start',
            'operating_system',
            'orderByField',
            'orderBy'
        ]))->latest()->paginateData($limit);

        //Log::debug($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'mobileLoginLogs' => $mobileLoginLogs,
                ],
                "message" => "Mobile Login Logs"
            ]
        ];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "List", "admin/mobile/logs/login/", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'mobileLoginLogs' => $mobileLoginLogs,
        ]);
    }

    public function indexCustomersMessaging(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "LogsController", "index");
        $usercurrent = User::where("id", Auth::id())->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $mobileMessagingCustomers = User::select('users.*')->distinct('users.id')
        ->Join('mobile_login_logs', 'users.id', 'mobile_login_logs.customer_id')->whereNotNull('mobile_login_logs.firebase_code')->where('users.role', 'customer')->orWhere('users.authentication', 1)
        ->applyFilters($request->only([
            'display_name',
            'contact_name',
            'phone',
            'status_customer',
            'customer_id',
            'orderByField',
            'orderBy'
        ]))->latest()->paginate($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'mobileMessagingCustomers' => $mobileMessagingCustomers,
                ],
                "message" => "Mobile Messaging Customers"
            ]
        ];
        LogsDev::finishLog($log, $res, $time, 'D', "Mobile Messaging Customers index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Mobile settings", "List", "admin/mobile/messaging/customers/", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'mobileMessagingCustomers' => $mobileMessagingCustomers,
        ]);
    }

    public function store(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "LogsController", "index");
        $usercurrent = User::where("id", Auth::id())->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo


        $token = $request->input('jwt');
        // $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzeXN0ZW1fdmVyc2lvbiI6IjcuMCIsImRldmljZV90eXBlIjoiSGFuZHNldCIsInNlcmlhbF9udW1iZXIiOiJMR00xNTBmNmIxYzE1IiwiaXNfdGFibGV0IjpmYWxzZSwibWFjX2FkZHJlc3MiOiIwMDo1NzpDMTpDNjozRTpGRiIsImN1c3RvbWVyX2lkIjoyMi4wLCJzZXNzaW9uX3N0YXJ0IjoiMjAyMi0wOC0wMyAxMDozOToxNSIsImRldmljZV9pZCI6Im1zbTg5MDkiLCJhcGlfbGV2ZWwiOjI0LjAsImZjbVRva2VuIjoiZjBzQUxNV3FRSy1rclVMM0ZJREFVOTpBUEE5MWJIWWdfdnVzM1ZVbElwSTd3VXcweklQdVVSX2xOZkF6QVgzMzdNS2FoV095ZGdlN0pBQkZBcF9pWWlFNEZyRDFTbHR5TUhYNlFyYndycWFweDVSbGItM2hjaFBtdzFpb3lnWVltTlJZaVNodEVJY0tyUVpmTXNXSjVtQ1NycGhmdko0d2FGVSIsImJyYW5kIjoibGdlIiwic3lzdGVtX25hbWUiOiJBbmRyb2lkIiwidW5pcXVlX2lkIjoiOWNlMzdmZjM0MDgyNDA4NSIsImRldmljZV9uYW1lIjoiUGhvZW5peCAzIiwibWFudWZhY3R1cmVyIjoiTEdFIn0.X5ZQVZdNUVH7MGxwcCs1fSazkoD4JMX7zj-wB2Kroq0';

        $tokenParts = explode(".", $token);
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        // $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);
        // var_dump([$jwtPayload]);
        $data = json_decode(json_encode($jwtPayload), true);

        $data['firebase_code'] = $data['fcmToken'];

        $mobile_login_log = MobileLoginLogs::create($data);

        if ($mobile_login_log && $data['customer_id']) {
            $user = User::find($data['customer_id']);
            $user->firebase_code = $data['fcmToken'];
            $user->save();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'mobile_login_log' => $mobile_login_log,
        ], "message" => "LogsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "LogsController store");
        /////////////////////////////////////////

        return response()->json([
            'mobile_login_log' => $mobile_login_log,
            'success' => true,
            'message' => 'Mobile Login log saved successfully',
        ]);
    }
}
