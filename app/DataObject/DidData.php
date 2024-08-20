<?php

namespace Crater\DataObject;

class DidData
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public static function fromRequestToApi($request): array
    {
        return array_filter([
            'id' => $request->pbxdid_id,
            'trunk' => $request->trunk,
            'did' => $request->number,
            'did_2' => $request->number_2,
            'e164' => $request->e164,
            'e164_2' => $request->e164_2,
            'disabled' => $request->disabled,
            'dest_type' => $request->dest_type,
            'destination' => $request->destination,
            'sms_enabled' => $request->sms_enabled,
        ], 'strlen');
    }

    public static function fromRequestToModel($request, ?int $tenantApiId): array
    {
        return array_filter([
            'trunk' => $request->trunk,
            'type' => self::getTypeName($request->dest_type),
            'number' => $request->number,
            'number_2' => $request->number_2,
            'ext' => $request->destination,
            'e164' => $request->e164,
            'e164_2' => $request->e164_2,
            'status' => is_null($request->disabled) ? null : ($request->disabled == 0 ? 'enabled' : 'disabled'),
            'server' => $tenantApiId,
        ], 'strlen');
    }

    public function toApi(): array
    {
        return [
            //'server'    => $this->values['tenantid'],
            'id' => $this->values['pbxdid_id'],
            'trunk' => $this->values['trunk'],
            'did' => $this->values['number'],
            'did_2' => $this->values['number_2'],
            'e164' => $this->values['e164'],
            'e164_2' => $this->values['e164_2'],
            'disabled' => $this->values['status'] == 'enabled' ? 0 : 1,
            'dest_type' => $this->values['type'] == 'Extension' ? 0 : 1,
            'destination' => $this->values['ext'],
        ];
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
