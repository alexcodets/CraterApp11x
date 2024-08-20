<?php

namespace Crater\DataObject;

use Crater\Models\PaymentGatewaysFee;

class PaymentAmountData
{
    private bool $hasFee;

    private array $ids;

    public array $fees;

    public float $totalFees;

    public int $amount;

    public function __construct(int $amount, ?bool $hasFee = null, ?array $feesIds = [])
    {
        $hasFee ??= false;
        $this->hasFee = $hasFee;
        $this->ids = $feesIds ?? [];
        $this->amount = $amount;
        \Log::debug(
            'data Payment Amount data',
            [
                'hasFee' => $this->hasFee,
                'ids' => $this->ids,
                'amount' => $this->amount,
            ]
        );
        if ($hasFee) {
            \Log::debug('Has Fee');
            $this->generateFees();
        } else {
            $this->totalFees = 0;
            $this->fees = [];
        }
    }

    public function generateFees(): void
    {
        $fees = PaymentGatewaysFee::select(['id', 'name', 'type', 'amount'])->find($this->ids);
        $this->totalFees = 0;
        $fees->each(function (PaymentGatewaysFee $fee) {

            if ($fee->type == 'percentage') {
                // $fee->amount is decimal so 100 => 1%
                $total = $this->amount * $fee->amount / 10_000;
            } else {
                $total = $fee->amount;
            }

            $this->totalFees += $total;

            $this->fees[] = [
                'id' => $fee->id,
                'name' => $fee->name,
                'type' => $fee->type,
                'amount' => $fee->amount,
                'total' => $total,
            ];
        });

    }

    public function getTotal(): int
    {
        return $this->amount + $this->totalFees;
    }

    public function getAmountMini()
    {
        return $this->amount / 100;
    }

    /* (amount + fees) / 100*/
    public function getTotalMini()
    {
        return $this->getTotal() / 100;
    }
}
