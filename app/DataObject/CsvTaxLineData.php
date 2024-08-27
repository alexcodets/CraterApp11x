<?php

namespace Crater\DataObject;

class CsvTaxLineData
{
    public const int LINE = 0;
    public const int DISCOUNT = 2;
    public const int TAX = 1;

    public string $invoiceNumber;

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

    public int $type;

    public function __construct(
        string  $invoiceNumber,
        ?string $itemDiscountType,
        ?float $itemDiscountVal,
        ?float $itemDiscount,
        ?string $taxName,
        ?int $taxPercentage,
        ?float $taxAmount,
        ?string $taxItem
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->itemDiscountType = $itemDiscountType;
        $this->itemDiscountVal = $itemDiscountVal;
        $this->itemDiscount = $itemDiscount;
        $this->taxName = $taxName;
        $this->taxPercentage = $taxPercentage;
        $this->taxAmount = $taxAmount;
        $this->taxItem = $taxItem;
        $this->type = $taxName ? self::TAX : self::DISCOUNT;
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $line): self
    {
        $line = array_map(function ($value) {
            return empty($value) ? null : $value;
        }, $line);

        if ($line[31] == '' && $line[27] == '') {
            throw new \Exception('A line can not be empty');
        }

        return new self(
            $line[0],
            $line[27],
            $line[28],
            $line[29],
            $line[31] ?? null,
            $line[32] ?? null,
            $line[33] ?? null,
            $line[34] ?? null
        );
    }
}
