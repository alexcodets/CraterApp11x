<?php

namespace Crater\Console\Commands;

use Crater\Models\PbxCdrTenant;
use Illuminate\Console\Command;

class PbxClearTenantImportCdrs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:ClearTenantImportCDRs
                            {--J|jobs : Clear all jobs from the queue=pbxTenantCdrImport, if tenant is specified, it will work the same.}
                            {--tenant= : specify tenant.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will make all the tenants (or selected) available for jobs (will change to null the field use for that)';

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

        if ($this->option('jobs')) {
            \Artisan::call('queue:clear --queue=pbxTenantCdrImport');
        }

        if ($this->option('tenant')) {
            PbxCdrTenant::where('id', $this->option('tenant'))->update([
                'job_active_at' => null,
            ]);

            return self::SUCCESS;
        }

        PbxCdrTenant::whereNotNull('job_active_at')->update([
            'job_active_at' => null,
        ]);

        return self::SUCCESS;

    }
}
