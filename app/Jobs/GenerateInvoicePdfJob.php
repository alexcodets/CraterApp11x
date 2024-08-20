<?php

namespace Crater\Jobs;

use Crater\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Invoice $invoice;

    public bool $deleteExistingFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, bool $deleteExistingFile = false)
    {
        $this->invoice = $invoice;
        $this->deleteExistingFile = $deleteExistingFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->invoice->generatePDF('invoice', $this->invoice->invoice_number, $this->deleteExistingFile);
    }
}
