<?php

namespace Crater\Console\Commands;

use Crater\Models\Invoice;
use Illuminate\Console\Command;

class CheckInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Invoice Status from status different from completed to overdue, when the due_date is pass today.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        Invoice::where('status', '<>', Invoice::STATUS_COMPLETED)
            ->whereDate('due_date', '<', now())
            ->update([
                'status' => Invoice::STATUS_OVERDUE,
            ]);

        return self::SUCCESS;

    }
}
