<?php

namespace Crater\Http\Requests;

class AvalaraExemptionRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
            'frc' => ['nullable', 'boolean'],
            'tpe' => ['nullable', 'integer'],
            'dom' => ['nullable', 'integer', 'between:0,3'],
            'cat' => ['nullable', 'integer', 'between:0,13'],
            'exnb' => ['nullable', 'boolean'],
            'scp' => ['nullable', 'integer'],
            'avalara_locations_id' => ['required'],
            'exemption_name' => ['required'],
            'pbx_services_id' => ['nullable'],
            //'user_id'              => ['required'],
            'enable' => ['nullable', 'boolean'],
        ];
    }
}
