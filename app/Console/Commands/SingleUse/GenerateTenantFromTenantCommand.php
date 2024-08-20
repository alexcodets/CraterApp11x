<?php

namespace Crater\Console\Commands\SingleUse;

use Crater\Models\PbxServerTenant;
use Crater\Models\PbxTenant;
use Illuminate\Console\Command;

class GenerateTenantFromTenantCommand extends Command
{
    protected $signature = 'single-use:tenant-from-tenant';

    protected $description = 'Will check every PbxTenant to see if a ServerTenant is necessary';

    public function handle(): void
    {
        //Artisan::call('single-use:tenant-from-tenant');
        PbxTenant::query()
            ->whereNotNull('tenantid')
            ->whereNotNull('pbx_server_id')
            ->whereNotNull('code')
            ->chunk(25, function ($tenants) {
                $tenants->each(function (PbxTenant $tenant) {
                    $exists = PbxServerTenant::query()
                        ->where('tenant_code', $tenant->code)
                        ->where('tenant_id', $tenant->tenantid)
                        ->where('pbx_server_id', $tenant->pbx_server_id)
                        ->exists();
                    if (! $exists) {
                        //dd($tenant);
                        PbxServerTenant::create([
                            'tenant_code' => $tenant->code,
                            'tenant_id' => $tenant->tenantid,
                            'pbx_server_id' => $tenant->pbx_server_id,
                            'company_id' => $tenant->company_id,
                            'name' => $tenant->name,
                            'status' => PbxServerTenant::STATUS_ACTIVE,
                        ]);
                    }
                });
            });

    }
}
