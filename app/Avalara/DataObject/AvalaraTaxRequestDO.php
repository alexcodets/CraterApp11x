<?php

namespace Crater\Avalara\DataObject;

class AvalaraTaxRequestDO
{
    public const TIME_FORMAT = 'Y-m-d\TH:i:s';

    public $body;

    public $invoice_id;

    public function __construct(array $body)
    {
        $this->body = $body;
    }

    public function getData(): array
    {
        return $this->body;
    }

    public function addInvoiceLine(array $item = []): void
    {
        $this->body['inv'][0]['itms'][] = $item;
    }

    public function addExemptionItem(array $item = []): void
    {
        $this->body['exms'][] = $item;
    }

    public function addBundleItem(array $item = []): void
    {
        $this->body['inv'][0]['itms'][] = $item;
    }

    /**
     * set Adj = true for invoice items.
     *
     * @return void
     */
    public function fullVoidBody(): void
    {
        $items = [];
        foreach ($this->body['inv'][0]['itms'] as $itm) {
            $itm['adj'] = true;
            $items[] = $itm;
        }
        $this->body['inv'][0]['itms'] = $items;
    }
}
