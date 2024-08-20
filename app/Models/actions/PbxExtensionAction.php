<?php

namespace Crater\Models\actions;

use Crater\Pbxware\PbxWareApi;
use Exception;

trait PbxExtensionAction
{
    /**
     *
     * @param \Crater\Pbxware\PbxWareApi $api
     * @return array
     */
    public function suspend(PbxWareApi $api): array
    {
        try {
            $this->validate($api, 'disabled');
            $this->disable($api);
            $this->status = 'disabled';
            $this->save();

        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

        return ['success' => true, 'message' => __('PbxService.extension.suspend.success.suspend', ['extension' => $this->id])];
        //Testing how to use it.
        //PbxExtensions::find(2)->suspend(new PbxWareApi(PbxServers::first()));
    }

    public function unSuspend(PbxWareApi $api)
    {
        try {
            $this->validate($api, 'enabled');
            $this->enable($api);
            $this->status = 'enabled';
            $this->save();

        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

        return ['success' => true, 'message' => __('PbxService.extension.suspend.success.unsuspend', ['extension' => $this->id])];

    }

    private function enable(PbxWareApi $api)
    {
        $api->extensionUpdate($this->pbx_tenant_code, ['status' => 1, 'id' => $this->pbxext_id]);
    }

    private function disable(PbxWareApi $api)
    {
        $api->extensionUpdate($this->pbx_tenant_code, ['status' => 0, 'id' => $this->pbxext_id]);
    }

    private function validate(PbxWareApi $api, string $status): array
    {
        if ($this->status == $status) {
            throw new Exception(__("PbxService.extension.suspend.errors.status.{$status}", ['extension' => $this->id]));
        }

        if ($this->pbxext_id == null) {
            throw new Exception(__('PbxService.extension.suspend.errors.extensionid', ['extension' => $this->id]));
        }

        if ($this->pbx_tenant_code == null) {
            throw new Exception(__('PbxService.extension.suspend.errors.tenantcode', ['extension' => $this->id]));
        }

        $apicheck = $api->checkConnection();
        if ($apicheck['success'] === false) {
            throw new Exception(__('PbxService.extension.suspend.errors.check_connection').$apicheck['message']);
        }

        $ext = $api->extensionConfiguration($this->pbx_tenant_code, $this->pbxext_id);

        if ($ext['success'] === false) {
            throw new Exception(__('PbxService.extension.suspend.errors.check_config').$ext['message']);
        }

        return ['success' => true];
    }
}
