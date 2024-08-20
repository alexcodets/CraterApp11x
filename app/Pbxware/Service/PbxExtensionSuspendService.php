<?php

namespace Crater\Pbxware\Service;

use Crater\Models\PbxExtensions;
use Crater\Models\PbxServices;
use Crater\Pbxware\PbxWareApi;
use Exception;
use Illuminate\Support\Facades\Log;

class PbxExtensionSuspendService
{
    protected $tenant;

    protected $api;

    public function suspend(int $pbxService)
    {
        //Log::debug("--------The Extension Suspend Start for PbxService {$pbxService}---------");
        try {
            $this->check($pbxService);
            /** @var \Crater\Pbxware\PbxWareApi $api */
            list($extensions, $api) = $this->check($pbxService);

        } catch (\Throwable $th) {
            //Log::debug(['success' => false, 'message' => $th->getMessage()]);
            //Log::debug("--------The Extension Suspend Ends with error for PbxService {$pbxService}---------");
            return ['success' => false, 'message' => $th->getMessage()];
        }

        try {
            /** @var PbxExtensions $extension */
            foreach ($extensions as $extension) {
                //Log::debug($extension->suspend($api));
            }

        } catch (\Throwable $th) {
            //Log::debug(['success' => false, 'message' => $th->getMessage()]);
        }

        //Log::debug("--------The Extension Suspend Ends for PbxService {$pbxService}---------");
        return ['success' => true];

    }

    public function unsuspend(int $pbxService)
    {
        //Log::debug("--------The Extension UnSuspend Start for PbxService {$pbxService}---------");

        try {
            $this->check($pbxService);
            /** @var \Crater\Pbxware\PbxWareApi $api */
            list($extensions, $api) = $this->check($pbxService);

        } catch (\Throwable $th) {
            //Log::debug(['success' => false, 'message' => $th->getMessage()]);
            //Log::debug("--------The Extension UnSuspend Ends with error for PbxService {$pbxService}---------");
            return ['success' => false, 'message' => $th->getMessage()];
        }

        try {
            /** @var PbxExtensions $extension */
            foreach ($extensions as $extension) {
                //Log::debug($extension->unSuspend($api));
            }

        } catch (\Throwable $th) {
            //Log::debug(['success' => false, 'message' => $th->getMessage()]);
        }

        //Log::debug("--------The Extension UnSuspend Ends for PbxService {$pbxService}---------");
        return ['success' => true];

    }

    /**
     *
     * @param int $id
     * @return array [Crater\Models\PbxTenant, \Crater\Pbxware\PbxWareApi]
     * @throws \Exception
     */
    private function check(int $id): array
    {

        $pbxService = PbxServices::find($id);

        if ($pbxService == null) {
            throw new Exception('PbxService Id is not valid');
        }

        /** @var \Crater\Models\PbxServices $pbxService */
        $extensions = $pbxService->pbxExtensions;

        if ($extensions->isEmpty()) {
            throw new Exception("PbxService {$pbxService->id} does not have Extensions");
        }

        $server = $pbxService->pbxPackage->pbxServer;

        if ($server == null) {
            throw new Exception("PbxService {$pbxService->id} does not have a valid Server");
        }

        return [$extensions, new PbxWareApi($server)];

    }
}
