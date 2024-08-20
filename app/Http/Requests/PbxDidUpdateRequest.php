<?php

namespace Crater\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;

class PbxDidUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $trunks = Cache::get('getTrunks', 'default');

        return [
            'trunk' => 'nullable',
            'number' => 'nullable',
            'dest_type' => 'bail|nullable|integer|between:0,6',
            'destination' => 'nullable',
            'disabled' => 'bail|nullable|integer|between:0,1',
            //'number'             => 'nullable|in:sip,iax',
            //'groupid'         => 'nullable',
//            'e164'            => 'nullable',
//            'e164_2'          => 'nullable',
//            'callerid'        => 'nullable',
//            'splan'           => 'nullable',
//            'call_rating_ext' => 'nullable',
//            'greeting'        => 'nullable',
//            'stripn'          => 'nullable',
//            'qprio'           => 'nullable',
//            'codec'           => 'nullable',
//            'ringtone'        => 'nullable',
//            'recordcall'      => 'nullable',
//            'state_text'      => 'nullable',
//            'city'            => 'nullable',
//            'areacode'        => 'nullable',
//            'sms_enabled'     => 'nullable',
        ];

    }

    public function messages(): array
    {
        return [
            'number.in' => __('pbxWare.validation.did.did.in'),
            'dest_type.between' => __('pbxWare.validation.did.dest_type.between'),
            'dest_type.integer' => __('pbxWare.validation.did.dest_type.integer'),
            'disabled.between' => __('pbxWare.validation.did.disabled.between'),
            'disabled.integer' => __('pbxWare.validation.did.disabled.integer'),
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
