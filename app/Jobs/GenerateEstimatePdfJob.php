<?php

namespace Crater\Jobs;

use Crater\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateEstimatePdfJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Estimate $estimate;

    public bool $deleteExistingFile;

    public function __construct(Estimate $estimate, bool $deleteExistingFile = false)
    {
        $this->estimate = $estimate;
        $this->deleteExistingFile = $deleteExistingFile;
    }

    public function handle(): void
    {
        $this->estimate->generatePDF('estimate', $this->estimate->estimate_number, $this->deleteExistingFile);
    }
}
