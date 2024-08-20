<?php

namespace Crater\Http\Controllers\V2\CorePbx\Service;

use Crater\Actions\DidStore;
use Crater\DataObject\DidStoreData;
use Crater\Http\Requests\PbxDIdStoreRequest;
use Crater\Models\PbxDID;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesDID;
use Crater\Traits\PbxServicesReCalculateTrait;
use Illuminate\Support\Facades\Log;

class PbxDidController
{
    public function store(PbxDIdStoreRequest $request, PbxServices $pbxService, DidStore $action)
    {
        try {
            $data = new DidStoreData($request->validated(), $pbxService->tenant->pbxServer, $pbxService->tenant);
            $response = $action->handle($data);

            if (! $response['success']) {
                return $response;
            }
            Log::debug('Error: ', $response);

            /* @var PbxDID $did */
            $did = $response['data']['did'];

            PbxServicesDID::create([
                'pbx_service_id' => $pbxService->id,
                'pbx_did_id' => $did->id,
                'company_id' => $data->companyId,
                'creator_id' => $data->creatorId,
                'price' => $pbxService->pbxPackage->profileDid->did_rate,
                'name_prefix' => $pbxService->pbxPackage->profileDid->name,
            ]);

            PbxServicesReCalculateTrait::calculatePriceService($pbxService, false);

        } catch (\Throwable $th) {
            \Log::error($th->getTraceAsString());

            return response()->json(['error' => $th->getMessage(), 'code' => $th->getCode()], 500);
        }

        return response()->json(['success' => true], 201);

    }
}
