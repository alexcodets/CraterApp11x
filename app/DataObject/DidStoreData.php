<?php

namespace Crater\DataObject;

use Crater\Models\PbxServers;
use Crater\Models\PbxTenant;

class DidStoreData
{
    public int $didId;

    public int $didNumber;

    private int $trunk;

    private int $dest_type;

    private int $number;

    private int $destination;

    private bool $disabled;

    public PbxServers $pbxServer;

    public PbxTenant $tenant;

    public int $creatorId;

    public int $companyId;

    public int $pbxTenantId;

    public function __construct(array $data, PbxServers $server, PbxTenant $tenant)
    {
        $this->creatorId = $data['creator_id'];
        $this->companyId = $data['company_id'];
        $this->pbxTenantId = $data['pbx_tenant_id'];
        $this->trunk = $data['trunk'];
        $this->number = $data['number'];
        $this->dest_type = $data['dest_type'];
        $this->destination = $data['destination'];
        $this->disabled = $data['disabled'];
        $this->pbxServer = $server;
        $this->tenant = $tenant;
    }

    public function dataToApi(): array
    {
        return array_filter([
            'trunk' => $this->trunk,
            'did' => $this->number,
            'dest_type' => $this->dest_type,
            'destination' => $this->destination,
            'disabled' => $this->disabled,
        ], fn ($value) => ! is_null($value) && $value !== '');
    }

    public function dataToModel(): array
    {
        return array_filter([
            'creator_id' => $this->creatorId,
            'company_id' => $this->companyId,
            'pbx_tenant_id' => $this->pbxTenantId,
            'trunk' => $this->trunk,
            'ext' => $this->destination,
            'number' => $this->number,
            'type' => self::getTypeName($this->dest_type),
            'destination' => $this->destination,
            'status' => $this->disabled == 1 ? 'disabled' : 'enabled',
            'didid' => $this->didId,
            'pbx_tenant_code' => $this->tenant->tenantid,
            'pbxdid_id' => $this->didId,
            'pbx_server_id' => $this->pbxServer->id,
            'api_id' => $this->didId,
            'server' => $this->tenant->tenantid,
        ], fn ($value) => ! is_null($value) && $value !== '');

    }

    public static function getTypeName(?int $type = null): ?string
    {
        $types = [
            'Extension',
            'Forward DID to Extension (Multi User)',
            'Ring Group',
            'IVR',
            'Queues',
            'External Number',
            'IVR tree',
        ];

        if (! is_numeric($type)) {
            return null;
        }

        return $type < count($types) ? $types[$type] : 'Unknown';

    }
}
