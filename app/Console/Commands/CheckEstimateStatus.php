<?php

namespace Crater\Console\Commands;

use Crater\Models\Estimate;
use Illuminate\Console\Command;

class CheckEstimateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estimate:check_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status from not final status to Expired from expenses that have expiry_date older than today.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $status = [Estimate::STATUS_ACCEPTED, Estimate::STATUS_REJECTED, Estimate::STATUS_EXPIRED];
        Estimate::whereNotIn('status', $status)
            ->whereDate('expiry_date', '<', now()->format('Y-m-d'))
            ->update([
                'status' => Estimate::STATUS_EXPIRED,
            ]);

        return self::SUCCESS;
    }
}
