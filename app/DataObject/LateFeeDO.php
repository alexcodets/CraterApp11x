<?php

namespace Crater\DataObject;

use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\InvoiceLateFee;
use Crater\Models\Tax;
use Crater\Models\TaxType;
use Exception;

class LateFeeDO
{
    public string $name;

    public int $days;

    public string $type;

    public $tax;

    public $total;

    public $amount;
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    private Invoice $invoice;

    /**
     * @var float|int
     */
    public $fee;

    public function __construct(string $name, Invoice $invoice, string $type, $amount)
    {
        $this->name = $name;
        $this->days = $invoice->days_overdue;
        $this->type = $type;
        $this->amount = $amount;
        $this->tax = 0;
        $this->fee = 0;
        $this->invoice = $invoice;
        // fee, tax, total, amount
    }

    public function isNew(): bool
    {
        return InvoiceLateFee::where('notice', $this->name)->where('invoice_id', '=', $this->invoice->id)
            ->doesntExist();
    }

    /**
     * @throws Exception
     */
    public function getType(): int
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return 0;
        }

        if ($this->type === self::TYPE_FIXED) {
            return 1;
        }

        throw new Exception('Invoice Late Feed Type is not valid');

    }

    /**
     * @throws Exception
     */
    public function calculateTotals()
    {
        $this->updateFee();
        $this->updateTaxes();
    }

    /**
     * @throws Exception
     */
    private function updateFee(): void
    {
        if ($this->type === LateFeeDO::TYPE_FIXED) {
            $this->fee = $this->amount * 100;

            return;
        }

        if ($this->type === LateFeeDO::TYPE_PERCENTAGE) {
            $this->fee = intval(round($this->invoice->sub_total * ($this->amount / 100)), 0);

            return;
        }

        throw new Exception('Invoice Late Feed Type is not valid');

    }

    private function updateTaxes()
    {
        $taxes = $this->invoice->taxes;

        if ($this->invoice->tax_per_item === "YES") {

            // Obtener los tax_type_id únicos directamente relacionados con la factura
            $taxTypeIds = InvoiceItem::where('invoice_id', $this->invoice->id)
                ->with(['taxes' => function ($query) {
                    $query->select('tax_type_id')->distinct();
                }])
                ->get()
                ->pluck('taxes.*.tax_type_id')
                ->collapse()
                ->unique();

            // Consultar la tabla tax_type y obtener los objetos TaxType
            $taxes = TaxType::whereIn('id', $taxTypeIds)->get();

        }
        foreach ($taxes as $tax) {
            $this->tax += $tax->percent / 100 * $this->fee;
        }
        $this->total = $this->fee + $this->tax;
    }
}
