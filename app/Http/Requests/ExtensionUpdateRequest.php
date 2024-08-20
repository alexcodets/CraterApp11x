<?php

namespace Crater\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExtensionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60',
            'pin' => 'nullable|digits:4',
            'protocol' => 'nullable|in:sip,iax',
            'email' => 'required|email|max:96',
            'ext' => 'nullable|integer|digits:4',
            'ua_id' => 'required|integer',
            'status' => 'nullable',
            'location' => 'required|in:local,remote',
            'auto_provisioning' => 'nullable|boolean',
            'mac_address' => ['required_if:auto_provisioning,1', 'nullable', 'regex:/^[0-9a-fA-F]{12}$/i'],
            'dhcp' => 'nullable|boolean',
            'static_ip' => ['required_if:dhcp,0', 'nullable', 'ip'],
            'time_zone' => ['nullable'],
        ];
        //"message": "Required field 'ext=a' contains invalid data (Regex: /^\\d{4}$/).",
        //none@careonecomm.com
    }

    public function messages(): array
    {
        return [
            'pin.digits' => __('pbxWare.validation.extension.pin.digits'),
            // 'pin.integer'   => __('pbxWare.validation.extension.pin.integer'),
            'protocol.in' => __('pbxWare.validation.extension.protocol.in'),
            'ext.digits' => __('pbxWare.validation.extension.extension.digits'),
            'ext.integer' => __('pbxWare.validation.extension.extension.integer'),
            'ua_id.integer' => __('pbxWare.validation.extension.ua_id.integer'),
            'auto_provisiong.boolean' => __('pbxWare.validation.extension.auto_provisioning.boolean'),
            'mac_address.regex' => __('pbxWare.validation.extension.mac_address.regex'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ]));
    }
}
