<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ExtensionUpdateRequest;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServices;
use Crater\Services\Pbx\UpdateExtensionService;
use Exception;

class PbxServiceDetailExtUpdateController extends Controller
{
    public function __invoke(ExtensionUpdateRequest $request, $pbxService, $ext)
    {
        $service = new UpdateExtensionService();

        try {
            [$pbxService, $ext] = $this->check($pbxService, $ext);

            return $service->handle($pbxService->tenant, $ext, $request);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }

    /**
     * @throws Exception
     *
     * @return array{0: PbxServices, 1: PbxExtensions}
     */
    private function check(int $pbxService, int $ext): array
    {

        $service = PbxServices::find($pbxService);
        $ext = PbxExtensions::find($ext);

        if (is_null($ext) or is_null($service)) {
            throw new Exception('You need to Supply a valid id for PbxService and Extension');
        }

        if ($ext->pbxext_id == null) {
            throw new Exception('The Extension pbxext_id Is required');
        }

        if (($service->tenant->tenantid ?? null) == null) {
            throw new Exception('The Tenant tenantid Is required');
        }

        return [$service, $ext];
    }
}
