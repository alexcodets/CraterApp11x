<?php

namespace Crater\Http\Requests;

class PcodeLookupRequest extends BaseApiRequest
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
            'country' => 'nullable|string|between:2,3',
            'state' => 'nullable|string|size:2',
            'county' => 'nullable|string',
            'city' => 'nullable|string',
            'zip' => 'nullable|string',
            'best_match' => 'nullable|boolean',
            'limit' => 'nullable|integer|digits_between:1,1000',
            'npa' => 'nullable|max:7',
            'fips' => 'nullable|max:11',
        ];
    }
}
