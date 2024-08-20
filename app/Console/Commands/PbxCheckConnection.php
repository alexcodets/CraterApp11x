<?php

namespace Crater\Console\Commands;

use Crater\Services\Pbx\PbxCheckConnectionService;
use Illuminate\Console\Command;

class PbxCheckConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:checkConnection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the Server status, if there is a problem it will notify the user';

    public PbxCheckConnectionService $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PbxCheckConnectionService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->service->updateServers();

        return self::SUCCESS;

    }
}
