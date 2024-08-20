<?php

namespace Crater\Traits;

//models

use Carbon\Carbon;
use Crater\Models\PushNotificationsLogs;
//
use Log;

trait PushNotificationsTrait
{
    public $url = "https://fcm.googleapis.com/fcm/send";

    public $serverApiKey = "AAAAwDIT6_I:APA91bENVmlAvkjT371HSL95HLl3XIM3t2dP3mjvnrJ91lMR-DLsyYQYf2RvMIouljxJQTTENCrtKsKSetjXYQ9Ie5ZVvUHNpWwcHE892W20A5Kw1TVUL1YUwg6b_gI45m3NRCUuJePR";

    public function sendNotification($sendTo, $data)
    {
        //Log::debug($data);

        $headers = [
          "Authorization: key=".$this->serverApiKey,
          "Content-Type: application/json"
        ];

        // Notification's Content
        $notificationContent = [
          "title" => $data['title'],
          "body" => $data['text'],
          "image" => $data['image']
          // "click_action" => "",
        ];

        // Optional
        $dataPayload = [
          "to" => "VIP",
          "date" => Carbon::now(),
          "other_data" => "another data",
          "x" => "data"
        ];

        $notificationBody = [
          "notification" => $notificationContent,
          "data" => $dataPayload, //Datapayload is optional
          "time_to_live" => 3600, //optional. Time in seconds. Max 4 weeks
          //"to" => $sendTo // Token or Reg_id individual
          "registration_ids" => $sendTo // an array of registry_ids
        ];

        $bSent = true;
        $date = Carbon::now();
        $log_message = 'Push notification sended successfully';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notificationBody));

            // Execute
            curl_exec($ch);

            curl_close($ch);

        } catch (\Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            $log_message = $e->getMessage();
            $bSent = false;

        }




        /*  $table->unsignedBigInteger('customer_id')->nullable();
         $table->timestamp('date')->nullable();
         $table->string('message', 255)->nullable();
         $table->boolean('notification_sent')->nullable();
         $table->string('log_message', 255)->nullable();
         $table->json('notification_data')->nullable();
*/
        $this->saveNotificationLog($data['customer_id'], $date, $data['text'], $bSent, $log_message, $data);
    }

    public function saveNotificationLog($customer_id, $date, $message, $notification_sent, $log_message, $notification_data)
    {
        // llenar objeto email log y almacenarlo
        $notificationLog['customer_id'] = $customer_id;
        $notificationLog['date'] = $date;
        $notificationLog['message'] = $message;
        $notificationLog['notification_sent'] = $notification_sent;
        $notificationLog['log_message'] = $log_message;

        // $dataEmail[] = $emailLog;
        //Log::debug('data push -----');
        //Log::debug($notification_data);


        //Log::debug('var dump: ');

        //Log::debug(json_encode($notification_data));


        $notificationLog['notification_data'] = json_encode($notification_data, JSON_FORCE_OBJECT);

        PushNotificationsLogs::create($notificationLog);

        return;
    }
}
