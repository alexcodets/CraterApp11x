<?php

namespace Crater\Avalara\Service;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Helpers\Chronometer;
use Crater\Models\AvalaraInvoice;
use Crater\Models\AvalaraLog;
use Crater\Models\AvalaraTax;
use Crater\Models\AvalaraTaxCagetory;
use Crater\Models\AvalaraTaxType;
use Crater\Models\CustomRate;
use Crater\Models\Invoice;
use Crater\Models\Item;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Log;
use stdClass;
use Throwable;

class AvalaraAdjustmentServiceOld
{
    public Invoice $invoice;

    public ?PbxServices $pbxService = null;

    public ?PbxPackages $pbxPackage = null;

    public User $user;

    public stdClass $oldValues;

    public stdClass $allItems;

    /**
     * @throws Exception
     */
    public function handle(Invoice $invoice): bool
    {
        $this->invoice = $invoice;
        //$this->user = $invoice->user;
        //TODO: Check witTrashed
        //Nuevos Items
        if ($this->invoice->pbx_service_id) {
            $this->validatePackageAndService();
        }
        $this->validateUser();
        $chronos = new Chronometer();
        $service = new AvalaraTaxService($this->user, new AvalaraService(new AvalaraApi($this->invoice->company->avalaraConfiguration)));
        $validation = $service->validateUserData($this->invoice->company->avalaraConfiguration, $this->invoice, true);

        if ($validation['success'] === false) {
            Log::debug('User data is not valid');

            throw new Exception($validation['message']);
        }
        $invoiceItems = $this->getInvoiceItems($invoice);

        //TODO: Esto es boceto, mejorar
        // Item_id me puede servir para mapear los nuevos items con los viejos taxes.
        // group by item_id
        // AvalaraTax::get()->groupBy('item_id')

        $avalaraInvoice = $invoice->avalaraInvoice;
        $avalaraTaxesGroup = $avalaraInvoice->avalaraTaxes->groupBy('item_id');

        foreach ($invoiceItems as $invoiceItem) {
            $avalaraTaxes = $avalaraTaxesGroup[$invoiceItem->item_id] ?? null;
            if (! $avalaraTaxes) {
                continue;
                // There should not be new items, adj is to reduce the amount, not for new or bigger amount, so
                //Here, for these specific case the invoice should be deleted and a new one made.
            }

            //Reasigno los valores a quien pertenece.
            foreach ($avalaraTaxes as $item) {
                $item->invoice_item_id = $invoiceItem->id;
                $item->save();
            }

        }

        // Here I could use Operation_type to filter, something to have in mind.
        $oldValues = $invoice->avalaraLogs[0]->jsonRequest->inv[0]->itms;
        $invoiceItems = $this->getInvoiceItems($invoice);

        $avaLog = new AvalaraLog();
        $avaLog->invoice_id = $this->invoice->id;
        $avaLog->user_id = $this->user->id;
        $avaLog->operation_type = AvalaraLog::OPERATION_EDIT;

        try {
            if ($this->invoice->pbx_service_id == null) {
                /** @var $service AvalaraTaxService */
                /** @var $allItems Collection */
                $avaLog->type = AvalaraLog::INVOICE_MANUAL;
                list($service, $allItems) = $this->manual($invoiceItems, $oldValues, $service);

            } else {
                /** @var $service AvalaraTaxService */
                /** @var $allItems Collection */
                $avaLog->type = AvalaraLog::INVOICE_SERVICE;
                $avaLog->pbx_service_id = $this->pbxService->id;
                list($service, $allItems) = $this->invoice($invoiceItems, $oldValues, $service);
            }
        } catch (Throwable $th) {
            //throw $th;
            Log::debug('Error While Building the request for Avalara');
            Log::debug($th->getMessage());
            $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process End while Building the request ----|>");
            $avaLog->status = AvalaraLog::STATUS_ERROR;
            $avaLog->response = json_encode($th->getMessage());
            $avaLog->save();

            return false;
        }
        Log::debug($service->service->request->body);

        return true;

        try {
            $avaLog->request = json_encode($service->service->request->body);
            $chronos->start();
            $taxesResponse = $service->getTaxes();
            $chronos->end();
            $avaLog->procesing_time = $chronos->totalExecutionMilliseconds();
            Log::debug('   Get taxes is ready next we need to use that data');

        } catch (Throwable $th) {
            //throw $th;
            Log::debug($th->getMessage());
            $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process End while calculating the tax ----|>");

            return false;
        }

        //Log::debug($taxesResponse);
        //Log::debug($allItems);
        //Log::debug($taxesResponse);

        try {
            if ($taxesResponse['success'] === false) {
                Log::debug($taxesResponse['message']);
                //Log::debug('response', $taxesResponse);
                $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process End while validating the tax ----|>");
                $avaLog->status = AvalaraLog::STATUS_ERROR;
                $avaLog->response = json_encode($taxesResponse);
                $avaLog->save();

                return false;
            }
        } catch (\Throwable $th) {
            Log::debug('   Error while reading the taxes response, i guess');

        }

        try {

            $this->avalaraItems($taxesResponse, $allItems);
            $avaLog->response = json_encode($taxesResponse);
            $avaLog->status = AvalaraLog::STATUS_SUCCESS;
            $avaLog->save();

            return true;
        } catch (\Throwable $th) {
            Log::debug('   Error while procesing the taxes info response, i guess');
            Log::debug($th->getMessage());

            //throw $th;
            return false;
        }

    }

