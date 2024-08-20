<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxServicesRequest extends FormRequest
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
            'company_id' => [ 'exists:companies,id' ],
            'creator_id' => [ 'nullable', 'integer' ],
            'customer_id' => [ '' ],
            // 'tax_type' => [ '' ],
            'cap_extension' => [''],
            'cap_total' => [''],
            'pbx_package_id' => [ 'nullable', 'integer' ],
            'pbx_tenant_id' => [ 'nullable', '' ],
            'prefixrate_groups_id' => [ 'nullable' ],
            'prefixrate_groups_outbound_id' => [ 'nullable' ],
            'status' => [''],
            'term' => [ 'nullable', '' ],
            'date_begin' => [ 'nullable', '' ],
            'renewal_date' => [ 'nullable', 'date' ],
            'allow_discount' => [ '' ],
            'auto_suspension' => [ '' ],
            'allow_pbxpackages' => [ '' ],
            'allow_items' => [ '' ],
            'allow_extensions' => [ '' ],
            'allow_did' => [ '' ],
            'allow_aditionalcharges' => [ '' ],
            'allow_usagesummary' => [ '' ],
            'only_callrating' => [ '' ],
            'allow_discount_value' => [ '' ],
            'allow_discount_type' => [ '' ],
            'date_from' => [
                'nullable',
                ''
            ],
            'date_to' => [
                'nullable',
                ''
            ],
            'time_period' => [
                'nullable',
                ''
            ],
            'time_period_value' => ['nullable', ''],
            'time_period_type' => ['nullable'],
            'items' => [
                ''
            ],
            'dids' => [
                ''
            ],
            'extensions' => [
                ''
            ],
            'pbxpackages_price' => [ '' ],
            'sub_total' => [ 'nullable' ],
            'total' => [ 'nullable' ],
            /* 'total_prorate'=> [ '' ], */
            'tax' => [  'nullable'  ],
            'inclusive_minutes_seconds_consumed' => [ 'nullable' ],
            'addresses_id' => [  'nullable'  ],
            'main_update' => [ 'nullable'  ],
            'allow_pbx_packages_update' => [ 'nullable'  ],
            'custom_destination_groups' => [
                ''
            ],
        ];
    }
}
