<?php

namespace Crater\DataObject;

use Crater\Models\PbxServices;

class ServiceRecalculateData
{
    public ?float $extensionsTotal;

    public ?int $extensionsCount;

    public ?float $didTotal;

    public ?int $didCount;

    public ?float $itemsTotal;

    public ?int $itemsCount;

    public ?float $taxItems;

    public ?float $taxService;

    public ?float $appRateTotal;

    public ?int $appRateCount;

    public ?float $packagePrice;

    public ?float $additionalChargeTotal;

    public ?float $discountTotal;

    private PbxServices $service;

    public function __construct(PbxServices $service)
    {
        $this->service = $service;
        $this->packagePrice = $service->pbxpackages_price / 100;
    }

    public function getSubTotal(): float
    {
        return $this->additionalChargeTotal + $this->didTotal + $this->extensionsTotal + $this->itemsTotal + $this->appRateTotal + $this->packagePrice;
    }

    public function getSubTotalNoItems(): float
    {
        return $this->getSubTotal() - $this->itemsTotal;
    }

    public function getSubTotalAfterDiscount(): float
    {
        return max(($this->getSubTotal() - $this->discountTotal), 0);
    }

    public function getSubTotalNoItemAfterDiscount(): float
    {
        return max(($this->getSubTotalAfterDiscount() - $this->itemsTotal), 0);
    }

    public function getSubTotalForTax(): float
    {
        //cuando es I no se agrega lo de los items.
        return $this->service->apply_tax_type == 'I' ? $this->getSubTotalNoItemAfterDiscount() : $this->getSubTotalAfterDiscount();
    }

    public function getTaxTotal(): float
    {
        if ($this->service->apply_tax_type == 'I') {
            return ($this->taxItems + $this->taxService) ?? 0;
        }

        return $this->taxService ?? 0;
    }

    public function getTotalService(): float
    {
        return $this->getSubTotalAfterDiscount() + $this->getTaxTotal();

    }
}