    /**
     * @throws Exception
     */
    private function invoice($invoiceItems, $oldValues, AvalaraTaxService $service): array
    {
        $allItems = collect();
        $disc = 0;
        $invoiceItem = new stdClass();

        if ($this->pbxPackage->avalaraBundle) {
            $this->logSection('Bundle');
            $noTaxable = $this->invoice->items()
                ->whereHas('item', function ($query) {
                    $query->where('no_taxable', 1);
                })->sum('total');

            if ($oldValues[0]->serv != $this->pbxPackage->bundleService) {
                throw new Exception('The service type for the bundle is wrong');
            }
            if ($oldValues[0]->tran != $this->pbxPackage->bundleTransaction) {
                throw new Exception('The service transaction for the bundle is wrong');
            }

            $bundle = (object)[
                'name' => $this->pbxPackage->pbx_package_name.$this->invoice->created_at->format('d/m/Y').' - Adjustment ',
                'price' => $oldValues[0]->chg - ($this->invoice->sub_total - $noTaxable) / 100,
                'transaction' => $this->pbxPackage->bundleTransaction,
                'service' => $this->pbxPackage->bundleService,
            ];

            $service->addBundleItem($bundle, $disc);

            return [$service, collect()];
        }

        if ($this->pbxPackage->avalara_extension) {
            $this->logSection('Extension');
            $item = $this->pbxPackage->avalaraExtension;

            if (is_null($item)) {
                throw new Exception('Avalara Extension Id null or incorrect');
            }
            $invoiceItem->quantity = $this->invoice->count_extension ?? 0;
            $invoiceItem->total = $this->invoice->pbx_total_extension ?? 0;

            $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

            if ($adjValues) {
                Log::debug('      Add Extension to edit body');
                $item->item_id = $item->id;
                $allItems[] = $item;
                $service->addTaxItemAdjForService($item, $adjValues, $disc);
            }

        }

        if ($this->pbxPackage->avalara_did) {
            $this->logSection('Did');
            $item = $this->pbxPackage->avalaraDid;

            if (is_null($item)) {
                throw new Exception('Avalara DID Id null or incorrect');
            }

            $invoiceItem->quantity = $this->invoice->count_did;
            $invoiceItem->total = $this->invoice->pbx_total_did ?? 0;

            $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

            if ($adjValues) {
                Log::debug('      Add Did to edit body');
                $item->item_id = $item->id;
                $allItems[] = $item;
                $service->addTaxItemAdjForService($item, $adjValues, $disc);
            }

        }

        if ($this->pbxPackage->avalara_callrating) {
            $this->logSection('CallRating');
            $item = $this->pbxPackage->avalaraCdr;

            if (is_null($item)) {
                throw new Exception('Avalara CDR Id null or incorrect');
            }

            $invoiceItem->quantity = $this->pbxService->pbxCdrTotalsForInvoice($this->invoice->id)->sum('exclusive_seconds') / 60;
            $invoiceItem->total = $this->pbxService->pbxCdrTotalsForInvoice($this->invoice->id)->sum('exclusive_cost');

            $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

            if ($adjValues) {
                Log::debug('      Add CallRating to edit body');
                $item->item_id = $item->id;
                $allItems[] = $item;
                $service->addTaxItemAdjForService($item, $adjValues, $disc);
            }

            $this->logSection('', '|-----Custom Rate Section Start-----|');

            //International
            if ($this->pbxPackage->inter_custom_destinations_item_id) {
                $this->logSection('International');

                $item = $this->pbxPackage->avalaraInternational;
                $this->customItem($allItems, $invoiceItem, $service, CustomRate::CATEGORY_INTERNATIONAL, $oldValues, $item);
            }

            //custom rate
            if ($this->pbxPackage->custom_destinations_item_id) {
                $this->logSection('Custom Destination');

                $item = $this->pbxPackage->avalaraCustomDestination;
                $this->customItem($allItems, $invoiceItem, $service, CustomRate::CATEGORY_CUSTOM, $oldValues, $item);
            }

            //Toll Free
            if ($this->pbxPackage->toll_free_custom_destinations_item_id) {
                $this->logSection('Tool Free Destination');

                $item = $this->pbxPackage->avalaraTollFree;
                $this->customItem($allItems, $invoiceItem, $service, CustomRate::CATEGORY_TOLL_FREE, $oldValues, $item);
            }

            $this->logSection('', '|-----Custom Rate Section End-----|');

        }

        if ($this->pbxPackage->avalara_custom_app_rate_item_id) {
            $this->logSection('AppRating');

            $item = $this->pbxPackage->avalaraAppRate;

            if (is_null($item)) {
                throw new Exception('Avalara Custom App Id null or incorrect');
            }

            $quantity = 0;
            foreach ($this->pbxService->pbxServicesAppRate as $app) {
                $quantity += $app->quantity;
            }
            $invoiceItem->quantity = $quantity;
            $invoiceItem->total = $this->invoice->pbx_total_apprate;

            $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

            if ($adjValues) {
                Log::debug('      Add AppCustomAppRate to edit body');
                $item->item_id = $item->id;
                $allItems[] = $item;
                $service->addTaxItemAdjForService($item, $adjValues, $disc);
            }

        }

        if ($this->pbxPackage->avalara_services_price_item) {
            $this->logSection('Pbx Service Price');
            $item = $this->pbxPackage->avalaraServicesPrice;

            if (is_null($item)) {
                throw new Exception('Avalara Custom App Id null or incorrect');
            }

            $invoiceItem->quantity = 1;
            $invoiceItem->total = $this->invoice->pbx_packprice;

            $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

            if ($adjValues) {
                Log::debug('      Add ServicePrices to edit body');
                $item->item_id = $item->id;
                $allItems[] = $item;
                $service->addTaxItemAdjForService($item, $adjValues, $disc);
            }
        }

        if ($this->pbxPackage->avalara_items) {
            $this->logSection('Items');
            $this->getAvalaraItems($invoiceItems, $oldValues, $service, $allItems);
        }

        //Additional Charges
        if ($this->pbxPackage->avalara_additional_charges_item) {

            $this->logSection('Additional Charges');
            $item = $this->pbxPackage->avalaraAdditionalCharges;

            if (is_null($item)) {
                throw new Exception('Avalara Additional Charge Id null or incorrect');
            }

            $invoiceItem->quantity = $this->invoice->invoiceAdditionalCharges()->sum('qty');
            $invoiceItem->total = $this->invoice->pbx_total_aditional_charges;

            $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

            if ($adjValues) {
                Log::debug('      Add AditionalCharges to edit body');
                $item->item_id = $item->id;
                $allItems[] = $item;
                $service->addTaxItemAdjForService($item, $adjValues, $disc);
            }
        }
        $this->logSection('', '|----- PbxGenerateAvalaraTaxes(Invoice) End  -----|}');

        return [$service, $allItems];
    }

