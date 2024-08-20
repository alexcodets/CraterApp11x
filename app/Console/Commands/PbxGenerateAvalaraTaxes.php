<?php

namespace Crater\Console\Commands;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
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
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use stdClass;
use Throwable;

class PbxGenerateAvalaraTaxes extends Command
{
    public Invoice $invoice;

    public ?PbxServices $pbxService = null;

    public ?PbxPackages $pbxPackage = null;

    public User $user;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:generateAvalaraTax
    {invoice_id : The id of the Invoice.}
    {--is_manual_invoice : Whether the Invoice is manual or not.}
    {commit=true : Whether it will be committed or not}
    {adj=false : if is a taxes calc or a adj}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate the avalara taxes associates to a invoice';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        Log::debug("PbxGenerateAvalaraTaxes");
        //TODO: Check if there exist a Avalara Tax so it run or not.

        $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process Start for Invoice: {$this->argument('invoice_id')} ----|>");

        try {
            $this->validateInput();
            //Log::debug($this->invoice->id);
            //!$this->option('is_manual_invoice') ||
            if ($this->invoice->pbx_service_id != null) {
                $this->validatePackageAndService();
            } else {
                if (is_null($this->invoice->inv_avalara_bool)) {
                    throw new Exception('The invoice is not an Avalara Invoice');
                }
            }
            $this->validateUser();
        } catch (Throwable $th) {
            Log::debug('It wont generate Avalara taxes');
            Log::debug($th->getMessage());
            $this->info($th->getMessage());

            return self::FAILURE;
        }

        $this->process();

        //Log::debug("PbxGenerateAvalaraTaxes - End");
        return self::SUCCESS;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function validateInput(): void
    {
        if ($this->argument('invoice_id') == null) {
            throw new Exception('Invoice Id is required');
        }

        if (! $this->isValidNumber($this->argument('invoice_id'))) {
            throw new Exception('Invoice Id is not a valid format');
        }

        $invoice = Invoice::find($this->argument('invoice_id'));

        if ($invoice == null) {
            throw new Exception('Invoice Id is Invalid');
        }

        if (is_null($invoice->company)) {
            throw new Exception('There is not a valide company');
        }

        if (is_null($invoice->company->avalaraConfiguration)) {
            throw new Exception('There is not active config');
        }

        $this->invoice = $invoice;
    }

    public function isValidNumber($value): bool
    {
        if (is_numeric($value) && is_int((int)$value)) {
            return true;
        }

        return false;
    }

