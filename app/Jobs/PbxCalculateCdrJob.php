<?php

namespace Crater\Jobs;

use Crater\Models\PbxJobLog;
use Crater\Models\PbxServices;
use Crater\Pbxware\Service\PbxService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PbxCalculateCdrJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public PbxServices $service;

    public string $type;

    public PbxService $pbxService;

    public $tries = 5;

    public $backoff = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PbxServices $service, string $type)
    {
        $this->service = $service;
        $this->type = $type;
        $this->pbxService = new PbxService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        //Log::debug("Inside Job");
        if ($this->batch()?->canceled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        $response = [];
        $data = [];
        $lvl = 1;
        switch ($this->type) {
            case 'outbound':
                $response = $this->pbxService->calculateOutbound($this->service);

                break;

            case 'inbound':
                $response = $this->pbxService->calculateInbound($this->service);

                break;
            default:
                PbxJobLog::create([
                    'name' => 'PbxCalculaCdrJob',
                    'response' => "Process Finished Successfuly for {$this->type}",
                    'lvl' => 2,
                    'data' => json_encode("The Type: {$this->type} is not a valid type (inbund|outbound)"),
                    'pbx_service_id' => $this->service->id,
                ]);

                break;
        }
        //

    }

    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}
