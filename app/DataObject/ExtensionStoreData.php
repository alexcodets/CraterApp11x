<?php

namespace Crater\DataObject;

use Crater\Models\PbxExtensions;
use Crater\Models\PbxServers;
use Crater\Models\PbxTenant;

class ExtensionStoreData
{
    public array $values;

    public int $creatorId;

    public int $companyId;

    public int $pbxTenantId;

    public PbxServers $pbxServer;

    public PbxTenant $pbxTenant;

    public string $name;

    public string $email;

    public string $protocol;

    public string $location;

    public int $ua_id;

    public string $status;

    private string $secret;

    public string $pin;

    public int $incoming_limit;

    public int $outgoing_limit;

    public bool $voicemail;

    public ?string $ext = null;

    public ?string $time_zone = null;

    public ?string $static_ip = null;

    public ?string $dhcp = null;

    public ?bool $auto_provisioning = null;

    public ?string $date_prorate = null;

    public ?string $ua_fullName = null;

    public ?string $ua_name = null;

    public ?string $macAddress;

    public ?int $extensionId = null;

    public ?int $extensionExt = null;

    public ?PbxExtensions $extension = null;

    public function __construct(array $values, PbxServers $servers, PbxTenant $tenant)
    {
        $this->values = $values;
        $this->creatorId = $values['creator_id'];
        $this->companyId = $values['company_id'];
        $this->pbxTenantId = $values['pbx_tenant_id'];
        $this->pbxServer = $servers;
        $this->pbxTenant = $tenant;
        //Required
        $this->name = $values['name'];
        $this->email = $values['email'];
        $this->protocol = $values['protocol'];
        $this->location = $values['location'];
        $this->ua_id = $values['ua_id'];
        $this->status = $values['status'];
        $this->secret = $values['secret'];
        $this->pin = $values['pin'];
        $this->incoming_limit = $values['incoming_limit'];
        $this->outgoing_limit = $values['outgoing_limit'];
        $this->voicemail = $values['voicemail'];

        //nullable
        $this->auto_provisioning = $values['auto_provisioning'] ?? null;
        $this->macAddress = $values['mac_address'] ?? null;
        $this->dhcp = $values['dhcp'] ?? null;
        $this->static_ip = $values['static_ip'] ?? null;
        $this->time_zone = $values['time_zone'] ?? null;

        $this->ext = $values['ext'] ?? null;
        $this->date_prorate = $values['date_prorate'] ?? null;
    }

    public function dataToApi(): array
    {

        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'prot' => $this->protocol,
            'location' => $this->location === 'local' ? 1 : 2,
            'ua' => $this->ua_id,
            'status' => $this->status === 'enabled' ? 1 : 0,
            'secret' => $this->secret,
            'pin' => $this->pin,
            'incominglimit' => $this->incoming_limit,
            'outgoinglimit' => $this->outgoing_limit,
            'voicemail' => $this->voicemail,
            'auto_provisioning' => $this->auto_provisioning,
            'macaddress' => $this->macAddress,
            'dhcp' => $this->dhcp,
            'static_ip' => $this->static_ip,
        ], fn ($value) => ! is_null($value) && $value !== '');

    }

    public function dataToModel(): array
    {
        return [
            'creator_id' => $this->creatorId,
            'company_id' => $this->companyId,
            'pbx_tenant_id' => $this->pbxTenantId,
            'name' => $this->name,
            'email' => $this->email,
            'ext' => $this->extensionExt,
            'extensionid' => $this->extensionId,
            'pbx_tenant_code' => $this->pbxTenant->tenantid,
            'status' => $this->status,
            'location' => $this->location,
            'macaddress' => $this->macAddress,
            'protocol' => $this->protocol,
            'pin' => $this->pin,
            'ua_id' => $this->ua_id,
            'date_prorate' => $this->date_prorate,
            'auto_provisioning' => $this->auto_provisioning,
            'dhcp' => $this->dhcp,
            'static_ip' => $this->static_ip,
            'time_zone' => $this->time_zone,
            'pbx_server_id' => $this->pbxServer->id,
            'api_id' => $this->extensionId ?? null,
        ];

    }
}
