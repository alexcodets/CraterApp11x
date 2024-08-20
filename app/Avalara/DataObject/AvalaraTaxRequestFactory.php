<?php

namespace Crater\Avalara\DataObject;

use Crater\Models\AvalaraConfig;

class AvalaraTaxRequestFactory
{
    public static function fromArray(array $array): AvalaraTaxRequestDO
    {
        return new AvalaraTaxRequestDO($array);
    }

    public static function fromJsonObject(object $object): AvalaraTaxRequestDO
    {
        return new AvalaraTaxRequestDO(json_decode(json_encode($object), true));
    }

    public static function fromModels(AvalaraBillingDO $billing, AvalaraCompanyDO $company, AvalaraInvoiceDO $invoice, AvalaraConfig $config): AvalaraTaxRequestDO
    {
        $companyData = $company->getCompanyData();
        $billing = $billing->getBillingData();

        $inv = array_merge(
            $invoice->toArray(),
            [
                'bill' => $billing,
                'acct' => $company->accountReference,
                //'summ' => $config->summ,
                //'dtl'  => $config->dtl,
                'dtl' => true,
            ]
        );
        $body = [
            'cfg' => $config->cfg,
            'cmpn' => $companyData,
            'inv' => [
                $inv,
            ],
        ];

        return new AvalaraTaxRequestDO($body);

    }
}
