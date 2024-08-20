<?php

namespace Crater\Http\Controllers\V2\CorePbx\Service;

use Crater\Actions\ExtensionStore;
use Crater\DataObject\ExtensionStoreData;
use Crater\Http\Requests\PbxExtensionStoreRequest;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesExtensions;
use Crater\Traits\PbxServicesReCalculateTrait;
use Illuminate\Support\Facades\Log;

class PbxExtensionController
{
    public function store(PbxExtensionStoreRequest $request, PbxServices $pbxService, ExtensionStore $action)
    {

        try {
            $data = new ExtensionStoreData($request->validated(), $pbxService->tenant->pbxServer, $pbxService->tenant);
            $response = $action->handle($data);

            if (! $response['success']) {
                return $response;
            }
            Log::debug('Error: ', $response);

            /* @var PbxExtensions $extension */
            $extension = $response['data']['extension'];

            PbxServicesExtensions::create([
                'pbx_service_id' => $pbxService->id,
                'pbx_extension_id' => $extension->id,
                'company_id' => $data->companyId,
                'creator_id' => $data->creatorId,
                'price' => $pbxService->pbxPackage->profileExtension->rate,
            ]);

            PbxServicesReCalculateTrait::calculatePriceService($pbxService, false);

        } catch (\Throwable $th) {
            \Log::error($th->getTraceAsString());

            return response()->json(['error' => $th->getMessage(), 'code' => $th->getCode()], 500);
        }

        return response()->json(['success' => true], 201);

    }
}
