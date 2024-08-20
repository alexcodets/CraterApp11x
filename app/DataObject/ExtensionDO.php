<?php

namespace Crater\DataObject;

class ExtensionDO
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function toApi(): array
    {
        // Ext
        return array_filter([
            //'server'    => $this->values['tenant_id'],
            'id' => $this->values['pbxext_id'],
            'name' => $this->values['name'] ?? null,
            'email' => $this->values['email'],
            'ext' => $this->values['ext'],
            'location' => $this->values['location'] == 'local' ? '1' : '2',
            'ua' => $this->values['ua_id'],
            'status' => $this->values['status'] == 'enabled' ? 1 : 0,
            'prot' => $this->values['protocol'],
            'macaddress' => $this->values['macaddress'],
            'pin' => $this->values['pin'],
            'acodecs' => implode(':', $this->values['codecs'] ?? []),
        ], 'strlen');
    }

    public static function fromRequestToApi($request): array
    {
        return array_filter([
            'id' => $request->pbxext_id,
            'name' => $request->name,
            'email' => $request->email,
            'ext' => $request->ext,
            'location' => $request->location == 'local' ? '1' : '2',
            'ua' => $request->ua_id,
            'status' => $request->status == 'enabled' ? 1 : 0,
            'prot' => $request->protocol,
            'macaddress' => $request->mac_address,
            'pin' => $request->pin,
            'acodecs' => implode(':', $request->values['codecs'] ?? []),
            'autoprovisiong' => $request->auto_provisioning,
            'dhcp' => $request->dhcp,
            'staticip' => $request->static_ip,
        ], 'strlen');
    }

    public static function fromRequestToModel($request): array
    {
        return array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'ext' => $request->ext,
            'status' => $request->status,
            'protocol' => $request->protocol,
            'pin' => $request->pin,
            'auto_provisioning' => $request->auto_provisioning,
            'macaddress' => $request->mac_address,
            'dhcp' => $request->dhcp ?? null,
            'static_ip' => $request->static_ip,
            'time_zone' => $request->time_zone,
            'location' => $request->location,
            'ua_id' => $request->ua_id,
            'ua_name' => $request->ua_name,
            'ua_fullname' => $request->ua_fullname,
        ], 'strlen');

    }
}
