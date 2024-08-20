<?php

namespace Crater\Http\Requests;

use Crater\Pbxware\PbxWareApi;
use Illuminate\Foundation\Http\FormRequest;

class PbxDIdStoreRequest extends FormRequest
{
    public function rules(): array
    {
        $this->route('pbxService')->load(['tenant', 'tenant.pbxServer']);
        $api = new PbxWareApi($this->route('pbxService')->tenant->pbxServer);
        $trunks = $api->getTrunks();
        \Log::debug($trunks);
        $trunks = $trunks['success'] ? array_keys($trunks['data']) : [];

        \Log::debug($trunks);

        return [
            'trunk' => ['bail','required', 'int', 'in:'.implode(',', $trunks)],
            'number' => 'required|int',
            'dest_type' => 'bail|required|integer|between:0,6',
            'destination' => 'required',
            'disabled' => 'bail|required|integer|between:0,1',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated(), [
            'creator_id' => auth()->id(),
            'company_id' => $this->route('pbxService')->company_id,
            'pbx_tenant_id' => $this->route('pbxService')->pbx_tenant_id,
        ]);
    }

    public function authorize(): bool
    {

        $this->route('pbxService')->load(['pbxPackage', 'tenant', 'tenant.pbxServer']);

        return $this->route('pbxService')->pbxPackage->did && auth()->user()->company_id === $this->route('pbxService')->company_id;


    }
}
