<?php

namespace Crater\Console\Commands;

use Crater\Models\Expense;
use Illuminate\Console\Command;

class ActiveExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:activation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status from pending to active for expenses that have expense_date older than today.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        Expense::where('status', Expense::STATUS_PENDING)->whereDate('expense_date', '<=', now()->format('y-m-d'))->update([
            'status' => Expense::STATUS_ACTIVE
        ]);

        return self::SUCCESS;
    }
}
