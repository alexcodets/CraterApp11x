<?php

namespace Crater\DataObject;

class CsvInvoiceFullData
{
    public const int LINE = 0;
    public const int DISCOUNT = 2;
    public const int TAX = 1;

    public string $invoiceNumber;

    public ?string $customerCode;

    public ?string $customer;

    public ?bool $taxPerItem;

    public ?bool $discountPerItem;

    public ?float $subTotal;

    public ?float $total;

    public ?string $customerName;

    public ?string $invoiceDate;

    public ?string $dueDate;

    public ?string $notes;

    public ?string $status;

    public ?string $paidStatus;

    public ?string $discountType;

    public ?int $discountVal;

    public ?string $discounts;

    public ?float $totalTax;

    public ?float $amountDue;

    public ?string $pbxServiceNumber;

    public ?string $serviceNumber;

    public ?string $servicePeriodFrom;

    public ?string $servicePeriodTo;

    public ?string $itemType;

    public ?string $itemName;

    public ?string $itemNumber;

    public ?string $itemDescription;

    public ?int $itemQuantity;

    public ?float $itemRate;

    public ?string $itemDiscountType;

    public ?float $itemDiscountVal;

    public ?int $itemDiscount;

    public ?int $itemAmount;

    public ?string $taxName;

    public ?int $taxPercentage;

    public ?float $taxAmount;

    public ?string $taxItem;
    private int $type;

    public function __construct(
        string  $invoiceNumber,
        ?string $customerCode,
        ?string $customerName,
        ?string $invoiceDate,
        ?string $dueDate,
        ?string $notes,
        ?string $status,
        ?string $paidStatus,
        ?bool $taxPerItem,
        ?bool $discountPerItem,
        ?int $subTotal,
        ?string $discountType,
        ?int $discountVal,
        ?string $discounts,
        ?float $totalTax,
        ?float $total,
        ?float $amountDue,
        ?string $pbxServiceNumber,
        ?string $serviceNumber,
        ?string $servicePeriodFrom,
        ?string $servicePeriodTo,
        ?string $itemType,
        ?string $itemNumber,
        ?string $itemName,
        ?string $itemDescription,
        ?int $itemQuantity,
        ?float    $itemRate,
        ?string $itemDiscountType,
        ?float $itemDiscountVal,
        ?float $itemDiscount,
        ?int    $itemAmount,
        ?string $taxName,
        ?int $taxPercentage,
        ?float $taxAmount,
        ?string $taxItem
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->customerCode = $customerCode;
        $this->customerName = $customerName;
        $this->invoiceDate = $invoiceDate;
        $this->dueDate = $dueDate;
        $this->notes = $notes;
        $this->status = $status;
        $this->paidStatus = $paidStatus;
        $this->taxPerItem = $taxPerItem;
        $this->discountPerItem = $discountPerItem;
        $this->subTotal = $subTotal;
        $this->discountType = $discountType;
        $this->discountVal = $discountVal ?? 0;
        $this->discounts = $discounts ?? 0;
        $this->totalTax = $totalTax;
        $this->total = $total;
        $this->amountDue = $amountDue;
        $this->pbxServiceNumber = $pbxServiceNumber;
        $this->serviceNumber = $serviceNumber;
        $this->servicePeriodFrom = $servicePeriodFrom;
        $this->servicePeriodTo = $servicePeriodTo;
        $this->itemType = $itemType;
        $this->itemNumber = $itemNumber;
        $this->itemName = $itemName;
        $this->itemDescription = $itemDescription;
        $this->itemQuantity = $itemQuantity;
        $this->itemRate = $itemRate;
        $this->itemDiscountType = $itemDiscountType;
        $this->itemDiscountVal = $itemDiscountVal;
        $this->itemDiscount = $itemDiscount;
        $this->itemAmount = $itemAmount;
        $this->taxName = $taxName;
        $this->taxPercentage = $taxPercentage;
        $this->taxAmount = $taxAmount;
        $this->taxItem = $taxItem;
        $this->type = self::LINE;
    }

    public static function fromArray(array $line): self
    {

        $line = array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $line);

        return new self(
            $line[0],
            $line[1] ?? null,
            $line[2] ?? null,
            $line[3],
            $line[4],
            $line[5],
            $line[6],
            $line[7],
            $line[8] === 'YES',
            $line[9] === 'YES',
            $line[10],
            $line[11] ?? null,
            $line[12],
            $line[13],
            $line[14] ?? 0,
            $line[15] ?? 0,
            $line[16],
            $line[17],
            $line[18],
            $line[19],
            $line[20],
            $line[21],
            $line[22],
            $line[23],
            $line[24] ?? null,
            $line[25],
            $line[26],
            $line[27],
            $line[28],
            $line[29],
            $line[30],
            $line[31] ?? null,
            $line[32] ?? null,
            $line[33] ?? null,
            $line[34] ?? null
        );
    }

    public function getTaxPerItemString(): string
    {
        return $this->taxPerItem === true ? 'YES' : 'NO';
    }

    public function getDiscountPerItemString(): string
    {
        return $this->discountPerItem === true ? 'YES' : 'NO';
    }

    public function isAService(): bool
    {
        return $this->pbxServiceNumber || $this->serviceNumber;
    }
}
