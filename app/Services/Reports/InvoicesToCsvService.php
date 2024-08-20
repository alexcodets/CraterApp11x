<?php

namespace Crater\Services\Reports;

use Crater\Models\Invoice;
use Crater\Models\InvoiceAdditionalCharge;
use Crater\Models\InvoicePbxDidDetail;
use Crater\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicesToCsvService
{
    private string $csv;

    private string $path;

    public int $lineCount;

    public int $totalLineCount;

    public function handle(User $user, ?array $filters = []): StreamedResponse
    {
        $name = $user->company_id.'_invoice.csv';
        $this->path = "/invoices/{$name}";
        //['InvoiceNo'","'Customer'","'InvoiceDate'","'DueDate'","'Terms'","'Location'","'Memo'","'Item(Product/Service)'","'ItemDescription'","'ItemQuantity'","'ItemRate'","'ItemAmount'","'Service Date']
        $header = 'InvoiceNo,Customer,Email,InvoiceDate,DueDate,Terms,Location,Memo,Status,PaidStatus,Subtotal,Discounts,TotalTax,Total,AmountDue,';
        $header = $header.'Item(Product/Service),ItemDescription,ItemQuantity,ItemRate,ItemDiscount,ItemAmount,Service Date,';

        $invoices = $this->getInvoices($user, $filters);

        if ($invoices->isEmpty()) {
            abort(404, 'No records found');
        }

        Storage::disk('reports')->put($this->path, $header);
        $this->lineCount = 1;
        $this->totalLineCount = 1;
        foreach ($invoices as $invoice) {
            Log::debug($invoice);
            $this->lineCount = 0;
            /* @var Invoice $invoice */

            $this->csv = "\"{$invoice->invoice_number}\",\"";
            $this->csv .= "{$invoice->user->name}\",\"";
            $this->csv .= $invoice->user->email ?? 'N/A';
            $this->csv .= '","';
            $this->csv .= $invoice->invoice_date->format('d/m/Y').'","';
            $this->csv .= "{$invoice->due_date}\",\"";
            $this->csv .= null.'","';
            $this->csv .= $invoice->addresses->address_street_1 ?? null.'","';
            $this->csv .= $invoice->notes.'","';
            $this->csv .= $invoice->status.'","';
            $this->csv .= $invoice->paid_status.'","';
            $this->csv .= $invoice->sub_total / 100 .'","';

            $this->csv .= $invoice->discount / 100 .'","';
            $this->csv .= $invoice->tax / 100 .'","';
            $this->csv .= $invoice->total / 100 .'","';
            $this->csv .= $invoice->due_amount / 100 .'",';

            try {
                $this->setInvoiceItems($invoice);
                $this->setAdditionalCharges($invoice);
                $this->setPbxAppRates($invoice);
                $this->setExtensions($invoice);
                $this->setDid($invoice);
            } catch (\Throwable $th) {
                //throw $th;
                if ($th->getCode() == 80085) {

                    $file = fopen(Storage::disk('reports')->path($this->path), 'r+');
                    Log::debug('To many invoices');
                    if ($file) {

                        $fullPath = Storage::disk('reports')->path($this->path);
                        $toDelete = 1000 - $this->lineCount;
                        $skipChar = 0;

                        for ($i = 1000; $i > $toDelete; $i--) {
                            $skipChar += strlen(file($fullPath)[$i]);
                        }

                        //Csv total char count.
                        $totalChar = filesize($fullPath);

                        // Move the file pointer to the end of the line 999/ToDelete.
                        fseek($file, $totalChar - $skipChar);
                        $newPosition = ftell($file);

                        ftruncate($file, $newPosition);

                        // Close the file
                        fclose($file);

                        break;
                    } else {
                        Log::debug('File not found');
                    }
                }

                throw $th;
            }

        }
        $headers = [
            //'ext' => pathinfo($name, PATHINFO_EXTENSION),
            'Content-Type' => 'text/csv',
        ];

        return Storage::disk('reports')->download($this->path, 'invoice.csv', $headers);

    }

    /**
     * @throws Exception
     */
    public function updateLineCount()
    {
        $this->lineCount++;
        $this->totalLineCount++;

        if ($this->totalLineCount > 1_000) {
            Log::error('Max line count reached: '.$this->totalLineCount);

            throw new Exception('Max line count reached', 80085);
        }

    }

    public function setInvoiceItems(Invoice $invoice)
    {
        foreach ($invoice->items as $item) {

            if ($item !== $invoice->items[0]) {
                $this->csv .= '"'.$invoice->invoice_number.'",';
                $this->csv .= ',,,,,,,,,,,,,,';
            }
            $this->csv .= '"';
            $this->csv .= sprintf('%s:%s","', $item->item?->itemCategory?->name ?? 'Custom', $item->name);
            $this->csv .= $item->description.'","';
            $this->csv .= $item->quantity.'","';
            $this->csv .= $item->price / 100 .'","';
            $this->csv .= $item->discount.'","';
            $this->csv .= $item->total / 100 .'","';
            $this->csv .= $invoice->invoice_date->format('d/m/Y').'",';

            Storage::disk('reports')->append($this->path, $this->csv);
            $this->csv = '';
            $this->updateLineCount();
        }
    }

    public function setAdditionalCharges($invoice)
    {

        foreach ($invoice->additionalCharges as $additional) {
            /*  @var InvoiceAdditionalCharge $additional */
            if ($this->csv === '') {
                $this->csv .= '"'.$invoice->invoice_number.'",';
                $this->csv .= ',,,,,,,,,,,,,,';
            }

            $this->csv .= '"PBX Service:Additional Charge","';
            $this->csv .= $additional->additional_charge_name.'","';
            $this->csv .= $additional->qty.'","';
            $this->csv .= $additional->additional_charge_amount.'","';
            $this->csv .= 0 .'","';
            $this->csv .= $additional->total.'","';
            $this->csv .= $invoice->invoice_date->format('d/m/Y').'",';
            Storage::disk('reports')->append($this->path, $this->csv);
            $this->csv = '';
            $this->updateLineCount();

        }
    }

    public function setPbxAppRates(Invoice $invoice)
    {
        foreach ($invoice->pbxAppRates as $rate) {
            if ($this->csv === '') {
                $this->csv .= '"'.$invoice->invoice_number.'",';
                $this->csv .= ',,,,,,,,,,,,,,';
            }
            $this->csv .= '"PBX Service:App Rate","';
            $this->csv .= $rate->app_name.'","';
            $this->csv .= $rate->quantity.'","';
            $this->csv .= $rate->costo.'","';
            $this->csv .= 0 .'","';
            $this->csv .= $rate->quantity * $rate->costo.'","';
            $this->csv .= $invoice->invoice_date->format('d/m/Y').'",';
            Storage::disk('reports')->append($this->path, $this->csv);
            $this->csv = '';
            $this->updateLineCount();

        }
    }

    public function setExtensions(Invoice $invoice)
    {
        foreach ($invoice->extensionDetails as $ext) {
            if ($this->csv === '') {
                $this->csv .= '"'.$invoice->invoice_number.'",';
                $this->csv .= ',,,,,,,,,,,,,,';
            }
            $this->csv .= '"PBX Service:Extension","';
            $this->csv .= $ext->name.'","';
            $this->csv .= $ext->quantity.'","';
            $this->csv .= $ext->price.'","';
            $this->csv .= 0 .'","';
            $this->csv .= $ext->total.'","';
            $this->csv .= $invoice->invoice_date->format('d/m/Y').'",';
            Storage::disk('reports')->append($this->path, $this->csv);
            $this->updateLineCount();
            $this->csv = '';

        }
    }

    public function setDid(Invoice $invoice)
    {
        foreach ($invoice->didDetails as $did) {
            /*  @var InvoicePbxDidDetail $did */
            if ($this->csv === '') {
                $this->csv .= '"'.$invoice->invoice_number.'",';
                $this->csv .= ',,,,,,,,,,,,,,';
            }
            $this->csv .= '"PBX Service:DID","';
            $this->csv .= $did->name.'","';
            $this->csv .= $did->quantity.'","';
            $this->csv .= $did->price.'","';
            $this->csv .= 0 .'","';
            $this->csv .= $did->total.'","';
            $this->csv .= $invoice->invoice_date->format('d/m/Y').'",';
            Storage::disk('reports')->append($this->path, $this->csv);
            $this->csv = '';
            $this->updateLineCount();

        }
    }

    public function getInvoices(User $user, array $filters): ?Collection
    {

        \Log::debug($filters);

        return Invoice::query()
            ->whereNull('deleted_at')
            ->where('company_id', $user->company_id)
            ->when($filters['from_date'] ?? null && $filters['to_date'] ?? null, function (Builder $query) use ($filters) {
                return $query->whereBetween('invoice_date', [$filters['from_date'], $filters['to_date']]);
            })
            ->when($filters['customer_id'] ?? null, function (Builder $query) use ($filters) {
                return $query->where('user_id', $filters['customer_id']);
            })
//            ->when($filters['from'] ?? null , function (Builder $query) use ($filters) {
//                return $query->where('invoice_date', '>=', $filters['from']);
//            })
//            ->when($filters['to'] ?? null , function (Builder $query) use ($filters) {
//                return $query->where('invoice_date', '<=', $filters['to']);
//            })
            ->when($filters['status'] ?? null, function (Builder $query) use ($filters) {
                if ($filters['status'] == Invoice::STATUS_DUE) {
                    return $query->dueInvoiceStatus();
                }
                if ($filters['status'] == Invoice::STATUS_UNPAID || $filters['status'] == Invoice::STATUS_PARTIALLY_PAID || $filters['status'] == Invoice::STATUS_PAID) {
                    return $query->where('paid_status', $filters['status']);
                }

                return $query->where('status', $filters['status']);
            })
            ->when($filters['customcode'] ?? null, function (Builder $query) use ($filters) {
                return $query->whereHas('user', function ($query) use ($filters) {
                    $query->where('customcode', 'LIKE', '%'.$filters['customcode'].'%');
                });
            })
            ->when($filters['invoice_number'] ?? null, function (Builder $query) use ($filters) {
                return $query->where('invoice_number', 'LIKE', '%'.$filters['invoice_number'].'%');
            })
            ->when($filters['order_by'] ?? null, function (Builder $query) use ($filters) {
                //orderby= invoice_number,invoice_date,name,status,paid_status,total,due_amount
                if ($filters['order_by'] == 'invoice_number') {
                    return $query->orderBy('invoice_number', $filters['order']);
                }
                if ($filters['order_by'] == 'invoice_date') {
                    return $query->orderBy('invoice_date', $filters['order']);
                }
                if ($filters['order_by'] == 'status') {
                    return $query->orderBy('status', $filters['order']);
                }
                if ($filters['order_by'] == 'paid_status') {
                    return $query->orderBy('paid_status', $filters['order']);
                }
                if ($filters['order_by'] == 'total') {
                    return $query->orderBy('total', $filters['order']);
                }
                if ($filters['order_by'] == 'due_amount') {
                    return $query->orderBy('due_amount', $filters['order']);
                }

                return $query;
            })
            ->with(['items', 'addresses', 'additionalCharges', 'pbxAppRates', 'extensionDetails', 'didDetails', 'user'])
            ->limit(100)
            ->get();
    }
}
