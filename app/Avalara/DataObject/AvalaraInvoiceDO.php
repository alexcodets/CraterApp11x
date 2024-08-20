<?php

namespace Crater\Avalara\DataObject;

use Crater\Models\AvalaraConfig;
use Crater\Models\Invoice;
use Crater\Models\User;
use Exception;

class AvalaraInvoiceDO
{
    public const TIME_FORMAT = 'Y-m-d\TH:i:s';

    public $date;

    public $invoice_number;

    public $customer_ref;

    public ?string $id;

    public ?string $avalara_invoice_number;

    public string $billCicleReference;

    public string $currency_code;

    public array $bill_period;

    public ?bool $invoice_mode;

    public ?bool $detail_line;

    public ?bool $response_summary;

    public ?bool $life_line;

    public bool $commit;

    public ?int $customer_type;

    /**
     * @throws Exception
     */
    public function __construct($date, bool $commit, AvalaraConfig $config, User $user, Invoice $invoice = null)
    {
        try {
            $this->date = $invoice ? date(self::TIME_FORMAT, strtotime($invoice->invoice_date->format('Y-m-d\TH:i:s'))) : $date;
        } catch (\Throwable $th) {
            throw new Exception(__('avalara.error.invoice.date'));
        }

        $this->commit = $commit;
        $this->id = $invoice->avalara_document_code ?? null;
        //$this->bill_cicle = $invoice;
        $this->bill_period = [
            'month' => $invoice ? $invoice->invoice_date->format('m') : now()->format('m'),
            'year' => $invoice ? $invoice->invoice_date->year : now()->year,
        ];
        $this->billCicleReference = $invoice ? $invoice->invoice_date->format('F') : now()->format('F');
        //TODO: Default currency
        $this->currency_code = $user->currency->code ?? 'USD';
        $this->customer_ref = $user->customcode ?? null;
        $this->invoice_number = $invoice->invoice_number ?? null;
        $this->avalara_invoice_number = $invoice->avalara_invoice_number ?? null;
        //
        $this->invoice_mode = $invoice->avalara_invm ?? $config->invm;
        $this->customer_type = $user->avalara_type ?? null;
        $this->life_line = $user->lfln ?? null;
    }

    public function toArray(): array
    {
        return array_filter([
            'doc' => $this->id,
            'cmmt' => $this->commit,
            'date' => $this->date,
            'ccycd' => $this->currency_code,
            'bcyc' => $this->billCicleReference,
            'bpd' => $this->bill_period,
            'invn' => $this->avalara_invoice_number,
            'custref' => $this->customer_ref,
            'cust' => $this->customer_type,
            'lfln' => $this->life_line,
            'invm' => $this->invoice_mode,
            //'acct' => '',
            //'is'
        ], function ($v) {
            return ! is_null($v);
        });
    }
}
