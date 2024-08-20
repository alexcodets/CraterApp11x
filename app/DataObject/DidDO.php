<?php

namespace Crater\DataObject;

class DidDO
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function toApi(): array
    {
        //DID
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

    public function fromApi()
    {
        # code...
    }

    public function updateValues(array $values)
    {
        $this->values = $values;
    }
}