    private function manual($invoiceItems, $oldItems, AvalaraTaxService $service): array
    {
        $allItems = collect();
        $this->getAvalaraItems($invoiceItems, $oldItems, $service, $allItems);

        //Log::debug('Manual Invoice');
        return [$service, $allItems];

    }

    private function getAvalaraItems($invoiceItems, $oldItems, AvalaraTaxService &$service, &$allItems): void
    {
        $requestItem = new stdClass();

        foreach ($invoiceItems as $invoiceItem) {

            $requestItem->quantity = $invoiceItem->quantity;
            $requestItem->total = ($invoiceItem->quantity * $invoiceItem->price);
            $adjValues = $this->getAdjValue($oldItems, $invoiceItem->item, $requestItem);

            if ($adjValues) {
                $invoiceItem->item->item_id = $invoiceItem->item->id;
                $allItems[] = $invoiceItem;
                $service->addTaxItemAdjForService($invoiceItem->item, $adjValues, true);
            }

        }
    }

    private function getInvoiceItems(Invoice $invoice)
    {
        return $invoice->items()->whereHas('item', function ($query) {
            $query->where('avalara_bool', '!=', null);
        })->with(
            [
                'item:id,name,description,avalara_service_type,avalara_payment_type',
                'item.avalaraServiceType:id,service_type,avalara_transaction_types'
            ]
        )->get();

    }

