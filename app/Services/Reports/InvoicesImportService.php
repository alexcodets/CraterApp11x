<?php

namespace Crater\Services\Reports;

use Crater\DataObject\CsvInvoiceFullData;
use Crater\DataObject\CsvTaxLineData;
use Crater\DataObject\CsvToInvoiceData;
use Crater\Helpers\General;
use Crater\Models\CustomerPackage;
use Crater\Models\Invoice;
use Crater\Models\Item;
use Crater\Models\PbxServices;
use Crater\Models\Tax;
use Crater\Models\TaxType;
use Crater\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class InvoicesImportService
{
    public const MAX_INVOICES = 100;
    public const MAX_LINES = 1_000;
    public const LINE_TYPE_TAX = 0;
    public const LINE_TYPE_ITEM = 1;
    public const LINE_TYPE_INVOICE = 2;

    public const FORMAT = 'd/m/Y';

    public function handle(User $auth, ?UploadedFile $file = null, ?string $format = null): JsonResponse
    {
        //        $header = 'InvoiceNo,Customer,Email,InvoiceDate,DueDate,Terms,Location,Memo,Status,PaidStatus,Subtotal,Discounts,TotalTax,Total,AmountDue,';
        //        $header = $header . 'Item(Product/Service),ItemDescription,ItemQuantity,ItemRate,ItemDiscount,ItemAmount,Service Date,';
        //        $header = 'InvoiceNo,Customer Number,Customer,Email,InvoiceDate,DueDate,Memo,Status,PaidStatus,Subtotal,Discounts,TotalTax,Total,AmountDue,Item(Product/Service),ItemDescription,ItemQuantity,ItemRate,ItemDiscount,ItemAmount,Service Date';
        //--------------------------------- 1- Invoice data 2- Item data -----------------------------
        $header = 'InvoiceNo,CustomerNumber,Customer,InvoiceDate,DueDate,Notes,Status,PaidStatus,TaxPerItem,DiscountPerItem,Subtotal,DiscountType,DiscountVal,Discounts,TotalTax,Total,AmountDue,PbxServiceNumber,ServiceNumber,ServicePeriodFrom,ServicePeriodTo,'
            .'ItemType,ItemNumber,Item(Product/Service),ItemDescription,ItemQuantity,ItemRate,ItemDiscountType,ItemDiscountVal,ItemDiscount,ItemAmount,TaxName,TaxPercentage,TaxAmount,TaxItem';

        if (! $file->isValid()) {
            return $this->responseL('The file is not valid.');
        }

        $lineCount = 0;
        $userCount = -1;
        $errors = [];
        $currentInvoice = '';
        $currentUser = '';
        $invoicesCount = collect([]);
        $invoices = [];
        $users = collect([]);
        $handler = fopen($file->getRealPath(), 'rb');
        $dateFormat = $format ?? self::FORMAT;
        $invoiceData = new \stdClass();
        $taxNames = [];
        $pbxServiceNames = [];
        $serviceNames = [];

        if ($handler === false) {
            return response()->json(['error' => __('services.invoices.import.errors.file.generic')]);
        }

        while (($line = fgetcsv($handler)) !== false) {
            if ($lineCount === 0) {
                if (implode(',', $line) !== $header) {
                    return $this->responseL(__('services.invoices.import.errors.header'));
                }
                $lineCount++;

                continue;
            }

            if (is_null($currentInvoice)) {
                $currentInvoice = $line[0];
            }

            if ($currentInvoice !== $line[0] && is_null($line[1])) {
                $errors['user']['missing'][] = $line[0];
                $currentInvoice = $line[0];
            }

            $lineCount++;
            \Log::debug("Lilnea:  ");
            \Log::debug($line);
            if ($line[1]) {

                $invoiceData = CsvToInvoiceData::fromArray($line);
                $userCount++;
                $users[] = $line[1];
                $invoicesCount[] = $lineCount;
                //$errors

                if (Invoice::where('invoice_number', $line[0])->where('company_id', $auth->company_id)->exists()) {
                    $errors['invoice']['exist'][] = $line[0];
                }

                if (in_array($line[0], $invoices)) {
                    $errors['invoice']['duplicate'][] = $line[0];
                }

                $invoices[] = $line[0];

                $errors = CsvToInvoiceData::validateArray($line, $dateFormat, $errors);

                if (in_array($line[6], [Invoice::STATUS_DRAFT, Invoice::STATUS_SAVE_DRAFT]) && $line[7] !== Invoice::STATUS_UNPAID) {
                    $errors['status']['draft_no_unpaid'][] = $line[0];
                }

                if ($line[6] === Invoice::STATUS_COMPLETED) {
                    if ($line[7] !== Invoice::STATUS_PAID) {
                        $errors['status']['complete_no_paid'][] = $line[0];
                    }
                    if ($line[15] !== 0) {
                        $errors['status']['complete_no_zero'][] = $line[0];
                    }
                }

                if ($line[7] === Invoice::STATUS_UNPAID && $line[16] !== $line[15]) {
                    $errors['status']['unpaid_total_diff'][] = $line[0];
                }

                if ($line[7] === Invoice::STATUS_PARTIALLY_PAID && $line[16] <= $line[15] && $line[16] > 0) {
                    $errors['status']['partial_total_diff'][] = $line[0];
                }

                if ($line[15] === 0 || $line[15] === '') {
                    $errors['total']['zero'][] = $line[0];
                }

                if ($line[16] === 0 || $line[16] === '') {
                    $errors['due']['zero'][] = $line[0];
                }


            }

            if ($line[31]) {
                $taxNames[] = $line[31];
            }

            if ($line[17]) {
                $pbxServiceNames[] = $line[17];
            }

            if ($line[18]) {
                $serviceNames[] = $line[18];
            }

            if ($invoiceData->discountPerItem) {

                if (! is_numeric($line[29])) {
                    $errors['discount_line']['number'][] = $line[0];
                }

                if (is_numeric($line[29]) && $line[29] < 0) {
                    $errors['discount_line']['positive'][] = $line[0];
                }

                if (! is_numeric($line[28]) || $line[28] < 0) {
                    $errors['discount_line']['val'][] = $line[0];
                }
            }

            if (! in_array($line[21], ['Item', 'Pbxservice', 'Service'])) {
                $errors['item']['type'][] = $line[0];
            }

            if (! is_numeric($line[25])) {
                $errors['quantity']['int'][] = $line[0];
            }

            if (is_numeric($line[25]) && $line[25] < 0) {
                $errors['quantity']['negative'][] = $line[0];
            }

            if (! is_numeric($line[26])) {
                $errors['total']['int'][] = $line[0];
            }

            if (is_numeric($line[26]) && $line[26] < 0) {
                $errors['total']['negative'][] = $line[0];
            }

            if (! in_array($line[27], ['fixed', 'percentage', null, ''])) {
                $errors['item_discount']['type'][] = $line[0];
            }

            if ($line[1] && $line[0]) {
                if ($currentInvoice === $line[0] && $currentUser !== $line[1]) {
                    $errors['user']['multiple_diff'][] = $line[0];
                }

                if ($currentInvoice === $line[0] && $currentUser === $line[1]) {
                    $errors['user']['multiple_same'][] = $line[0];
                }

                $currentInvoice = $line[0];
                $currentUser = $line[1];
            }

        }
        fclose($handler);

        if ($lineCount < 2) {
            return response()->json(['error' => __('services.invoices.import.errors.file.empty')]);
        }

        if ($lineCount > self::MAX_LINES) {
            return $this->responseL(__('services.invoices.import.errors.file.max_lines', ['lines' => $lineCount, 'max_lines' => self::MAX_LINES]));
        }

        if ($userCount > self::MAX_INVOICES) {
            return $this->responseL(__('services.invoices.import.errors.invoices.max', ['invoices' => $userCount, 'max_invoices' => self::MAX_INVOICES]));
        }

        $users = $users->unique()->values();
        //$names = array_unique($users['name']);
        $taxNames = array_unique($taxNames);
        $pbxServiceNames = array_unique($pbxServiceNames);
        $serviceNames = array_unique($serviceNames);

        foreach ($users as $user) {
            $exists = User::where('customcode', $user)->where('company_id', $auth->company_id)->doesntExist();
            if ($exists) {
                $errors['users']['no_found'][] = __('services.invoices.import.errors.users.no_found', ['user' => $user]);
            }
        }

        foreach ($taxNames as $name) {
            $exists = TaxType::where('name', $name)->where('company_id', $auth->company_id)->doesntExist();
            if ($exists) {
                $errors['taxes']['no_found'][] = __('services.invoices.import.errors.tax.no_found', ['tax' => $name]);
            }
        }

        foreach ($pbxServiceNames as $name) {
            $exists = ! PbxServices::where('pbx_services_number', $name)->exists();
            if ($exists) {
                $errors['pbx_service']['no_found'][] = __('services.invoices.import.errors.pbxService', ['service' => $name]);
            }
        }

        foreach ($serviceNames as $name) {
            $exists = CustomerPackage::where('code', $name)->where('company_id', $auth->company_id)->doesntExist();
            if ($exists) {
                $errors['service']['no_found'][] = __('services.invoices.import.errors.service', ['service' => $name]);
            }
        }

        \Log::debug('Errors ; ', $errors);
        if ($errors) {
            return response()->json($this->prettyLongResponse($errors));
        }

        $this->generate($file, $auth);

        return response()->json(['success']);

    }

    public function generate(?UploadedFile $file, User $auth)
    {
        $invoice = null;
        $user = null;
        $invoiceData = null;
        $firstRow = true;
        $handler = fopen($file->getRealPath(), 'rb');
        $lineInvoice = null;
        $lineData = null;
        $taxData = null;
        $currentType = null;
        while (($line = fgetcsv($handler)) !== false) {
            if ($firstRow) {
                $firstRow = false;

                continue;
            }
            if ($line[21] == '') {
                $taxData = CsvTaxLineData::fromArray($line);
                $currentType = self::LINE_TYPE_TAX;
            } else {
                $lineData = CsvInvoiceFullData::fromArray($line);
                $currentType = self::LINE_TYPE_ITEM;
            }
            $data = $line[21] == '' ? CsvInvoiceFullData::fromArray($line) : CsvTaxLineData::fromArray($line);

            if ($data->customerCode) {
                [$invoiceData, $user, $invoice] = $this->processInvoice($data, $line, $auth);
            }
            if ($data->type === CsvInvoiceFullData::LINE) {
                $this->generateItem($lineData, $invoice, $invoiceData, $user, $auth);
            }

            switch ($data->type) {
                case CsvInvoiceFullData::LINE:
                    break;
                case CsvInvoiceFullData::TAX:
                    //code
                    break;
                case CsvInvoiceFullData::DISCOUNT:
                    //code
                    break;

            }

            if (! $invoiceData->taxPerItem && $data->taxAmount) {

                $item_id2 = null;
                $obj2 = Item::where('name', $data->taxItem)->first();
                if ($obj2 != null) {
                    $item_id2 = $obj2->id;
                }

                Tax::create([
                    'tax_type_id' => 1,
                    'invoice_id' => $invoiceData->id,
                    'company_id' => $user->company_id,
                    'amount' => $data->taxAmount * 100 ,
                    'percent' => $data->taxPercentage,
                    'name' => $data->taxName,
                    'item_id' => $data->taxItem ? $item_id2 : null,
                ]);
            }

        }
        fclose($handler);
    }

    public function processInvoiceTax(CsvTaxLineData $taxData, CsvInvoiceFullData $item, CsvInvoiceFullData $invoice)
    {
        throw new \Exception('Method not implemented');

    }

    public function responseL(string $text): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $text,
            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
        ], 422);
    }

    public function prettyLongResponse(array $errors): array
    {
        $response = [];
        if ($errors['invoice']['invalid'] ?? null) {
            $response['invoice_invalid']['title'] = __('services.invoices.import.errors.title.invoices.invalid');
            $response['invoice_invalid']['items'] = array_unique($errors['invoice']['invalid'] ?? []);
        }
        if ($errors['date']['due_date'] ?? null) {
            $response['date_due']['title'] = __('services.invoices.import.errors.title.date.due_date');
            $response['date_due']['items'] = array_unique($errors['date']['due_date'] ?? []);
        }

        if ($errors['date']['service_start'] ?? null) {
            $response['date_service']['title'] = __('services.invoices.import.errors.title.date.service_start');
            $response['date_service']['items'] = array_unique($errors['date']['service_start'] ?? []);
        }

        if ($errors['date']['service_end'] ?? null) {
            $response['date_service']['title'] = __('services.invoices.import.errors.title.date.service_end');
            $response['date_service']['items'] = array_unique($errors['date']['service_end'] ?? []);
        }

        if ($errors['date']['invoice'] ?? null) {
            $response['date_invoice']['title'] = __('services.invoices.import.errors.title.date.invoice');
            $response['date_invoice']['items'] = array_unique($errors['date']['invoice'] ?? []);
        }

        //        if ($errors['status'] ?? null) {
        //            $response['status']['title'] = __('services.invoices.import.errors.title.status.no_valid');
        //            $response['status']['items'] = array_unique($errors['status'] ?? []);
        //        }

        if ($errors['status']['draft_no_paid'] ?? null) {
            $response['status_draft_no_paid']['title'] = __('services.invoices.import.errors.title.status.draft_no_paid');
            $response['status_draft_no_paid']['items'] = array_unique($errors['status']['draft_no_paid'] ?? []);
        }

        if ($errors['status']['complete_no_paid'] ?? null) {
            $response['status_complete_no_paid']['title'] = __('services.invoices.import.errors.title.status.complete_no_paid');
            $response['status_complete_no_paid']['items'] = array_unique($errors['status']['complete_no_paid'] ?? []);
        }

        if ($errors['status']['complete_no_zero'] ?? null) {
            $response['status_complete_no_zero']['title'] = __('services.invoices.import.errors.title.status.complete_no_zero');
            $response['status_complete_no_zero']['items'] = array_unique($errors['status']['complete_no_zero'] ?? []);
        }

        if ($errors['status']['unpaid_total_diff'] ?? null) {
            $response['status_unpaid_total_diff']['title'] = __('services.invoices.import.errors.title.status.unpaid_total_diff');
            $response['status_unpaid_total_diff']['items'] = array_unique($errors['status']['unpaid_total_diff'] ?? []);
        }

        if ($errors['status']['partial_total_diff'] ?? null) {
            $response['status_partial_total_diff']['title'] = __('services.invoices.import.errors.title.status.partial_total_diff');
            $response['status_partial_total_diff']['items'] = array_unique($errors['status']['partial_total_diff'] ?? []);
        }

        if ($errors['paid_status'] ?? null) {
            $response['paid_status']['title'] = __('services.invoices.import.errors.title.status_payment');
            $response['paid_status']['items'] = array_unique($errors['paid_status'] ?? []);
        }

        if ($errors['per']['tax'] ?? null) {
            $response['per_tax']['title'] = __('services.invoices.import.errors.title.tax.per');
            $response['per_tax']['items'] = array_unique($errors['per']['tax']);
        }

        if ($errors['per']['discount'] ?? null) {
            $response['per_discount']['title'] = __('services.invoices.import.errors.title.discount.per');
            $response['per_discount']['items'] = array_unique($errors['per']['discount']);
        }

        if ($errors['users']['no_found'] ?? null) {
            $response['users_no_found']['title'] = __('services.invoices.import.errors.title.users.no_found');
            $response['users_no_found']['items'] = array_unique($errors['users']['no_found'] ?? []);
        }

        if ($errors['taxes']['no_found'] ?? null) {
            $response['taxes_no_found']['title'] = __('services.invoices.import.errors.title.tax.no_found');
            $response['taxes_no_found']['items'] = array_unique($errors['taxes']['no_found'] ?? []);
        }

        if ($errors['pbx_service']['no_found'] ?? null) {
            $response['taxes_no_found']['title'] = __('services.invoices.import.errors.title.pbxService');
            $response['taxes_no_found']['items'] = array_unique($errors['pbx_service']['no_found'] ?? []);
        }

        if ($errors['service']['no_found'] ?? null) {
            $response['taxes_no_found']['title'] = __('services.invoices.import.errors.title.service');
            $response['taxes_no_found']['items'] = array_unique($errors['service']['no_found'] ?? []);
        }

        if ($errors['discount']['type'] ?? null) {
            $response['discount_type']['title'] = __('services.invoices.import.errors.title.discount.type');
            $response['discount_type']['items'] = array_unique($errors['discount']['type'] ?? []);
        }

        if ($errors['tax']['type'] ?? null) {
            $response['tax_type']['title'] = __('services.invoices.import.errors.title.tax.type');
            $response['tax_type']['items'] = array_unique($errors['tax']['type'] ?? []);
        }

        if ($errors['user']['missing'] ?? null) {
            $response['users_missing']['title'] = __('services.invoices.import.errors.title.users.missing');
            $response['users_missing']['items'] = array_unique($errors['user']['missing'] ?? []);
        }

        if ($errors['users']['multiple_diff'] ?? null) {
            $response['users_multiple_diff']['title'] = __('services.invoices.import.errors.title.users.multiple_diff');
            $response['users_multiple_diff']['items'] = array_unique($errors['users']['multiple_diff'] ?? []);
        }

        if ($errors['users']['multiple_same'] ?? null) {
            $response['users_multiple_same']['title'] = __('services.invoices.import.errors.title.users.multiple_same');
            $response['users_multiple_same']['items'] = array_unique($errors['users']['multiple_same'] ?? []);
        }

        if ($errors['invoice']['exist'] ?? null) {
            $response['invoice_exist']['title'] = __('services.invoices.import.errors.title.invoices.exist');
            $response['invoice_exist']['items'] = array_unique($errors['invoice']['exist'] ?? []);
        }

        if ($errors['invoice']['duplicate'] ?? null) {
            $response['invoice_duplicate']['title'] = __('services.invoices.import.errors.title.invoices.duplicate');
            $response['invoice_duplicate']['items'] = array_unique($errors['invoice']['duplicate'] ?? []);
        }

        if ($errors['total']['minor_than_due'] ?? null) {
            $response['due_biggest']['title'] = __('services.invoices.import.errors.title.due_amount.biggest');
            $response['due_biggest']['items'] = array_unique($errors['total']['minor_than_due'] ?? []);
        }

        if ($errors['discount_line']['positive'] ?? null) {
            $response['discount_line_positive']['title'] = __('services.invoices.import.errors.title.discount.line.positive');
            $response['discount_line_positive']['items'] = array_unique($errors['discount_line']['positive'] ?? []);
        }
        if ($errors['discount_line']['number'] ?? null) {
            $response['discount_line_int']['title'] = __('services.invoices.import.errors.title.discount.line.numeric');
            $response['discount_line_int']['items'] = array_unique($errors['discount_line']['number'] ?? []);
        }
        if ($errors['discount_line']['val'] ?? null) {
            $response['discount_line_val']['title'] = __('services.invoices.import.errors.title.discount.line.val');
            $response['discount_line_val']['items'] = array_unique($errors['discount_line']['val'] ?? []);
        }

        if ($errors['discounts_invoice']['number'] ?? null) {
            $response['discounts_invoice_int']['title'] = __('services.invoices.import.errors.title.discount.invoice.numeric');
            $response['discounts_invoice_int']['items'] = array_unique($errors['discounts_invoice']['number'] ?? []);
        }

        if ($errors['discounts_invoice']['positive'] ?? null) {
            $response['discounts_invoice_positive']['title'] = __('services.invoices.import.errors.title.discount.invoice.positive');
            $response['discounts_invoice_positive']['items'] = array_unique($errors['discounts_invoice']['positive'] ?? []);
        }

        if ($errors['discounts_invoice']['val'] ?? null) {
            $response['discounts_invoice_positive']['title'] = __('services.invoices.import.errors.title.discount.invoice.val');
            $response['discounts_invoice_positive']['items'] = array_unique($errors['discounts_invoice']['val'] ?? []);
        }

        if ($errors['discounts_invoice']['total'] ?? null) {
            $response['discounts_invoice_total']['title'] = __('services.invoices.import.errors.title.discount.invoice.total');
            $response['discounts_invoice_total']['items'] = array_unique($errors['discounts_invoice']['total'] ?? []);
        }

        if ($errors['item']['type'] ?? null) {
            $response['item_type']['title'] = __('services.invoices.import.errors.title.item.type');
            $response['item_type']['items'] = array_unique($errors['item']['type'] ?? []);
        }

        if ($errors['item_discount']['type'] ?? null) {
            $response['item_discount_type']['title'] = __('services.invoices.import.errors.title.discount.type');
            $response['item_discount_type']['items'] = array_unique($errors['item_discount']['type'] ?? []);
        }

        if ($errors['total']['zero'] ?? null) {
            $response['total_zero']['title'] = __('services.invoices.import.errors.title.total.positive');
            $response['total_zero']['items'] = array_unique($errors['total']['zero'] ?? []);
        }

        if ($errors['total']['negative'] ?? null) {
            $response['total_negative']['title'] = __('services.invoices.import.errors.title.total.positive');
            $response['total_negative']['items'] = array_unique($errors['total']['negative'] ?? []);
        }

        if ($errors['total']['int'] ?? null) {
            $response['total_int']['title'] = __('services.invoices.import.errors.title.total.int');
            $response['total_int']['items'] = array_unique($errors['total']['int'] ?? []);
        }

        if ($errors['quantity']['int'] ?? null) {
            $response['quantity_no_int']['title'] = __('services.invoices.import.errors.title.quantity.no_int');
            $response['quantity_no_int']['items'] = array_unique($errors['quantity']['int'] ?? []);
        }

        if ($errors['due']['zero'] ?? null) {
            $response['due_zero']['title'] = __('services.invoices.import.errors.title.due_amount.positive');
            $response['due_zero']['items'] = array_unique($errors['due']['zero'] ?? []);
        }

        if ($response) {
            $response = [
                'success' => false,
                'message' => 'Validation errors',
                'data' => $response,
                'code' => 4022,
            ];
        }

        return $response;

    }

    public function generateItem($data, Invoice $invoice, ?CsvToInvoiceData $invoiceData, $user, User $auth): void
    {
        switch ($data->itemType) {
            case 'Item':

                $item = $invoice->items()->create([
                    'item_id' => Item::where('item_number', $data->itemNumber)->first('id')->id,
                    'name' => $data->itemName,
                    'description' => $data->itemDescription,
                    'quantity' => $data->itemQuantity,
                    'price' => $data->itemRate * 100,
                    'total' => $data->itemAmount * 100,
                    'discount' => $data->discountPerItem ? $data->itemDiscount : 0,
                    'discount_type' => $data->discountPerItem ? $data->itemDiscountType : 'fixed',
                    'discount_val' => $data->discountPerItem ? ($data->itemDiscountVal * 100) : 0,
                ]);

                if ($invoiceData->taxPerItem && $data->taxAmount) {
                    $item->taxes()->create([
                        'tax_type_id' => 1,
                        //'invoice_id'  => $invoiceData->id,
                        'company_id' => $user->company_id,
                        'amount' => $data->taxAmount,
                        'percent' => $data->taxPercentage,
                        'name' => $data->taxName,
                        'item_id' => $data->taxItem ? Item::where('name', $data->taxItem)->first()->id(['id']) : null,
                    ]);
                }

                break;
            case 'PbxService':

                switch ($data->itemName) {
                    case 'PBXSEXTENSION':
                        $invoice->extensionDetails()->create([
                            'name' => $data->itemDescription,
                            'quantity' => $data->itemQuantity,
                            'price' => $data->itemRate,
                            'total' => $data->itemAmount,
                        ]);

                        break;
                    case 'PBXSDID':
                        $invoice->didDetails()->create([
                            'name' => $data->itemDescription,
                            'quantity' => $data->itemQuantity,
                            'price' => $data->itemRate,
                            'total' => $data->itemAmount,
                        ]);

                        break;
                    case 'PBXSACHARGE':
                        $invoice->additionalCharges()->create([
                            'additional_charge_amount' => $data->itemAmount,
                            'template_name' => 'Custom',
                            'additional_charge_name' => $data->itemDescription ?? 'Custom Charge',
                            'additional_charge_type' => 'Service',
                            'qty' => $data->itemQuantity,
                            'price' => $data->itemRate,
                            'total' => $data->itemAmount,
                            'company_id' => $user->company_id,
                            'creator_id' => $auth->id,
                        ]);

                        break;
                    case 'PBXSARATE':
                        $invoice->pbxAppRates()->create([
                            'app_name' => $data->itemDescription,
                            'quantity' => $data->itemQuantity,
                            'costo' => $data->itemRate,
                        ]);

                        break;
                    default:
                        // default code
                        break;
                }

                break;
            case 'Service':
                break;
            default:
                // default code
                break;
        }
    }

    public function processInvoice($data, $line, User $auth): array
    {
        $invoiceData = CsvToInvoiceData::fromArray($line);
        $user = User::where('customcode', $line[1])->first();
        $pbxService = $data->pbxServiceNumber ? PbxServices::where('pbx_services_number', $data->pbxServiceNumber)->first('id')->id : null;
        $service = $data->serviceNumber ? CustomerPackage::where('code', $data->serviceNumber)->where('company_id', $auth->company_id)->first('id')->id : null;

        $invoice = Invoice::create([
            'invoice_number' => $data->invoiceNumber,
            'invoice_date' => $data->invoiceDate,
            'due_date' => $data->dueDate,
            'notes' => $data->notes,
            'status' => $data->status,
            'paid_status' => $data->paidStatus,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'tax_per_item' => $data->getTaxPerItemString(),
            'discount_per_item' => $data->getDiscountPerItemString(),
            'sub_total' => $data->subTotal,
            'discount_type' => $data->discountType,
            'discount_val' => $data->discountVal,
            'discount' => $data->discounts,
            'tax' => $data->totalTax,
            'total' => $data->total * 100,
            'due_amount' => $data->amountDue * 100,
            'pbx_service_id' => $pbxService,
            'unique_hash' => General::generateUniqueId(),
            'pbxservice_date_prev' => $data->servicePeriodFrom,
            'pbxservice_date_renewal' => $data->servicePeriodTo,
            //                    'item_type' => $line[21],
            'service_end' => $data->isAService() ? $data->servicePeriodTo : null,
            'service_start' => $data->isAService() ? $data->servicePeriodFrom : null,
            'customer_packages_id' => $service,
        ]);

        $invoiceData->setId($invoice->id);

        return [$invoiceData, $user, $invoice];
    }
}
