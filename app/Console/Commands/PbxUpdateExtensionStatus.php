<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Mail\PbxUpdateExtensionStatusMail;
use Crater\Models\Company;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxTenant;
use Crater\Models\ScheduleLog;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Log;
use Mail;
use Throwable;

class PbxUpdateExtensionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:updateExtensionStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It check the PbxServer And Update the local status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        //Log::debug('===================PbxUpdateExtensionStatus===================');
        //Log::debug('<=Start=>');

        PbxTenant::whereHas('pbxExtensions')->with(['pbxExtensions', 'pbxServer', 'pbxService'])->chunk(10, function ($tenants) {
            foreach ($tenants as $tenant) {
                //Log::debug("  tenant: {$tenant->id}");
                $api = new PbxWareApi($tenant->pbxServer);
                $values = [];

                $apiResponse = $api->extensionsList($tenant->tenantid);

                //Fail tenant lvl.
                if (! $apiResponse['success']) {
                    //Log::debug('El error', $apiResponse);
                    //Log::debug('Entro aca');
                    $tenant->scheduleLogs()->create(
                        [
                            'module_name' => 'pbx:updateExtensionStatus',
                            'lvl' => ScheduleLog::LVL_ERROR,
                            'message' => __('comandos.updateExtensionStatus.errors.api', ['error' => $apiResponse['message']]),
                        ]
                    );

                    continue;
                }

                $apiExtensions = $apiResponse['data'];

                foreach ($tenant->pbxExtensions as $extension) {
                    //Log::debug('Dentro de las extensiones');
                    if (! isset($apiExtensions[$extension->pbxext_id])) {
                        continue;
                    }
                    //Log::debug("La extension {$extension->name} esta en el listado");
                    if ($apiExtensions[$extension->pbxext_id]['status'] == $extension->status) {
                        continue;
                    }

                    //Here es donde cambia de un estado al otro (comentario generic)

                    //Log::debug("     La extension {$extension->name} tenia status: {$extension->status}, y ahora pasara a ser {$apiExtensions[$extension->pbxext_id]['status']}");
                    //Log::debug('Extension: ', $apiExtensions[$extension->pbxext_id]);

                    /* @var ScheduleLog $log */
                    //$this->logGeneration($extension, $apiExtensions[$extension->pbxext_id]);

                    $values[$apiExtensions[$extension->pbxext_id]['status']][] = $extension;

                    $extension->status = $apiExtensions[$extension->pbxext_id]['status'];
                    $extension->save();

                }
                if (empty($values['enabled']) && empty($values['disabled'])) {
                    continue;
                }
                //Log::debug('Values was not empty');
                //Log::debug($values);

                try {
                    $response = $this->sendMail($tenant, $values);
                } catch (Throwable $th) {
                    //throw $th;
                    Log::debug($th->getMessage());
                    //Log::debug('Error Sending Mail');
                    $response = ['sent' => false, 'reason' => $th->getMessage()];
                }

                try {
                    $this->logManager($values, $response);
                } catch (Throwable $th) {
                    //throw $th;
                    //Log::debug('Error Log Manager');

                    Log::debug($th->getMessage());
                }

            }
        });

        //og::debug('< CheckLogs >');
        $this->checkLogs();
        //Log::debug('</ CheckLogs >');
        //Log::debug('<=End.=>');

        return self::SUCCESS;
    }

    /**
     * @throws Throwable
     */
    public function logManager(array $values, array $mailResponse)
    {
        foreach ($values['disabled'] ?? [] as $ext) {
            $this->logGeneration($ext, $mailResponse);
        }
        foreach ($values['enabled'] ?? [] as $ext) {
            $this->logGeneration($ext, $mailResponse);
        }

    }

    public function sendMail(PbxTenant $tenant, array $extensions): array
    {

        //Log::debug('Inside Send Mail');

        $company = $tenant->company;

        /* @var Company $company */
        if (is_null($company)) {
            //It shouldn't happen.
            //Log::debug('1');
            return ['sent' => false, 'reason' => 'The company for the extension is not valid'];
        }

        $notificationActivation = $company->server_check_notification;

        if (! $notificationActivation['notification_enabled']) {
            //Log::debug('2');
            return ['sent' => false, 'reason' => __('comandos.general.notifications.disabled')];
        }

        if (! $notificationActivation['server_email']) {
            Log::debug('3');

            return ['sent' => false, 'reason' => __('comandos.general.notifications.no_email')];
        }

        //Log::debug('Inside SendMail');
        //Log::debug($tenant->company->main_email);

        //Check Email
        try {
            if (! empty($extensions['enabled'] ?? [])) {
                //Log::debug('enabled entro');
                Mail::to($tenant->company->main_email)->send(new PbxUpdateExtensionStatusMail($extensions['enabled'], $tenant, 'up'));
            }
            if (! empty($extensions['disabled'] ?? [])) {
                //Log::debug('disabled entro');
                Mail::to($tenant->company->main_email)->send(new PbxUpdateExtensionStatusMail($extensions['disabled'], $tenant, 'down'));
            }
        } catch (Throwable $th) {
            $tenant->scheduleLogs()->create(
                [
                    'module_name' => 'pbx:updateExtensionStatus',
                    'lvl' => ScheduleLog::LVL_ERROR,
                    'message' => __('comandos.updateExtensionStatus.errors.email.not_send', ['error' => $th->getMessage()]),
                    'extra_data' => json_encode([
                        'address' => $tenant->company->main_email,
                        'extensions' => $extensions
                    ])
                ]
            );
            Log::error('Error With mail');
            $this->info(__('Error With mail'));
            Log::error($th->getMessage());

            return ['sent' => false, 'reason' => $th->getMessage()];
        }

        return ['sent' => true];

    }

    /**
     * @throws Throwable
     */
    public function logGeneration(PbxExtensions $extension, $response)
    {
        switch ($extension->status) {

            case 'disabled':
                $log = $this->down($extension);
                /* @var ScheduleLog $log */
                $extraData = json_decode($log->extra_data);

                if ($response['sent']) {
                    $extraData->disabled = (object)['email' => $response];
                    $extraData->time->mail_disable = now()->toDateTimeString();
                } else {
                    $extraData->disabled = (object)[
                        'email' => [
                            'sent' => false,
                            'reason' => 'error',
                            'tries' => 1
                        ]
                    ];
                    $extraData->errors = (object)['disabled' => ['email' => $response['reason']]];

                }

                $log->extra_data = json_encode($extraData);
                $log->save();

                break;
            case 'enabled':
                $log = $this->up($extension);
                $extraData = json_decode($log->extra_data);

                if ($response['true']) {
                    $extraData->enabled = (object)['email' => $response];
                    $extraData->time->mail_enable = now()->toDateTimeString();
                } else {
                    $extraData->enabled->email = (object)[
                        'sent' => false,
                        'reason' => 'error',
                        'tries' => 1
                    ];
                    //Log::debug('Fallo enabled case');
                    $extraData->errors = (object)['up' => ['email' => $response['reason']]];
                }

                $extraData->time->total = Carbon::parse($extraData->time->disabled)
                    ->diffForHumans($extraData->time->disabled, ['parts' => 3, 'join' => true]);
                $log->extra_data = json_encode($extraData);
                $log->save();

                break;
            default:
                break;
        }
    }

    /**
     * @throws Throwable
     */
    public function down(PbxExtensions $extension): Model
    {

        try {
            return $extension->scheduleLogs()->create([
                'module_name' => 'pbx:updateExtensionStatus',
                'lvl' => ScheduleLog::LVL_ERROR,
                'message' => 'disabled',
                'extra_data' => json_encode([
                    'time' => [
                        'disabled' => now()->toDateTimeString()
                    ],
                    'name' => $extension->name,
                ])
            ]);
        } catch (Throwable $th) {
            //Log::debug('Error inside down');
            throw $th;
        }

    }

    /**
     * @throws Throwable
     */
    public function up(PbxExtensions $extension)
    {
        try {
            $log = $extension->disabledLogs()->first() ?? new ScheduleLog();
            $log->message = 'enabled';
            $extraData = json_decode($log->extra_data) ?? (object)['time' => []];
            $extraData->time->enabled = now()->toDateTimeString();
            $log->extra_data = json_encode($extraData);
            $log->save();

            return $log;
        } catch (Throwable $th) {
            //Log::debug('Error inside Up');
            throw $th;
        }

    }

    public function checkLogs()
    {
        $logs = ScheduleLog::where('module_name', 'pbx:updateExtensionStatus')
            ->where('message', 'disabled')->where('updated_at', '<=', now()->subMinutes(3)->toDateTimeString())
            ->with('model')->get();
        //Log::debug('Round');
        $temp = [];

        foreach ($logs as $log) {
            $extension = $log->model;
            /* @var PbxExtensions $extension */
            $temp[$extension->pbxTenant->id]['ext'][$extension->status][] = $extension;
            $temp[$extension->pbxTenant->id]['logs'][] = $log;
            $temp[$extension->pbxTenant->id]['tenant'] = $extension->pbxTenant;

        }

        foreach ($temp as $item) {
            try {
                $response = $this->sendMail($item['tenant'], $item['ext']);
            } catch (Throwable $th) {
                //throw $th;
                Log::debug($th->getMessage());
                //Log::debug('Error Sending Mail');
                $response = ['sent' => false, 'reason' => $th->getMessage()];
            }

            foreach ($item['logs'] as $log) {
                //$extension = $log->model;
                /* @var PbxExtensions $extension */
                $extraData = json_decode($log->extra_data);
                $tries = $extraData->disabled->email->tries ?? 1;
                //Log::debug('Tries: ', ['tries' => $tries]);

                try {
                    $extraData->disabled = (object)['email' => array_merge($response, ['tries' => $tries + 1])];
                    $extraData->time->mail_disable = now()->toDateTimeString();

                } catch (Throwable $th) {
                    //throw $th;
                    $extraData->errors = (object)['disabled' => $th->getMessage()];
                    //Log::debug('Fallo enabled case');
                    Log::debug($th->getMessage());
                }
                //Log::debug('Extra Data:');
                //Log::debug(json_encode($extraData));
                $log->extra_data = json_encode($extraData);
                $log->save();
            }

        }


    }
}