    private function getAdjValue($oldItems, Item $item, $invoiceItem): ?stdClass
    {
        foreach ($oldItems as $oldItem) {

            if ($oldItem->serv != $item->avalaraServiceType->service_type || $oldItem->tran != $item->avalaraServiceType->avalara_transaction_types) {
                continue;
            }

            if ($oldItem->ref != $item->description ?? $item->name) {
                continue;
            }

            if ($item->avalara_payment_type == Item::AVALARA_PAYMENT_TYPE_LINES && $invoiceItem->quantity >= $oldItem->line) {
                return null;
            }
            if ($item->avalara_payment_type == Item::AVALARA_PAYMENT_TYPE_TAXABLE && $invoiceItem->total >= $oldItem->chg) {
                return null;
            }

            //return $oldItem;

            Log::debug("      Type: {$item->avalara_payment_type}");
            Log::debug("      Old:   - Quantity: {$oldItem->line} Total: {$oldItem->chg}");
            Log::debug("      Supposed New:   - Quantity: {$invoiceItem->quantity} Total: {$invoiceItem->total}");

            $invoiceItem->quantity = $oldItem->line - $invoiceItem->quantity;
            $invoiceItem->total = $oldItem->chg - $invoiceItem->total;

            Log::debug("      Amount to sent:   - Quantity: {$invoiceItem->quantity} Total: {$invoiceItem->total}");

            return $invoiceItem;

        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function validatePackageAndService(): void
    {

        $pbxService = PbxServices::find($this->invoice->pbx_service_id);

        if ($pbxService == null) {
            throw new Exception('Pbx Service is Invalid');
        }
        $this->pbxService = $pbxService;

        $pbxPackage = PbxPackages::find($this->pbxService->pbx_package_id);

        if ($pbxPackage == null) {
            throw new Exception('PbxPackages Invalid');
        }

        $this->pbxPackage = $pbxPackage;

        if ($this->pbxPackage->avalara_options == null or ! $this->pbxPackage->avalara_options) {
            throw new Exception('Package option for avalara not active');
        }

    }

    public function logSection(string $name = '', string $text = '  Avalara'): void
    {
        Log::debug("$text $name");
    }

    /**
     * @return void
     * @throws Exception
     */
    public function validateUser(): void
    {
        $user = User::find($this->invoice->user_id);

        if ($user == null) {
            throw new Exception('User Invalid');
        }

        $this->user = $user;

        if (! $user->avalara_bool) {
            throw new Exception('User not avalara');
        }

    }

    /**
     * @throws Exception
     */
    public function customItem(&$allItems, $invoiceItem, AvalaraTaxService &$service, string $category, $oldValues, Item $item = null): void
    {

        if (is_null($item)) {
            throw new Exception('Avalara '.CustomRate::getCategoryName($category).'CDR Id null or incorrect');
        }

        $invoiceItem->total = $this->pbxService->pbxCustomCdrTotalsForInvoice($this->invoice->id, $category)->sum('exclusive_cost');
        $invoiceItem->quantity = $this->pbxService->pbxCustomCdrTotalsForInvoice($this->invoice->id, $category)->sum('exclusive_seconds') / 60;

        Log::debug('      Avalara '.CustomRate::getCategoryName($category));
        Log::debug("      New:  Quantity: {$invoiceItem->quantity}, Total: {$invoiceItem->total}");
        //Log::debug("      Old:  Total: {$oldItem->chg}, Quantity: {$oldItem->line}" );

        $adjValues = $this->getAdjValue($oldValues, $item, $invoiceItem);

        if ($adjValues) {
            Log::debug("      Add CustomItem {CustomRate::getCategoryName($category)} to edit body");
            $item->item_id = $item->id;
            $allItems[] = $item;
            $service->addTaxItemAdjForService($item, $adjValues, 0);
        }

    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function avalaraItems(array $taxesResponse, Collection $invoiceItems)
    {

        try {
            $taxItems = $taxesResponse['data']['items'];
            $totalTax = 0;
            $avalaraInvoice = AvalaraInvoice::where('invoice_id', $this->invoice->id)
                ->where('pbx_service_id', '=', $this->pbxService ? $this->pbxService->id : null)->first();
            if (! $avalaraInvoice) {
                throw new Exception('There should be a avalara Invoice');
            }
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());

            throw $th;
        }

        Log::debug('Avalara Items first half');
        foreach ($taxItems as $key => $taxItem) {
            foreach ($taxItem['txs'] as $taxes) {

                $avalaraType = AvalaraTaxType::where('tid', $taxes['tid'])->first();
                $avalaraCategory = AvalaraTaxCagetory::where('cid', $taxes['cid'])->first();

                if (is_null($avalaraType)) {
                    $avalaraType = AvalaraTaxType::create([
                        'name' => $taxes['name'],
                        'tid' => $taxes['tid'],
                        'company_id' => $this->pbxService ? $this->pbxService->company_id : null,
                        'creator_id' => null,
                    ]);
                }

                if (is_null($avalaraCategory)) {
                    $avalaraCategory = AvalaraTaxCagetory::create([
                        'name' => $taxes['cat'],
                        'cid' => $taxes['cid'],
                        'company_id' => $this->pbxService ? $this->pbxService->company_id : null,
                        'creator_id' => null,
                    ]);
                }

                $tax = new AvalaraTax();
                $tax->fill($taxes);

                if ($invoiceItems->isEmpty()) {
                    $tax->item_id = null;
                    $tax->invoice_item_id = null;
                    $tax->company_id = $this->user->company_id;

                } else {
                    $tax->item_id = $invoiceItems[$key]->item_id ?? null;
                    $tax->invoice_item_id = $invoiceItems[$key]->invoice_item_id ?? null;
                    $tax->company_id = $invoiceItems[$key]->company_id ?? null;

                }

                $tax->status = 'A';
                $tax->amount = $taxes['tax'] * 100;
                $tax->avalara_type_id = $avalaraType->id;
                $tax->avalara_category_id = $avalaraCategory->id;
                $tax->avalara_invoice_id = $avalaraInvoice->id;

                $tax->creator_id = $this->user->id;
                $tax->is_adj = true;

                $tax->save();

                Log::debug('After tax save');


                $totalTax += $taxes['tax'];
            }
        }

        $totalTax = $totalTax * 100;
        Log::debug('Total Tax : '.$totalTax);
        $this->invoice->avalara_total_tax = $totalTax;
        if ($this->invoice->pbx_service_id) {
            $this->invoice->total = $this->invoice->total + $totalTax;
            $this->invoice->due_amount = $this->invoice->due_amount + $totalTax;
        }
        $this->invoice->save();
    }
}
