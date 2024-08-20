<?php

namespace Crater\Console\Commands;

use Crater\Services\Payment\InvoiceLateFeeService;
use Illuminate\Console\Command;
use Log;
use Throwable;

class InvoiceGenerateLateFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generateLateFee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Late fees for invoices.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(InvoiceLateFeeService $service): int
    {
        try {
            $service->execute();
        } catch (Throwable $th) {
            Log::debug($th->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
