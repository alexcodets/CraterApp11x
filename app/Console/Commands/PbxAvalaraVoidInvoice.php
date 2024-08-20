<?php

namespace Crater\Console\Commands;

use Crater\Avalara\Service\AvalaraAdjustmentService;
use Crater\Models\AvalaraInvoice;
use Crater\Models\Invoice;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class PbxAvalaraVoidInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:avalaraVoidInvoice {invoice}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Void a Avalara invoice, if the invoice is in the actual period the transaction will be
     reversed, if the invoice is from a previous period a new invoice with same data but adj true will be sent';

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
        //return Command::SUCCESS;
        //TODO: partial refund?
        //$this->info('process started');
        try {
            $service = $this->validation();

        } catch (Throwable $th) {
            //Log::debug($th->getMessage());
            $this->error('Error While Validating');
            $this->error($th->getMessage());

            return self::FAILURE;
        }

        try {
            $service->voidOrReverseInvoice();

            return self::SUCCESS;

        } catch (Throwable $th) {
            //Log::debug($th->getMessage());
            $this->error('Error While processing');
            $this->error($th->getMessage());

            return self::FAILURE;
        }

    }

    /**
     * @throws Exception
     */
    public function validation(): AvalaraAdjustmentService
    {
        $id = $this->argument('invoice');

        $invoice = Invoice::withTrashed()->find($id);
        $avalaraInvoice = AvalaraInvoice::where('invoice_id', $id)->first();

        if (is_null($invoice)) {
            throw new Exception('The Invoice ID is Invalid');
        }

        if (is_null($invoice->deleted_at)) {
            throw new Exception('The invoice is not Deleted, so it cannot be VOID');
        }

        /*if ($invoice->status !== Invoice::STATUS_COMPLETED){
            throw new Exception('The Invoice status is not complete, so it cannot be void');
        }*/

        /*if (is_null($invoice->inv_avalara_bool)){
            throw new Exception('The invoice is not an avalara invoice');
        }*/

        /*if (is_null($invoice->pbx_service_id)){
            throw new Exception('The invoice is not a Pbx Invoice');
        }*/

        if ($avalaraInvoice->status !== AvalaraInvoice::STATUS_ACTIVE) {
            throw new Exception('The invoice is not active so it cannot be void again');
        }

        //TODO: Check, may delete later.
        /*if (PbxServices::where('id', $invoice->pbx_service_id)->doesntExist()){
            throw new Exception('The service associated to the Invoice Is not valid');
        }*/

        if (is_null($invoice->company)) {
            throw new Exception('The company associated to the Invoice Is not valid');
        }

        if (is_null($invoice->company->avalaraConfiguration)) {
            throw new Exception('There is not valide Avalara Configuration set');
        }

        $this->info('Validation Passed');

        return (new AvalaraAdjustmentService($invoice, $avalaraInvoice));
    }
}
