<?php

namespace Crater\Console\Commands;

use Crater\Jobs\PbxServerTenantDidSynchronizeJob;
use Crater\Jobs\PbxServerTenantExtensionSynchronizeJob;
use Crater\Jobs\PbxServiceRecalculateJob;
use Crater\Models\PbxServerTenant;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class PbxTenantSynchronizeCommand extends Command
{
    protected $signature = 'tenant:synchronize';

    protected $description = 'Synchronize tenant did from the Pbx Server with the system';

    public function handle(): void
    {

        \Log::debug('Starting PbxTenantSynchronizeCommand');

        $jobs = [];
        //        PbxServerTenant::chunk(20, function ($tenants) {
        //            $tenants->each(fn(PbxServerTenant $tenant) => Bus::chain([
        //                new PbxServerTenantDidSynchronizeJob($tenant->id),
        //                new PbxServerTenantExtensionSynchronizeJob($tenant->id),
        //            ])->dispatch());
        //        });

        PbxServerTenant::chunk(20, function ($tenants) use (&$jobs) {
            $tenants->each(function (PbxServerTenant $tenant) use (&$jobs) {
                $jobs[] = new PbxServerTenantDidSynchronizeJob($tenant->id);
                $jobs[] = new PbxServerTenantExtensionSynchronizeJob($tenant->id);
            });
        });

        if ($jobs === []) {
            Log::debug('No tenant for job.');

            return;
        }

        $jobs[] = new PbxServiceRecalculateJob();

        Log::debug('total jobs: '.count($jobs));

        try {
            $batch = Bus::batch($jobs)->then(function (Batch $batch) {
                Cache::put('tenant_synchronize', [], 3600);
            })->catch(function (Batch $batch, Throwable $e) {
                \Log::error('Error with batch: '.$e->getMessage());
            })->finally(function (Batch $batch) {})
                ->name('Tenant-Recalculate')
                ->dispatch();
        } catch (\Throwable $th) {
            \Log::error('Error with batch Global: '.$th->getMessage());
        }

        \Log::debug('Ending PbxTenantSynchronizeCommand');

    }
}
