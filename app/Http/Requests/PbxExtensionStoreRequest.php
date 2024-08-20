<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxExtensionStoreRequest extends FormRequest
{
    public function rules(): array
    {

        //prot => protocol, ua => ua_id, location (local, remote) -> 1,2, incominglimit => incoming_limit, outgoinglimit => outgoing_limit
        return [
            'name' => ['required', 'max:60'],
            'email' => ['required', 'email', 'max:96'],
            'protocol' => ['required', 'in:sip,iax'],
            'location' => ['required', 'in:local,remote'],//local,remote
            'ua_id' => ['required', 'integer'], //Must be active
            'status' => ['required', 'in:enabled,disabled'],
            'secret' => ['required'],
            'pin' => ['required', 'digits:4'],
            'incoming_limit' => ['required', 'integer', 'min:0', 'max:9999'],
            'outgoing_limit' => ['required', 'integer', 'min:0', 'max:9999'],
            'voicemail' => ['required', 'in:1,0'],
            'auto_provisioning' => 'nullable|boolean',
            'mac_address' => ['required_if:auto_provisioning,1', 'nullable', 'regex:/^[0-9a-fA-F]{12}$/i'],
            'dhcp' => 'nullable|boolean',
            'static_ip' => ['required_if:dhcp,0', 'nullable', 'ip'],
            'time_zone' => ['nullable'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated(), [
            'creator_id' => auth()->id(),
            'company_id' => $this->pbxService->company_id,
            'pbx_tenant_id' => $this->pbxService->pbx_tenant_id,
        ]);
    }

    public function authorize(): bool
    {

        $this->route('pbxService')->load(['pbxPackage', 'tenant', 'tenant.pbxServer']);

        return $this->route('pbxService')->pbxPackage->extensions && auth()->user()->company_id === $this->route('pbxService')->company_id;


    }
}