    /**
     * @return void
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

    public function process(): bool
    {
        //Se comprueba que exista el usuario, que los datos de address sean correctos
        $chronos = new Chronometer();
        $service = new AvalaraTaxService($this->user, new AvalaraService(new AvalaraApi($this->invoice->company->avalaraConfiguration)));
        //TODO: Commit.
        $commit = $this->argument('commit');
        $adj = $this->argument('adj');
        $validation = $service->validateUserData($this->invoice->company->avalaraConfiguration, $this->invoice, $commit);

        if ($validation['success'] === false) {
            $this->info('User data is not valid');
            $this->error($validation['message']);
            Log::debug($validation['message']);

            return self::FAILURE;
        }

        //$invoices->items
        $invoiceItems = $this->invoice->items()->whereHas('item', function ($query) {
            $query->where('avalara_bool', '!=', null);
        })->with('item')->get();

        //Pasada todas las validaciones, se procede a generar impuestos Avalara para el documento
        //Armando y calculando los impuestos
        $invoiceItem = new stdClass();
        //$allItems    = collect();

        $avaLog = new AvalaraLog();
        $avaLog->invoice_id = $this->invoice->id;
        $avaLog->user_id = $this->user->id;
        //Log::debug($this->invoice);

        try {
            //$this->invoice->pbx_service_id == null
            if ($this->option('is_manual_invoice') || (is_null($this->invoice->pbx_service_id) && $this->invoice->inv_avalara_bool)) {
                /** @var $service AvalaraTaxService */
                /** @var $allItems Collection */
                //Log::debug('Go Manual');
                list($service, $allItems) = $this->manual($invoiceItems, $service);
                $avaLog->type = AvalaraLog::INVOICE_MANUAL;

            } else {
                /** @var $service AvalaraTaxService */
                /** @var $allItems Collection */
                list($service, $allItems) = $this->invoice($invoiceItems, $service, $invoiceItem);
                $avaLog->pbx_service_id = $this->pbxService->id;
                //Log::debug('Go Service');
                $avaLog->type = AvalaraLog::INVOICE_SERVICE;
            }
        } catch (Throwable $th) {
            //throw $th;
            Log::debug($th->getMessage());
            $this->info('Error While Building the request for Avalara');
            $this->error($th->getMessage());
            //Log::debug($th->getMessage());
            $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process End while Building the request ----|>");
            $avaLog->status = AvalaraLog::STATUS_ERROR;
            $avaLog->response = json_encode($th->getMessage());
            $avaLog->save();

            return false;
        }

        try {
            $avaLog->request = json_encode($service->service->request->body);
            $chronos->start();
            $taxesResponse = $service->getTaxes();
            $chronos->end();
            $avaLog->procesing_time = $chronos->totalExecutionMilliseconds();
        } catch (Throwable $th) {
            //throw $th;
            //Log::debug($th->getMessage());
            $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process End while calculating the tax ----|>");
            Log::debug($th->getMessage());

            return false;
        }

        //Log::debug($taxesResponse);
        //Log::debug($allItems);
        //Log::debug($taxesResponse);
        if ($taxesResponse['success'] === false) {
            $this->error($taxesResponse['message']);
            //Log::debug($taxesResponse['message']);
            //Log::debug('response', $taxesResponse);
            $this->logSection('', "<|---- PbxGenerateAvalaraTaxes Process End while validating the tax ----|>");
            $avaLog->status = AvalaraLog::STATUS_ERROR;
            $avaLog->response = json_encode($taxesResponse);
            $avaLog->save();
            Log::debug($taxesResponse['message']);

            return false;
        }

        $this->avalaraItems($taxesResponse, $allItems);
        $avaLog->response = json_encode($taxesResponse);
        $avaLog->status = AvalaraLog::STATUS_SUCCESS;
        $avaLog->save();

        return true;

    }

    public function manual($invoiceItems, AvalaraTaxService $service): array
    {
        $allItems = collect();
        $this->getAvalaraItems($invoiceItems, $service, $allItems);

        //Log::debug('Manual Invoice');
        return [$service, $allItems];

    }

    /**
     * @param $invoiceItems
     * @param AvalaraTaxService $service
     * @param $invoiceItem
     * @return array
     * @throws Exception
     */
    public function invoice($invoiceItems, AvalaraTaxService $service, $invoiceItem): array
    {
        $allItems = collect();
        /* @var $adj bool */
        $this->logSection('', '|----- PbxGenerateAvalaraTaxes(Invoice) Start -----|}');

        if ($this->pbxPackage->avalaraBundle) {
            $this->logSection('Bundle');
            $noTaxable = $this->invoice->items()
                ->whereHas('item', function ($query) {
                    $query->where('no_taxable', 1);
                })->sum('total');

            $bundle = (object)[
                'name' => $this->pbxPackage->pbx_package_name.$this->invoice->created_at->format('d/m/Y'),
                'price' => ($this->invoice->sub_total - $noTaxable) / 100,
                'transaction' => $this->pbxPackage->bundleTransaction,
                'service' => $this->pbxPackage->bundleService,
            ];
            $service->addBundleItem($bundle);

            return [$service, collect()];
        }

        if ($this->pbxPackage->avalara_extension) {
            $this->logSection('Extension');
            $item = $this->pbxPackage->avalaraExtension;

            if (is_null($item)) {
                throw new Exception('Avalara Extension Id null or incorrect');
            }

            $item->item_id = $item->id;
            $allItems[] = $item;
            $invoiceItem->quantity = $this->invoice->count_extension ?? 0;
            $invoiceItem->total = $this->invoice->pbx_total_extension ?? 0;
            $service->addTaxItemForService($item, $invoiceItem);
        }

        if ($this->pbxPackage->avalara_did) {
            $this->logSection('Did');
            $item = $this->pbxPackage->avalaraDid;

            if (is_null($item)) {
                throw new Exception('Avalara DID Id null or incorrect');
            }

            $item->item_id = $item->id;
            $allItems[] = $item;

            $invoiceItem->quantity = $this->invoice->count_did;
            $invoiceItem->total = $this->invoice->pbx_total_did ?? 0;
            $service->addTaxItemForService($item, $invoiceItem);
        }

        if ($this->pbxPackage->avalara_callrating) {
            $this->logSection('CallRating');
            $item = $this->pbxPackage->avalaraCdr;

            if (is_null($item)) {
                throw new Exception('Avalara CDR Id null or incorrect');
            }

            $item->item_id = $item->id;
            $allItems[] = $item;
            $invoiceItem->total = $this->pbxService->pbxCdrTotalsForInvoice($this->invoice->id)->sum('exclusive_cost');
            $invoiceItem->quantity = $this->pbxService->pbxCdrTotalsForInvoice($this->invoice->id)->sum('exclusive_seconds') / 60;
            $service->addTaxItemForService($item, $invoiceItem);
            $this->logSection('', '|-----Custom Rate Section Start-----|');

            //WIP: Custom Rate;

            //International
            if ($this->pbxPackage->inter_custom_destinations_item_id) {
                $this->logSection('International');

                $item = $this->pbxPackage->avalaraInternational;
                $this->customItem($allItems, $invoiceItem, $service, CustomRate::CATEGORY_INTERNATIONAL, $item);
            }

            //custom rate
            if ($this->pbxPackage->custom_destinations_item_id) {
                $this->logSection('Custom Destination');

                $item = $this->pbxPackage->avalaraCustomDestination;
                $this->customItem($allItems, $invoiceItem, $service, CustomRate::CATEGORY_CUSTOM, $item);
            }

            //Toll Free
            if ($this->pbxPackage->toll_free_custom_destinations_item_id) {
                $this->logSection('Tool Free Destination');

                $item = $this->pbxPackage->avalaraTollFree;
                $this->customItem($allItems, $invoiceItem, $service, CustomRate::CATEGORY_TOLL_FREE, $item);
            }

            $this->logSection('', '|-----Custom Rate Section End-----|');

        }


        if ($this->pbxPackage->avalara_custom_app_rate_item_id) {
            $this->logSection('AppRating');

            $item = $this->pbxPackage->avalaraAppRate;

            if (is_null($item)) {
                throw new Exception('Avalara Custom App Id null or incorrect');
            }

            $item->item_id = $item->id;
            $allItems[] = $item;

            /*
            Cada App es su propio item individual
            foreach ($this->pbxService->pbxServicesAppRate as $app) {
            $allItems[]            = $item;
            $invoiceItem->quantity = $app->quantity;
            $invoiceItem->price    = $app->costo;
            $service->addTaxItemForService($item, $invoiceItem);
            }
             */

            // Agrupando las app
            $quantity = 0;
            //$price = 0;
            foreach ($this->pbxService->pbxServicesAppRate as $app) {
                $quantity += $app->quantity;
                //$price += $app->costo;
            }

            $invoiceItem->total = $this->invoice->pbx_total_apprate;
            $invoiceItem->quantity = $quantity;

            $service->addTaxItemForService($item, $invoiceItem);

        }

        if ($this->pbxPackage->avalara_services_price_item) {
            $this->logSection('Pbx Service Price');
            $item = $this->pbxPackage->avalaraServicesPrice;

            if (is_null($item)) {
                throw new Exception('Avalara Custom App Id null or incorrect');
            }

            $item->item_id = $item->id;
            $allItems[] = $item;

            $invoiceItem->quantity = 1;
            $invoiceItem->total = $this->invoice->pbx_packprice;
            $service->addTaxItemForService($item, $invoiceItem);
        }

        if ($this->pbxPackage->avalara_items) {
            $this->logSection('Items');
            $this->getAvalaraItems($invoiceItems, $service, $allItems);
        }

        //Additional Charges
        if ($this->pbxPackage->avalara_additional_charges_item) {

            $this->logSection('Additional Charges');
            $item = $this->pbxPackage->avalaraAdditionalCharges;

            if (is_null($item)) {
                throw new Exception('Avalara Additional Charge Id null or incorrect');
            }

            $item->item_id = $item->id;
            $allItems[] = $item;

            $invoiceItem->quantity = $this->invoice->invoiceAdditionalCharges()->sum('qty');
            $invoiceItem->total = $this->invoice->pbx_total_aditional_charges;
            $service->addTaxItemForService($item, $invoiceItem);
        }

        $avalaraExemptions = $this->pbxService->avalaraExemptions()->with('location')->get();
        foreach ($avalaraExemptions as $ex) {
            $service->addExemption($ex);
        }
        $this->logSection('', '|----- PbxGenerateAvalaraTaxes(Invoice) End  -----|}');

        return [$service, $allItems];
    }

    public function logSection(string $name = '', string $text = '  Avalara'): void
    {
        $this->info("{$text} {$name}");
        //Log::debug("$text $name");
    }

    /**
     * @param $allItems
     * @param $invoiceItem
     * @param AvalaraTaxService $service
     * @param string $category
     * @param Item|null $item
     * @return void
     * @throws Exception
     */
    public function customItem(&$allItems, $invoiceItem, AvalaraTaxService &$service, string $category, Item $item = null): void
    {

        if (is_null($item)) {
            throw new Exception('Avalara'.CustomRate::getCategoryName($category).'CDR Id null or incorrect');
        }
        $item->item_id = $item->id;
        $allItems[] = $item;
        $invoiceItem->total = $this->pbxService->pbxCustomCdrTotalsForInvoice($this->invoice->id, $category)->sum('exclusive_cost');
        $invoiceItem->quantity = $this->pbxService->pbxCustomCdrTotalsForInvoice($this->invoice->id, $category)->sum('exclusive_seconds') / 60;
        $service->addTaxItemForService($item, $invoiceItem);

    }

    public function avalaraItems(array $taxesResponse, Collection $invoiceItems)
    {

        $taxeItems = $taxesResponse['data']['items'];
        $totalTax = 0;
        $avalaraInvoice = AvalaraInvoice::create([
            'pbx_service_id' => $this->pbxService ? $this->pbxService->id : null,
            'invoice_id' => $this->invoice->id,
            'avalara_invoice_number' => $this->invoice->avalara_invoice_number,
            'document_code' => $this->invoice->avalara_document_code ?? null,
        ]);
        foreach ($taxeItems as $key => $taxItem) {
            //Key position del item en array items

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

                $tax->save();

                $totalTax += $taxes['tax'];
            }
        }

        $totalTax = $totalTax * 100;
        $this->info('Total Tax : '.$totalTax);
        $this->invoice->avalara_total_tax = $totalTax;
        //$this->invoice->total = $this->invoice->subtotal + $this->invoice->tax;
        $this->invoice->total = $this->invoice->total + $totalTax;
        $this->invoice->due_amount = $this->invoice->due_amount + $totalTax;
        $this->invoice->save();
    }

    public function getAvalaraItems($invoiceItems, AvalaraTaxService &$service, &$allItems): void
    {
        $requestItem = new stdClass();

        foreach ($invoiceItems as $invoiceItem) {
            $requestItem->quantity = $invoiceItem->quantity;
            $requestItem->total = $invoiceItem->quantity * $invoiceItem->price;
            $service->addTaxItemForService($invoiceItem->item, $requestItem);
            $invoiceItem->invoice_item_id = $invoiceItem->id;
            $allItems[] = $invoiceItem;
        }
    }
}
