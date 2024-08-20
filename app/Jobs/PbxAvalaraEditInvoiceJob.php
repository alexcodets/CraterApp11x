<?php

namespace Crater\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class PbxAvalaraEditInvoiceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $invoiceId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Artisan::call('pbx:avalaraEditInvoice', ['invoice' => $this->invoiceId]);
    }
}
