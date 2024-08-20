<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Service\AvalaraValidationService;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\AvalaraItemsTaxRequest;
use Crater\Models\Item;
use Crater\Models\User;
use Exception;
use Throwable;

class AvalaraItemsTaxController extends Controller
{
    public function show(AvalaraItemsTaxRequest $request, User $user)
    {
        $items = collect($request->items)->map(function ($item, $key) {
            $item['item'] = Item::find($item['id']);

            return $item;
        })->filter(function ($item, $key) {
            return $item['item'] && $item['item']->avalara_bool == 1;
        });

        try {
            $service = AvalaraValidationService::validateAndGetAvalaraTaxService($user);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'errors' => [],
                'status' => $th->getCode(),
            ]);
        }


        foreach ($items as $item) {
            $item = (object)$item;

            try {

                AvalaraValidationService::itemValidation($item->item);
                $service->addTaxItemForService($item->item, $item);
                ////Log::debug($service->service->request->body);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => [],
                    'status' => $e->getCode(),
                ]);
            }
        }

        return response()->json($service->getTaxes());

    }
}
