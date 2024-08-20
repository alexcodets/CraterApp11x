<?php

namespace Crater\Avalara\Service;

use Crater\Avalara\DataObject\AvalaraCompanyModelDO;
use Crater\Avalara\DataObject\AvalaraExemptionDO;
use Crater\Avalara\DataObject\AvalaraInvoiceDO;
use Crater\Avalara\DataObject\AvalaraLocationBillingDo;
use Crater\Avalara\DataObject\AvalaraUserBillingDO;
use Crater\Models\AvalaraConfig;
use Crater\Models\AvalaraExemption;
use Crater\Models\AvalaraServiceType;
use Crater\Models\Invoice;
use Crater\Models\Item;
use Crater\Models\User;
use Exception;
use Throwable;
use TypeError;

class AvalaraTaxService
{
    public User $user;

    public AvalaraService $service;

    public function __construct(User $user, AvalaraService $service)
    {
        $this->service = $service;
        $this->user = $user;
    }

    /**
     * Validate and Prepare the required Billing and company data.
     *
     * @return array
     */
    public function validateUserData(AvalaraConfig $config, Invoice $invoice = null, bool $commit = false): array
    {
        $this->user->load('shippingAddress');
        $date = now()->format('Y-m-d H:i:s');

        try {
            $billing = $this->getInvoiceLocation();
        } catch (TypeError $th) {
            return $this->error('The billing Address data of the user is incomplete');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }

        try {
            $company = new AvalaraCompanyModelDO($config);
        } catch (TypeError $th) {
            return $this->error(__('avalara.error.company_model.required.base'));
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }

        $validar = $company->checkItems($config);
        if (! $validar['success']) {
            return $this->error($validar['msg']);
        }

        try {
            $invoiceDo = new AvalaraInvoiceDO($date, $commit, $config, $this->user, $invoice ?? null);
            $this->service->prepareForTax($invoiceDo, $billing, $company, $config);
        } catch (Throwable $th) {
            return $this->error($th->getMessage(), $th->getCode());
        }

        return $this->success();
    }

    /**
     * @throws Exception
     */
    private function getInvoiceLocation()
    {
        $location = $this->user->location;
        if ($location) {
            return new AvalaraLocationBillingDo($location);
        }
        $location = $this->user->company->location;
        if ($location) {
            return new AvalaraLocationBillingDo($location);
        }

        return new AvalaraUserBillingDO($this->user->billingAddress);
    }

    public function validateIsAvalaraInvoice($invoice): array
    {
        if ($invoice->avalara_bool == null) {
            return $this->error(__('avalara.error.not_avalara_item.message'), __('avalara.error.not_avalara_item.code'));
        }

        return $this->success();
    }

    public function validateItemData(Item $item): array
    {

        try {

            if ($item->avalara_bool == null) {
                throw new Exception(__('avalara.tax_services.error.avalara_bool.null'), config('avalara.errors.not_avalara_item.code'));
            }

            if ($item->avalara_payment_type == null) {
                throw new Exception(__('avalara.tax_services.error.payment_type.null'), config('avalara.errors.not_avalara_item.code'));
            }

            if (! in_array($item->avalara_payment_type, Item::AVALARA_PAYMENT_TYPES)) {
                throw new Exception(__('avalara.tax_services.error.payment_type.invalid'), config('avalara.errors.not_avalara_item.code'));
            }

            if ($item->avalara_service_type === null) {
                throw new Exception(__('avalara.tax_services.error.service_type.null', ['item' => $item->id]), config('avalara.errors.invalid_ts_pair_data.code'));
            }

            if (AvalaraServiceType::where('id', '=', $item->avalara_service_type)->doesntExist()) {
                throw new Exception(__('avalara.tax_services.error.service_type.invalid', ['id' => $item->avalara_service_type, 'item' => $item->id]), 404);
            }

            if ($item->avalara_type == null || $item->avalara_type == 0) {
                throw new Exception(__('avalara.tax_services.error.avalara_type.null', ['item' => $item->id]), config('avalara.errors.invalid_ts_pair_data.code'));
            }

        } catch (Throwable $th) {
            return $this->error($th->getMessage(), $th->getCode());

        }

        return $this->success();
    }

