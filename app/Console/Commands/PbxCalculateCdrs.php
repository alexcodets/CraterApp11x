<?php

namespace Crater\Console\Commands;

use Crater\Models\PbxServices;
use Crater\Pbxware\Service\PbxService;
use Crater\Traits\PbxServiceValidationTrait;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Collection;
use Log;

class PbxCalculateCdrs extends Command
{
    use PbxServiceValidationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:calculateCDRs
                            {--service= : pbxServices id, if no service id is inputted, it will run for every PbxService.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CDRs total calculation';

    /* @var PbxService */
    protected $pbxService;

    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->pbxService = new PbxService();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('<----------PbxCalculateCdrs---------->');

        $services = $this->getService();

        if (! $this->validateServices($services)) {
            $this->info('   Not a valid service');
            $this->info('<----------PbxCalculateCdrs----------/>');

            return 0; // Retorno adecuado para indicar un error o proceso sin ejecución
        }

        foreach ($services as $service) {
            $this->info('----------id----------'.$service->pbx_services_number);
            $this->info('   Service id----------'.$service->id);

            if (! $this->validateService($service->pbxPackage)) {
                continue;
            }

            try {
                $response = $this->pbxService->calculateTotal($service);
            } catch (\Throwable $th) {
                if ($th instanceof LockTimeoutException) {
                    Log::debug("The {$service->id} could not complete the calculation process due to a lock.");
                }

                Log::debug($th->getMessage());
                $response['success'] = false;
            }

            Log::debug("The {$service->id} PbxCalculateCdr was " . ($response['success'] ? 'successful' : 'a failure'));
        }

        $this->info('----------Totals ready----------');

        return 0; // Indica una ejecución exitosa
    }

    public function getService(): Collection
    {
        if ($this->option('service') == null) {
            return PbxServices::whereNull('deleted_at')
                ->where('status', 'A')
                ->whereHas('pbxPackage', function ($q) {
                    $q->where('call_ratings', 1);
                })
                ->with(['tenant:id,name,code', 'pbxPackage'])
                ->get();
        }

        return PbxServices::whereNull('deleted_at')
            ->where('status', 'A')
            ->whereHas('pbxPackage', function ($q) {
                $q->where('call_ratings', 1);
            })
            ->with(['tenant:id,name,code', 'pbxPackage'])
            ->find([$this->option('service')]);
    }
}
