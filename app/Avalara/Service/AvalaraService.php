<?php

namespace Crater\Avalara\Service;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\DataObject\AvalaraBillingDO;
use Crater\Avalara\DataObject\AvalaraCompanyDO;
use Crater\Avalara\DataObject\AvalaraInvoiceDO;
use Crater\Avalara\DataObject\AvalaraTaxRequestDO;
use Crater\Avalara\DataObject\AvalaraTaxRequestFactory;
use Crater\Avalara\DataObject\AvalaraTaxResponseDO;
use Crater\Models\AvalaraConfig;
use Illuminate\Support\Facades\Log;

class AvalaraService
{
    public AvalaraTaxRequestDO $request;

    public AvalaraApi $api;

    public function __construct(AvalaraApi $api)
    {
        $this->api = $api;
    }

    public function prepareForTax(AvalaraInvoiceDO $invoice, AvalaraBillingDO $billing, AvalaraCompanyDO $company, AvalaraConfig $config): void
    {
        $this->request = AvalaraTaxRequestFactory::fromModels($billing, $company, $invoice, $config);
    }

    public function prepareForVoid(object $values)
    {
        $this->request = AvalaraTaxRequestFactory::fromJsonObject($values);
        $this->request->fullVoidBody();
    }

    public function getTaxes(): array
    {

        $values = $this->api->getTaxes($this->request->body);

        if ($values['success'] === false) {
            //Log::debug('Is not success inside the getTaxes');
            return $values;
        }

        return (new AvalaraTaxResponseDO())->getData($values);
    }

    public function addCharge($chg, int $transaction, int $service, int $sale, string $ref, int $line = 0, bool $adj = false): void
    {
        $this->request->addInvoiceLine([
            'ref' => $ref,
            'chg' => $chg,
            'line' => $line,
            'sale' => $sale,
            'tran' => $transaction,
            'serv' => $service,
            "qty" => 1,
            'adj' => $adj,
        ]);
    }

    public function addChargeAdj($chg, int $transaction, int $service, int $sale, string $ref, int $line = 0, int $disc = 0): void
    {
        $this->request->addInvoiceLine([
            'ref' => "$ref - Adjustment with discount type {$disc}",
            'chg' => $chg,
            'line' => $line,
            'sale' => $sale,
            'tran' => $transaction,
            'serv' => $service,
            'adj' => true,
            'adjm' => 0,
            'disc' => $disc,
        ]);
    }

    public function addLineAdj(int $line, int $transaction, int $service, int $sale, string $ref, int $disc = 0): void
    {
        $this->request->addInvoiceLine([
            'ref' => "$ref - Adjustment with discount type {$disc}",
            'line' => $line,
            'chg' => 0,
            'sale' => $sale,
            'tran' => $transaction,
            'serv' => $service,
            'adj' => true,
            'adjm' => 0,
            'disc' => $disc,
        ]);
    }

    public function addExemption(array $item)
    {
        $this->request->addExemptionItem($item);
    }

    public function addLine(int $line, int $transaction, int $service, int $sale, string $ref, bool $adj = false): void
    {
        $this->request->addInvoiceLine([
            'ref' => $ref,
            'line' => $line,
            'chg' => 0,
            'sale' => $sale,
            'tran' => $transaction,
            'serv' => $service,
            "qty" => 1,
            'adj' => $adj,
        ]);
    }

    public function addBundle(int $price, string $transaction, string $service, string $name, bool $adjm = false): void
    {
        $this->request->addBundleItem([
            'ref' => $name,
            'chg' => $price,
            'sale' => 1,
            'tran' => $transaction,
            'serv' => $service,
            //"qty" => 1,
            //'adj' => $adjm,
        ]);

    }

    public function commitInvoice(string $doc, array $values = []): array
    {
        return $this->api->commitDoc($doc, $values);
    }

    public function unCommitInvoice(string $doc, array $values = []): array
    {
        return $this->api->unCommitDoc($doc, $values);
    }

    public function serverStatus(): array
    {
        return $this->api->isHealthy();
    }

    public function getPCode(array $location): array
    {
        return $this->api->locationPCode($location);
    }

    public function getTsPair(): array
    {
        return $this->api->getTSPair();
    }

    public function getTaxTypes($type = '*'): array
    {
        return $this->api->getTaxTypes($type);
    }
}
