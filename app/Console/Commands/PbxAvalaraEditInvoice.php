<?php

namespace Crater\Console\Commands;

use Crater\Avalara\Service\AvalaraAdjustmentService;
use Crater\Avalara\Service\AvalaraService;
use Crater\Helpers\Chronometer;
use Crater\Models\AvalaraInvoice;
use Crater\Models\AvalaraLog;
use Crater\Models\Invoice;
use Exception;
use Illuminate\Console\Command;
use Log;
use Throwable;

class PbxAvalaraEditInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:avalaraEditInvoice
     {invoice : The Invoice That was edited}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'When there is a edit to a Avalara Invoice';

    protected Invoice $invoice;

    protected AvalaraInvoice $avalaraInvoice;

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
        try {
            Log::debug('Inside Avalara Edit');
            $service = $this->validation();

        } catch (Throwable $th) {
            Log::debug($th->getMessage());
            $this->error('Error While Validating');
            $this->error($th->getMessage());

            return self::FAILURE;
        }

        try {
            $service->editInvoice();
        } catch (Throwable $th) {
            //throw $th;
            Log::debug($th->getMessage());

        }

        return self::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function validation(): AvalaraAdjustmentService
    {
        $id = $this->argument('invoice');

        $invoice = Invoice::find($id);
        $avalaraInvoice = $invoice->avalaraInvoice;

        if (is_null($invoice)) {
            throw new Exception('The Invoice ID is Invalid');
        }

        if (! is_null($invoice->deleted_at)) {
            throw new Exception('The invoice is not Deleted, so it cannot be VOID');
        }

        if ($avalaraInvoice->status !== AvalaraInvoice::STATUS_ACTIVE) {
            throw new Exception('The invoice is not active so it cannot be edit');
        }

        if (is_null($invoice->company)) {
            throw new Exception('The company associated to the Invoice Is not valid');
        }

        if (is_null($invoice->company->avalaraConfiguration)) {
            throw new Exception('There is not valide Avalara Configuration set');
        }

        //if ($invoice->created_at->format('m-Y') != now()->format('m-Y')) throw new Exception('Avalara Edit is only available for Invoice in the same month of creating');

        $this->info('Validation Passed');
        Log::debug('Validation Passed');

        return (new AvalaraAdjustmentService($invoice, $avalaraInvoice));
    }

    public function voidInvoice(Chronometer $timer, AvalaraService $service, AvalaraLog $log): void
    {
        $timer->start('void');
        $service->unCommitInvoice($this->invoice->avalara_document_code);
        $timer->end('void');
        $log->procesing_time = $timer->totalExecutionMilliseconds('void');
        $log->operation_type = AvalaraLog::OPERATION_VOID;
        $log->save();
        $this->avalaraInvoice->status = AvalaraInvoice::STATUS_VOID;
        $this->avalaraInvoice->save();
    }
}
