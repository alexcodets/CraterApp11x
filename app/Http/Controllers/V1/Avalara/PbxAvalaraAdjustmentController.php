<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
use Crater\Helpers\Chronometer;
use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraLog;
use Crater\Models\Invoice;
use Illuminate\Http\Request;

class PbxAvalaraAdjustmentController extends Controller
{
    public function update(Invoice $invoice, Request $request): array
    {
        //TODO: It should not be possible to edit after the month finished.
        //Abandoned.

        try {
            $chronos = new Chronometer();

            //\Log::debug('-------');
            $requestItems = $request->items;
            $invoiceItems = $invoice->items()->whereHas('item', function ($query) {
                $query->where('avalara_bool', '!=', null);
            })->with('item')->get();
            //return $request->all();

            $service = new AvalaraTaxService($invoice->user, new AvalaraService(new AvalaraApi($invoice->company->avalaraConfiguration)));

            $validation = $service->validateUserData($invoice->company->avalaraConfiguration, $invoice, true);

            if ($validation['success'] === false) {
                return [
                    'success' => false,
                    'message' => 'User data is not valid'
                ];
            }

            $avaLog = new AvalaraLog();
            $avaLog->invoice_id = $invoice->id;
            $avaLog->user_id = $invoice->user->id;
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        try {

            $values = new \stdClass();
            foreach ($requestItems as $item) {
                $invoiceItem = $invoiceItems->find($item['id']);
                if (is_null($invoiceItem)) {
                    return [
                        'success' => false,
                        'message' => 'One of the items id is not valid'
                    ];
                }

                $avalaraItem = $invoiceItem->item;
                $avalaraItem->item_id = $invoiceItem->id;

                $avalaraItem->item_id = $item->id;

                $values->quantity = $invoice->extensions()->count();
                $values->total = $invoice->pbx_total_extension ?? 0;
                $service->addTaxItemForService($invoiceItem, $values);

            }
        } catch (\Throwable $th) {
            $avaLog->status = AvalaraLog::STATUS_ERROR;
            $avaLog->response = $th->getMessage();
            $avaLog->save();

            //\Log::debug('Error While Arming the request');
            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        try {
            $avaLog->request = json_encode($service->service->request->body);
            $chronos->start();
            $taxesResponse = $service->getTaxes();
            $chronos->end();
            $avaLog->procesing_time = $chronos->totalExecutionMilliseconds();
        } catch (Throwable $th) {
            //\Log::debug('PbxGenerateAvalaraTaxes Process End while calculating the tax');
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];

        }

        if ($taxesResponse['success'] === false) {
            Log::debug('response', $taxesResponse);
            //\Log::debug('PbxGenerateAvalaraTaxes Process End while validating the tax');
            $avaLog->status = AvalaraLog::STATUS_ERROR;
            $avaLog->response = json_encode($taxesResponse);
            $avaLog->save();

            return [
                'success' => false,
                'message' => 'PbxGenerateAvalaraTaxes Process End while validating the tax'
            ];
        }

        //Updating the items.
        foreach ($requestItems as $item) {
            $invoiceItem = $invoiceItems->find($item['id']);
            $invoiceItem->quantity = $item->quantity;
            $invoiceItem->price = $item->price;
            $invoiceItem->total = $item->total;

            $avalaraItem = $invoiceItem->item;
            $avalaraItem->item_id = $invoiceItem->id;

            $avalaraItem->item_id = $item->id;

            $values->quantity = $invoice->extensions()->count();
            $values->total = $invoice->pbx_total_extension ?? 0;
            $service->addTaxItemForService($invoiceItem, $values);

        }

        //$this->avalaraItems($taxesResponse, $allItems);
        $avaLog->response = json_encode($taxesResponse);
        $avaLog->status = AvalaraLog::STATUS_SUCCESS;
        $avaLog->operation_type = AvalaraLog::OPERATION_EDIT;
        $avaLog->save();

        return true;


    }
}
