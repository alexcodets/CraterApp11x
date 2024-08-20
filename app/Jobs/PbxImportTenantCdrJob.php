<?php

namespace Crater\Jobs;

use Carbon\Carbon;
use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxJobLog;
use Crater\Models\PbxServices;
use Crater\Pbxware\PbxWareApi;
use Crater\Pbxware\Service\PbxService;
use Crater\Pbxware\Service\PbxTenantCdrImportService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PbxImportTenantCdrJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Carbon $end;

    protected Carbon $start;

    protected PbxCdrTenant $tenant;

    protected string $name;

    protected PbxWareApi $api;

    protected PbxService $service;

    protected string $status;

    public $tries = 10;
    //public $backoff = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PbxCdrTenant $tenant, PbxWareApi $api, Carbon $start, Carbon $end, string $status = '8')
    {
        $this->api = $api;
        $this->service = new PbxService();
        $this->start = $start;
        $this->end = $end;
        $this->status = $status;
        $this->name = 'PbxTenantImportCdrJob';
        $this->tenant = $tenant;

        //$this->onQueue('pbxTenantCdrImport');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PbxTenantCdrImportService $service): void
    {
        if (! $this->validation()) {
            //Log::debug("Error Tenant is not valide anymore");
            return;
        }

        $delay = 180;

        try {
            //Log::debug("Run Started");
            $service->import($this->tenant, $this->api, $this->start, $this->end, $this->job->getJobId(), $this->status);
            //Log::debug("Run finished");
        } catch (Throwable $th) {
            //throw $th;
            //Log::debug($th->getMessage());
            PbxJobLog::create([
                'name' => 'PbxImportCdrJob',
                'response' => $th->getMessage(),
                'lvl' => PbxJobLog::LVL_WARNING,
                //'data'           => json_encode($data),
                'pbx_service_id' => null,
            ]);

            $this->release($delay);
        }
    }

    public function validation(): bool
    {
        return PbxServices::whereNull('deleted_at')->whereNotIn('status', ['P', 'C'])
            ->whereHas('tenant', function ($query) {
                return $query->where('code', $this->tenant->code)
                    ->where('tenantid', $this->tenant->tenantid)
                    ->where('pbx_server_id', $this->tenant->pbx_server_id);
            })->exists();
    }
}
