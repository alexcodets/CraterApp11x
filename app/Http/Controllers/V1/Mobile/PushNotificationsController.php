<?php

namespace Crater\Http\Controllers\V1\Mobile;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PushNotificationsLogs;
use Crater\Models\User;
use Crater\Traits\PushNotificationsTrait;
// traits
use Illuminate\Http\Request;
use Log;

class PushNotificationsController extends Controller
{
    use PushNotificationsTrait;

    public function indexLogs(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PushNotificationsController", "indexLogs");

        $limit = $request->has('limit') ? $request->limit : 10;

        $notificationsLogs = PushNotificationsLogs::with('customer')->select('*')->applyFilters($request->only([
            'customer',
            'date',
            'message',
            'orderByField',
            'orderBy'
        ]))->latest()->paginate($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'notificationsLogs' => $notificationsLogs,
        ], "message" => "Listado de push notifications"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado de push notifications");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("push notifications", "List", "/mobile/logs/notifications/list", Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'notificationsLogs' => $notificationsLogs,
            'success' => true
        ]);
    }

    public function sendToDevice(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PushNotificationsController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $sendTo = $request->input('fcm_token');

        // validar tipo de variable si es array se envia notificacion a varios dispositivos si es string a solo uno.
        if (gettype($sendTo) == 'array') {
            foreach ($sendTo as $key => $value) {
                $fcm_token = User::select('firebase_code')->where('id', $value)->get();
                //Log::debug($fcm_token);
                if ($fcm_token[0]->firebase_code) {
                    // set customer_id
                    $request['customer_id'] = $value;
                    // PushNotificationsTrait method
                    $this->sendNotification([$fcm_token[0]->firebase_code], $request);
                }
            }
        } else {
            $user = User::select('id')->where('firebase_code', $sendTo)->first();
            //Log::debug('user-----------');
            //Log::debug($user);
            $request['customer_id'] = $user->id;
            // PushNotificationsTrait method
            $this->sendNotification([$sendTo], $request->all());
        }

        //Log::debug($request);




        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'notification' => 'success',
                ],
                "message" => "Mobile Login Logs"
            ]
        ];
        LogsDev::finishLog($log, $res, $time, 'D', "Push Notifications index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Push Notifications", "List", "admin/mobile/logs/login/", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Push Notification Sended Successfully'
        ]);
    }
}
