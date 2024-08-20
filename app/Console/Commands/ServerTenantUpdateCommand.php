<?php

namespace Crater\Console\Commands;

use Crater\Jobs\ServerTenantUpdateJob;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Illuminate\Console\Command;

class ServerTenantUpdateCommand extends Command
{
    protected $signature = 'server:tenant-update';

    protected $description = 'Update Did and Extension for PbxServerTenant that does not have a PbxTenant with active service.';

    public function handle(): void
    {
        $this->info('Server tenant update command called');

        $idArray = PbxServices::where('main_update', true)->get('pbx_tenant_id')->toArray();
        $bindingsString = trim(str_repeat('?,', count($idArray)), ',');

        $tenants = PbxServerTenant::query()
            ->whereNotExists(fn ($query) => $query->select(\DB::raw(1))
                ->from('pbx_tenant as tenants')
                ->whereRaw("id IN ( {$bindingsString} )", $idArray)
                ->whereRaw('tenants.code = pbx_server_tenants.tenant_code')
                ->whereRaw('tenants.tenantid = pbx_server_tenants.tenant_id')
                ->whereRaw('tenants.pbx_server_id = pbx_server_tenants.pbx_server_id'))
            ->get();

        $tenants->each(function (PbxServerTenant $tenant) {
            ServerTenantUpdateJob::dispatch($tenant->id);
        });

    }
}
