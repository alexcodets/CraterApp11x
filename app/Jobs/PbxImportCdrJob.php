<?php

namespace Crater\Jobs;

use Carbon\Carbon;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxJobLog;
use Crater\Models\PbxServices;
use Crater\Pbxware\PbxWareApi;
use Crater\Pbxware\Service\PbxWareService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PbxImportCdrJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $name;

    protected Carbon $end;

    protected Carbon $start;

    protected PbxWareService $service;

    protected PbxServices $pbxService;

    protected PbxWareApi $api;

    protected string $status;

    public $tries = 10;
    //public $backoff = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PbxWareApi $api, PbxServices $pbxService, PbxWareService $service, Carbon $start, Carbon $end, string $status = '8')
    {
        $this->api = $api;
        $this->pbxService = $pbxService;
        $this->service = $service;
        $this->start = $start;
        $this->end = $end;
        $this->status = $status;
        $this->name = 'PbxImportCdrJob';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        //Log::debug("");
        //Log::debug("<[==============================|PbxImportCdr|==============================]>");
        //Log::debug("Starting Import for service {$this->pbxService->id} on timezone {$this->start->tzName}");
        //Log::debug("Importing time between: " . $this->start->format('Y-m-d H:i:s') . " and " . $this->end->format('Y-m-d H:i:s'));

        if (! $this->validation()) {
            Log::debug("Error Service {$this->pbxService->id} is not valide anymore");

            return;
        }
        if ($this->batch() && $this->batch()->cancelled()) {
            Log::debug('Batch Cancelled');

            return;
        }

        $this->pbxService->is_importing = true;
        $this->pbxService->save();
        $cdr = new CallDetailRegister();

        //Hacer esto al inicio ahorra algo de tiempo
        //TODO:
        try {
            $cdr->setTable($cdr->firstOrCreateTableFromService($this->pbxService));
            $response = $this->service->importCdrsByTrunk($this->api, $this->pbxService, $this->start, $this->end, $cdr, $this->status);
        } catch (Throwable $th) {
            //throw $th;
            $response['success'] = false;
            $response['message'] = $th->getMessage();
        }

        if (! $response['success']) {
            $delay = 20;
            //Log::debug('-----Error-----');
            $note = "The Job: _PbxImportCdrJob_ For Service id: {$this->pbxService->id} will be try again in {$delay} seconds, now is the attempt number: ".$this->attempts();
            //Log::debug($note);
            //Log::debug($response['message']);
            //Log::debug('--------------');

            $data = [
                'timeLapse' => [
                    'timezone' => $this->start->tzName,
                    'start' => $this->start->format('Y-m-d H:i:s'),
                    'end' => $this->end->format('Y-m-d H:i:s'),
                ],
                'service_id' => $this->pbxService->id,
                'tries' => $this->attempts(),
                'retry_in' => "{$delay}s",
                'cdr_status' => $this->status,
            ];

            PbxJobLog::create([
                'name' => 'PbxImportCdrJob',
                'response' => $response['message'],
                'lvl' => PbxJobLog::LVL_WARNING,
                'info' => $note,
                'data' => json_encode($data),
                'pbx_service_id' => $this->pbxService->id,
            ]);

            $this->release($delay);

            //If the process failed the job should be counted as failed and retry later.

        }
        if ($response['success']) {
            //Log::debug('Process Finished Successfully');

            $data = [
                'timeLapse' => [
                    'timezone' => $this->start->tzName,
                    'start' => $this->start->format('Y-m-d H:i:s'),
                    'end' => $this->end->format('Y-m-d H:i:s'),
                ],
                'service_id' => $this->pbxService->id,
                'tries' => $this->attempts(),
                'cdr_status' => $this->status,
                'time' => $response['time'],
            ];

            PbxJobLog::create([
                'name' => 'PbxImportCdrJob',
                'response' => 'Process Finished Successfuly',
                'lvl' => PbxJobLog::LVL_INFO,
                'data' => json_encode($data),
                'pbx_service_id' => $this->pbxService->id,
            ]);
        }

        /*if (isset($response['time'])) {
            //Log::debug('----------Time----------');
            //Log::debug('Main process: ' . $response['time']['main']);
            //Log::debug('getting cdr from api: ' . $response['time']['cdrs']);
            //Log::debug('Total Time: ');
            //Log::debug($response['time']['totals']);
        }*/
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $delay = 20;
        //Log::debug("The Job: {$name} with id: {$id} just fail");
        //Log::debug($exception->getMessage());
        $note = 'Error at Job LVL';

        $data = [
            'timeLapse' => [
                'timezone' => $this->start->tzName,
                'start' => $this->start->format('Y-m-d H:i:s'),
                'end' => $this->end->format('Y-m-d H:i:s'),
            ],
            'service_id' => $this->pbxService->id,
            'tries' => $this->attempts(),
            'retry_in' => "{$delay}s",
            'cdr_status' => $this->status,
        ];

        PbxJobLog::create([
            'name' => $this->name,
            'response' => $exception->getMessage(),
            'lvl' => 3,
            'info' => $note,
            'data' => json_encode($data),
            'pbx_service_id' => $this->pbxService->id,
        ]);

        $this->release($delay);

    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        //return [(new WithoutOverlapping($this->pbxService->id))->releaseAfter(50)->expireAfter(180)];
        return [];
    }

    public function validation(): bool
    {
        return PbxServices::whereNull('deleted_at')->where('status', 'A')->whereId($this->pbxService->id)->exists();
    }
}
