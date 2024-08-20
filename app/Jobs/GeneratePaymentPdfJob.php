<?php

namespace Crater\Jobs;

use Crater\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePaymentPdfJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Payment $payment;

    public bool $deleteExistingFile;

    public function __construct(Payment $payment, bool $deleteExistingFile = false)
    {
        $this->payment = $payment;
        $this->deleteExistingFile = $deleteExistingFile;
    }

    public function handle(): void
    {
        $this->payment->generatePDF('payment', $this->payment->payment_number, $this->deleteExistingFile);
    }
}
