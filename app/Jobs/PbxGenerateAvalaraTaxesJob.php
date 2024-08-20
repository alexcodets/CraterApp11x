<?php

namespace Crater\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class PbxGenerateAvalaraTaxesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $invoiceId;

    protected string $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $invoiceId, string $options = '')
    {
        $this->invoiceId = $invoiceId;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Artisan::call('pbx:generateAvalaraTax '.$this->invoiceId.' '.$this->options);
    }
}