    public function addTaxItem(Item $item, $request): array
    {
        $response = $this->validateItemData($item);
        if (! $response['success']) {
            return $response;
        }

        switch ($item->avalara_payment_type) {
            case 'LINES':
                $this->service->addLine(
                    $request->quantity,
                    $item->avalaraServiceType->avalara_transaction_types,
                    $item->avalaraServiceType->service_type,
                    1,
                    $item->description ?? $item->name
                );

                break;
            case 'TAXABLE_AMOUNT':
                $this->service->addCharge(
                    $request->quantity * $request->price / 100,
                    $item->avalaraServiceType->avalara_transaction_types,
                    $item->avalaraServiceType->service_type,
                    1,
                    $item->description ?? $item->name
                );

                break;
            case 'NOTHING':
                break;
            default:
                return $this->error('The type of payment is not valid');
        }

        return $this->success();
    }

    public function addTaxItemForService(Item $item, $request, $adj = false): array
    {
        \Log::debug("addTaxItemForService");
        \Log::debug("Item->avalara_payment_type");
        \Log::debug($item->avalara_payment_type);
        \Log::debug("Request->total");
        \Log::debug($request->total);

        $response = $this->validateItemData($item);
        if (! $response['success']) {
            return $response;
        }

        switch ($item->avalara_payment_type) {
            case 'LINES':
                $this->service->addLine(
                    $request->quantity,
                    $item->avalaraServiceType->avalara_transaction_types,
                    $item->avalaraServiceType->service_type,
                    1,
                    $item->description ?? $item->name,
                    $adj
                );

                break;
            case 'TAXABLE_AMOUNT':
                $this->service->addCharge(
                    $request->total / 100,
                    $item->avalaraServiceType->avalara_transaction_types,
                    $item->avalaraServiceType->service_type,
                    1,
                    $item->description ?? $item->name,
                    0,
                    $adj
                );

                break;
            case 'NOTHING':
                // //Log::debug('The payment Type can not be different to LINES or TAXABLE_AMOUNT');
                break;
            default:
                return $this->error('The type of payment is not valid');
        }

        return $this->success();
    }

    public function addTaxItemAdjForService(Item $item, $request, $disc = 0): array
    {
        $response = $this->validateItemData($item);
        if (! $response['success']) {
            return $response;
        }

        switch ($item->avalara_payment_type) {
            case 'LINES':
                $this->service->addLineAdj(
                    $request->quantity,
                    $item->avalaraServiceType->avalara_transaction_types,
                    $item->avalaraServiceType->service_type,
                    1,
                    $item->description ?? $item->name,
                    $disc
                );

                break;
            case 'TAXABLE_AMOUNT':
                $this->service->addChargeAdj(
                    $request->total / 100,
                    $item->avalaraServiceType->avalara_transaction_types,
                    $item->avalaraServiceType->service_type,
                    1,
                    $item->description ?? $item->name,
                    0,
                    $disc
                );

                break;
            case 'NOTHING':
                // //Log::debug('The payment Type can not be different to LINES or TAXABLE_AMOUNT');
                break;
            default:
                return $this->error('The type of payment is not valid');
        }

        return $this->success();
    }

    public function addBundleItem(object $bundle, $adj = false)
    {
        $this->service->addBundle($bundle->price, $bundle->transaction, $bundle->service, $bundle->name, $adj);
    }

    private function success(array $data = []): array
    {
        return [
            'success' => true,
            'status' => 200,
            'data' => $data,
        ];
    }

    public function error(string $message, int $status = 500, array $errors = []): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status' => $status,
        ];
    }

    public function getTaxes(): array
    {
        return $this->service->getTaxes();
    }

    public function addExemption(AvalaraExemption $item)
    {
        $this->service->addExemption((new AvalaraExemptionDO())->toArray($item));
    }
}
