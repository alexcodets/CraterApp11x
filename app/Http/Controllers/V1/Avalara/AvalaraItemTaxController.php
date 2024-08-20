<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Service\AvalaraValidationService;
use Crater\Http\Controllers\Controller;
use Crater\Models\Item;
use Crater\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AvalaraItemTaxController extends Controller
{
    public function show(Request $request, User $user, Item $item): JsonResponse
    {
        $request->request->add(['total' => $request->price]);

        try {
            $service = AvalaraValidationService::validateAndGetAvalaraTaxService($user);
            $service->addTaxItemForService($item, $request);

        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'errors' => [],
                'status' => $th->getCode(),
            ]);
        }

        ////Log::debug($service->service->request->body);
        return response()->json($service->getTaxes());
    }
}
