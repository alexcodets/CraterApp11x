<?php

namespace Crater\Models\actions;

use Crater\Pbxware\PbxWareApi;
use Exception;
use Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

trait PbxTenantAction
{
    public function suspend(PbxWareApi $api): array
    {
        try {
            $this->validate($api, 0);
            if ($this->isNewVersion($api)) {
                return $this->disableStatus($api);
            }

            return $this->suspendManual($api);
        } catch (Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    public function unsuspend(PbxWareApi $api): array
    {
        try {
            $this->validate($api, 1);
            if ($this->isNewVersion($api)) {
                return $this->enableStatus($api);
            }

            return $this->unsuspendManual($api);
        } catch (Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    /**
     *
     * @param PbxWareApi $api
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    private function suspendManual(PbxWareApi $api): array
    {
        try {
            //Log::debug('Before BDConfig');
            $this->updateDbConfig($api);
            //Log::debug('Before Clear Codecs');
            $this->clearCodecsAndChannels($api);

        } catch (Throwable $th) {
            $this->status = 1;
            $this->save();

            throw $th;
        }

        return ['success' => true, 'message' => __('pbxTenant.tenant.success.suspend')];
        //Testing how to use it.
        //PbxTenant::first()->suspend(new PbxWareApi(PbxServers::first()));
    }

    /**
     * @param PbxWareApi $api
     * @return array
     * @throws Exception
     * @throws Throwable
     */
    public function unsuspendManual(PbxWareApi $api): array
    {
        $this->validate($api, 1);
        $this->restoreCodecsAndChannels($api);
        $this->clearDbConfig();

        return ['success' => true, 'message' => __('pbxTenant.tenant.success.unsuspend')];
    }

    /**
     * @throws Exception
     */
    private function validate(PbxWareApi $api, bool $status): array
    {
        if ($this->status == $status) {
            throw new Exception(__('pbxTenant.tenant.errors.status.'.intval($status)));
        }

        if ($this->tenantid == null) {
            throw new Exception(__('pbxTenant.tenant.errors.tenantid'));
        }

        $apicheck = $api->checkConnection();
        if ($apicheck['success'] === false) {
            throw new Exception(__('pbxTenant.tenant.errors.check_connection').$apicheck['message']);
        }

        return ['success' => true];
    }

    /**
     * Update the config field of the tenant with the latest data from the pbxSystem.
     *
     * @param PbxWareApi $api
     * @return bool[]
     * @throws Exception
     */
    private function updateDbConfig(PbxWareApi $api): array
    {
        $config = $api->tenantConfiguration($this->tenantid);
        if (! $config['success']) {
            //Log::debug('Inside Fail config');
            throw new Exception($config['message']);
        }

        $this->config = $config['data'];
        $this->status = 0;
        $this->save();

        return ['success' => true];
    }

    private function clearDbConfig(): bool
    {
        $this->config = null;
        $this->status = 1;
        $this->save();

        return true;
    }

    /**
     * Clear the codec field in the PbxSystem from the Tenant.
     *
     * @param PbxWareApi $api
     * @return bool
     * @throws Exception
     */
    private function clearCodecsAndChannels(PbxWareApi $api): bool
    {
        $response = $api->tenantUpdate(
            $this->tenantid,
            ['local_channels' => '0', 'remote_channels' => '0', 'conferences' => '0', 'queues' => '0', 'auto_attendants' => '0', 'dahdi' => '0']
        );

        if (! $response['success']) {
            throw new Exception($response['message']);
        }

        return true;
    }

    /**
     * Restore the codec field in the PbxSystem from the tenant;
     *
     * @param PbxWareApi $api
     * @return bool
     * @throws Exception
     */
    private function restoreCodecsAndChannels(PbxWareApi $api): bool
    {
        $response = $api->tenantUpdate($this->tenantid, $this->getDataBack());
        if (! $response['success']) {
            throw new Exception(__('pbxTenant.tenant.errors.restoring_codecs').$response['message']);
        }

        return true;
    }

    private function getCodec(array $array): string
    {
        return implode(':', $array);
    }

    public function getCodecs(): array
    {
        $config = $this->getConfigurationAttribute();

        return array_filter([
            'localcodecs' => $this->getCodec($config->local_codecs ?? []),
            'remotecodecs' => $this->getCodec($config->remote_codecs ?? []),
            'networkcodecs' => $this->getCodec($config->network_codecs ?? []),
        ]);
    }

    public function getChannels(): array
    {
        $config = $this->getConfigurationAttribute();

        return array_filter([
            'local_channels' => $config->incominglimit ?? '',
            'remote_channels' => $config->outgoinglimit ?? '',
            'conferences' => $config->conch ?? '',
            'queues' => $config->quech ?? '',
            'auto_attendants' => $config->aach ?? '',
            'dahdi' => $config->zapch ?? '',
        ]);
    }

    public function getDataBack(): array
    {
        return array_merge($this->getChannels() + $this->getCodecs());
    }

    public function updateStatus(PbxWareApi $api, $status = 1)
    {
        return $api->tenantUpdate($this->tenantid, ['status' => $status]);
    }

    public function isNewVersion(PbxWareApi $api): bool
    {
        $response = $api->getLicenseInfo();
        if ($response['success'] === false) {
            return 0;
        }

        if (version_compare(strtok($response['data']['Version'], ' '), PbxWareApi::VERSION_API_TENANT_STATUS_CHANGE, '>=')) {
            return true;
        }

        return false;
    }

    /**
     * @param PbxWareApi $api
     * @return array
     * @throws Exception
     * @throws Throwable
     */
    public function disableStatus(PbxWareApi $api): array
    {
        $status = $this->updateStatus($api, 0);
        if ($status['success'] === false) {
            return ['success' => false, 'message' => $status['error']];
        }

        $this->status = 0;
        $this->save();

        return ['success' => true, 'message' => __('pbxTenant.tenant.success.suspend')];
    }

    /**
     * @param PbxWareApi $api
     * @return array
     * @throws Throwable
     */
    public function enableStatus(PbxWareApi $api): array
    {
        $status = $this->updateStatus($api, 1);
        if ($status['success'] === false) {
            return ['success' => false, 'message' => $status['error']];
        }
        $this->status = 1;
        $this->save();

        //TODO: esta parte.
        if (! $this->getDataBack()) {
            return ['success' => true, 'message' => __('pbxTenant.tenant.success.unsuspend')];
        }

        if ($this->settings()->where(['option' => 'disable_old_way'])->doesntExist()) {
            $codecs = implode(', ', $this->getCodecs());
            $channels = implode(', ', $this->getChannels());
            $this->settings()->create(['option' => 'disable_old_way', 'value' => json_encode($this->getDataBack())]);
            //$this->settings()->create(['option' => 'disable_old_way']);

            return ['success' => true, 'message' => __('pbxTenant.tenant.success.unsuspendOld', ['channels' => $channels, 'codecs' => $codecs])];
        }

        return ['success' => true, 'message' => __('pbxTenant.tenant.success.unsuspend')];

    }
}
