<?php

namespace Crater\Avalara\Service;

use Artisan;
use Crater\Avalara\Apis\AvalaraApi;
use Crater\Helpers\Chronometer;
use Crater\Models\AvalaraInvoice;
use Crater\Models\AvalaraLog;
use Crater\Models\Invoice;
use Exception;
use Log;
use Throwable;

class AvalaraAdjustmentService
{
    public Invoice $invoice;
    public const VOID = 0;
    public const REVERSE = 1;

    private AvalaraInvoice $avalaraInvoice;

    private AvalaraService $service;

    public function __construct(Invoice $invoice, AvalaraInvoice $avalaraInvoice)
    {
        $this->service = new AvalaraService(new AvalaraApi($invoice->company->avalaraConfiguration));
        $this->invoice = $invoice;
        $this->avalaraInvoice = $avalaraInvoice;
    }

    public function getInvoiceType(): int
    {

        if ($this->invoice->created_at->format('Y-m') != now()->format('Y-m')) {
            // se le hace void
            return self::REVERSE;
        }

        return self::VOID;

    }

    public function voidInvoice(Chronometer $timer, AvalaraLog $log, string $type = 'void'): void
    {
        $timer->start('void');
        Log::debug("Going to un commit Invoice: {$this->invoice->invoice_number}");
        $this->service->unCommitInvoice($this->invoice->invoice_number);
        $timer->end('void');
        $log->procesing_time = $timer->totalExecutionMilliseconds('void');
        $log->operation_type = $type == 'void' ? AvalaraLog::OPERATION_VOID : AvalaraLog::OPERATION_EDIT;
        $log->save();
        $this->avalaraInvoice->status = $type == 'void' ? AvalaraInvoice::STATUS_VOID : AvalaraInvoice::STATUS_EDITED;
        $this->avalaraInvoice->save();
    }

    public function reverseInvoice(Chronometer $timer, AvalaraLog $log): void
    {
        // Log::debug(json_encode($this->invoice->avalaraLog->jsonRequest));
        $this->service->prepareForVoid($this->invoice->avalaraLog->jsonRequest);
        //Log::debug('Pasado Prepared');
        $this->avalaraInvoice->status = AvalaraInvoice::STATUS_REVERSE;
        $timer->start('void');
        // Log::debug('Time Started');
        $response = $this->service->getTaxes();
        $timer->end('void');
        $log->procesing_time = $timer->totalExecutionMilliseconds('void');
        $log->operation_type = AvalaraLog::OPERATION_REVERSE;
        $log->response = json_encode($response);
        $log->request = json_encode($this->service->request->body);
        //  Log::debug('before save');
        $log->save();
        // Log::debug('before save');
        $this->avalaraInvoice->status = AvalaraInvoice::STATUS_REVERSE;
        $this->avalaraInvoice->save();

    }

    /**
     * @throws Throwable
     */
    public function voidOrReverseInvoice()
    {
        $type = $this->getInvoiceType();
        $timer = new Chronometer();
        $log = $this->getNewLog();

        try {
            switch ($type) {
                case self::VOID:
                    $this->voidInvoice($timer, $log);

                    break;
                case self::REVERSE:
                    $this->reverseInvoice($timer, $log);

                    break;
                default:
                    // default code
                    break;
            }
        } catch (Throwable $th) {
            Log::error($th->getMessage());

            throw $th;
        }

    }

    /**
     * @throws Exception
     */
    public function editInvoice()
    {
        //if ($this->getInvoiceType() == self::REVERSE) throw new Exception('Only current month Avalara invoice can be edited');
        switch ($this->getInvoiceType()) {
            case self::REVERSE:
                $this->editDiffMonthInvoice();

                break;
            case self::VOID:
                $this->editCurrentMonthInvoice();

                break;
            default:
                break;
        }
    }

    private function editCurrentMonthInvoice()
    {
        $timer = new Chronometer();
        $log = $this->getNewLog();

        $this->voidInvoice($timer, $log, 'edit');

        $this->invoice->updateInvoiceNumberAndAvalaraCode();
        Artisan::call('pbx:generateAvalaraTax', ['invoice_id' => $this->invoice->id]);
    }

    private function editDiffMonthInvoice()
    {
        $timer = new Chronometer();
        $log = $this->getNewLog();

        $this->reverseInvoice($timer, $log);

        $this->invoice->updateInvoiceNumberAndAvalaraCode();
        Artisan::call('pbx:generateAvalaraTax', ['invoice_id' => $this->invoice->id]);
    }

    private function getNewLog(): AvalaraLog
    {
        return new AvalaraLog([
            'invoice_id' => $this->invoice->id,
            'type' => 1,
            'status' => AvalaraLog::STATUS_SUCCESS,
            'pbx_service_id' => $this->invoice->pbx_service_id,
            'user_id' => $this->invoice->user_id,
            'avalara_log_id' => $this->invoice->avalaraLog->id,
        ]);
    }
}
