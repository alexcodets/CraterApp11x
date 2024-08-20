<?php

namespace Crater\Services\Pbx;

use Carbon\Carbon;
use Crater\DataObject\AddressDO;
use Crater\Mail\PbxServerNotification;
use Crater\Models\Company;
use Crater\Models\PbxServers;
use Crater\Models\ScheduleLog;
use Crater\Models\User;
use Crater\Pbxware\PbxWareApi;
use Crater\Traits\SendEmailsTrait;
use Exception;
use Mail;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PbxCheckConnectionService
{
    use SendEmailsTrait;

    /**
     * @param PbxServers $pbxServer
     * @return void
     * @throws Exception
     */
    public function validateServer(PbxServers $pbxServer): void
    {
        if (is_null($pbxServer->hostname)) {
            throw new Exception('Hostname for pbxServer is required', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_null($pbxServer->api_key)) {
            throw new Exception('ApiKey for pbxServer is required', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_null($pbxServer->ssl_port)) {
            throw new Exception('ssl_port for pbxServer is required', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    /**
     * @throws Exception
     */
    public function checkServer(PbxServers $pbxServer): array
    {
        $this->validateServer($pbxServer);
        $api = new PbxWareApi($pbxServer);

        return $api->checkConnection();
    }

    /**
     * @throws Exception
     */
    public function checkLicense(PbxServers $pbxServer): array
    {
        $this->validateServer($pbxServer);
        $api = new PbxWareApi($pbxServer);

        return $api->getLicenseInfo();

    }

    private function serverUp(PbxServers $pbxServer)
    {

        if ($pbxServer->status == PbxServers::STATUS_ACTIVE) {
            return;
        }

        $pbxServer->status = PbxServers::STATUS_ACTIVE;
        $pbxServer->save();
        //Log::debug("Server {$pbxServer->id} is active");

        $log = $pbxServer->downLogs()->first();
        if (is_null($log)) {

            $extraData = [
                'server_name' => $pbxServer->server_label,
                'down' => [
                    'mail' => ['sent' => false],
                ],
                'time' => [
                    'down' => $pbxServer->created_at,
                ],
                'errors' => [
                    'reason_down' => __('comandos.checkConnection.errors.no_log.up')
                ],
            ];

            $pbxServer->scheduleLogs()->create([
                'module_name' => 'PbxCheckConnectionService',
                'message' => __('comandos.checkConnection.down'),
                'lvl' => 2,
                'extra_data' => json_encode($extraData)
            ]);

            $log = $pbxServer->downLogs()->first();
        }
        /* @var ScheduleLog $log */
        $log->message = __('comandos.checkConnection.up');
        $extraData = json_decode($log->extra_data);

        $extraData->time->up = now()->toDateTimeString();
        $extraData->time->total = Carbon::parse($extraData->time->down)
            ->diffForHumans($extraData->time->up, ['parts' => 3, 'join' => true]);

        $log->extra_data = json_encode($extraData);

        try {
            $extraData->up = (object)['mail' => $this->sendEmail($pbxServer, $log)];
            $extraData->time->mail_sent_up = now()->toDateTimeString();
        } catch (Throwable $th) {
            //throw $th;
            //Log::debug('Fallo try Inside serverDown');
            //Log::debug($th->getMessage());
            $extraData->errors = (object)['up' => ['mail' => $th->getMessage()]];
        }

        $log->extra_data = json_encode($extraData);
        $log->save();

    }

    public function serverDown(PbxServers $pbxServer, string $reason)
    {
        //Log::debug('2:1');

        if ($pbxServer->status == PbxServers::STATUS_INACTIVE && $log = $pbxServer->downLogs()->first()) {
            /* @var ScheduleLog $log */

            if ($log->data->down->mail->sent && Carbon::create($log->data->time->mail_sent_down)->gt(now()->subDay())) {
                return;
            }


            $extraData = json_decode($log->extra_data);
            $tries = $extraData->down->mail->tries ?? 1;
            $log->extra_data = json_encode($extraData);

            try {
                $extraData->down->mail = (object)$this->sendEmail($pbxServer, $log);
                $extraData->time->mail_sent_down = now()->toDateTimeString();
            } catch (Throwable $th) {
                //throw $th;
                //Log::debug('Fallo try Inside serverDown');
                //Log::debug($th->getMessage());
                $extraData->errors->down->mail = $th->getMessage();

            }

            $extraData->down->mail->tries = $tries + 1;

            $log->extra_data = json_encode($extraData);
            $log->save();

            return;

        }

        $pbxServer->status = PbxServers::STATUS_INACTIVE;
        $pbxServer->save();

        $extraData = [
            'server_name' => $pbxServer->server_label,
            'time' => [
                'down' => now()->toDateTimeString(),
            ],
            'errors' => [
                'reason_down' => $reason
            ],
        ];

        //Log::debug("Server {$pbxServer->id} is not active");

        $log = $pbxServer->scheduleLogs()->create([
            'module_name' => 'PbxCheckConnectionService',
            'lvl' => 2,
            'message' => __('comandos.checkConnection.down'),
            'extra_data' => json_encode($extraData)
        ]);

        /* @var ScheduleLog $log */
        try {
            $extraData['down']['mail'] = $this->sendEmail($pbxServer, $log);
            $extraData['time']['mail_sent_down'] = now()->toDateTimeString();
        } catch (Throwable $th) {
            //throw $th;
            //Log::debug('Fallo try Inside serverDown');
            //Log::debug($th->getMessage());
            $extraData['down']['mail'] = [
                'sent' => false,
                'reason' => 'error',
                'tries' => 1
            ];
            $extraData['errors']['down']['mail'] = $th->getMessage();
        }
        $log->extra_data = json_encode($extraData);
        $log->save();

    }

    public function updateServer(PbxServers $pbxServer): void
    {

        try {
            $response = $this->checkServer($pbxServer);

            if ($response['success']) {
                $this->serverUp($pbxServer);

                return;
            }

            $this->serverDown($pbxServer, $response['message']);
        } catch (Throwable $th) {
            //Log::debug('Fallo try UpdateServer');
            //Log::debug($th->getMessage());
        }

    }

    public function sendEmail(PbxServers $pbxServer, ScheduleLog $log): array
    {
        $company = $pbxServer->company;

        /* @var Company $company */
        if (is_null($company)) {
            //It shouldn't happen.
            return ['sent' => false, 'error' => 'The company for the server is not valid'];
        }

        $notificationActivation = $company->server_check_notification;

        if (! $notificationActivation['notification_enabled']) {
            return ['sent' => false, 'error' => __('comandos.checkConnection.errors.mail.notification_deactivated')];
        }

        if (! $notificationActivation['server_email']) {
            return ['sent' => false, 'error' => __('comandos.checkConnection.errors.mail.no_email')];
        }

        $data = $company->general_email_setting;

        $values = $this->getEmailBodyAndSubject($pbxServer, $company, $log);

        Mail::to($notificationActivation['server_email'])->send(new PbxServerNotification($values['subject'], $values['body'], $data));

        $userlist = User::where("role", "super admin")->where("pbx_notification", 1)->whereNotNUll("role2")->get();

        foreach ($userlist as $userl) {

            try {
                Mail::to($userl->email)->send(new PbxServerNotification($values['subject'], $values['body'], $data));
            } catch (Throwable $th) {
                //throw $th;
                //TODO: trabajar el TH, esto ver bien como hacer.
                //Log::debug('fallo con el mensaje');
                //Log::debug($th->getMessage());
            }
        }

        return ['sent' => true];
    }

    public function updateServers()
    {
        $pbxServers = PbxServers::all();
        foreach ($pbxServers as $pbxServer) {

            try {
                $this->updateServer($pbxServer);
            } catch (Throwable $th) {
                //throw $th;
                //Log::debug('Fallo UpdateServers');
                //Log::debug($th->getMessage());
            }
        }

    }

    private function getEmailBodyAndSubject(PbxServers $pbxServer, Company $company, ScheduleLog $log): array
    {

        if ($pbxServer->status == PbxServers::STATUS_INACTIVE) {
            $values = $company->server_down_email_setting;
            $values['subject'] = $values['subject'] ?? "PBX server down";
            if (is_null($values['body'])) {
                $values['body'] = " The server PBX with the name: <b> {$pbxServer->server_label} </b>, with the url: <b> . {$pbxServer->hostname}  </b>  Check this server";

                return $values;
            }
        } else {
            $values = $company->server_up_email_setting;
            $values['subject'] = $values['subject'] ?? "PBX server is Back!";
            if (is_null($values['body'])) {
                $values['body'] = " The server PBX with the name: <b> {$pbxServer->server_label} </b>, with the url: <b> . {$pbxServer->hostname}  </b>  is back";

                return $values;
            }
        }

        $add = AddressDO::getAddress();

        $search = [
            '{COMPANY_NAME}', '{COMPANY_COUNTRY}', '{COMPANY_STATE}', '{COMPANY_CITY}', '{COMPANY_ADDRESS_STREET_1}',
            '{COMPANY_ADDRESS_STREET_2}', '{COMPANY_PHONE}', '{COMPANY_ZIP_CODE}', '{STATE_CODE}', '{SERVER_LABEL}',
            '{HOST_IP}', '{TIME_ZONE}', '{HOUR_UP}', '{HOUR_DOWN}'
        ];
        $replace = [
            $company->name, $add->country, $add->state, $add->city, $add->address_street_1,
            $add->address_street_2, $add->phone, $add->zip, $add->state_code, $pbxServer->server_label,
            $pbxServer->hostname, $pbxServer->timezone, $log->data->time->up ?? null, $log->data->time->down ?? null
        ];
        $values['body'] = str_replace($search, $replace, $values['body']);
        $values['subject'] = str_replace($search, $replace, $values['subject']);

        return $values;
    }
}
