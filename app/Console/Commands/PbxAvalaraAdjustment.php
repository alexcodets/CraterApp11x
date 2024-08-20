<?php

namespace Crater\Console\Commands;

use Illuminate\Console\Command;

class PbxAvalaraAdjustment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:avalaraAdjustment {invoice}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If there is a adjustment to a Avalara Invoice';

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
        return 0;
    }
}
