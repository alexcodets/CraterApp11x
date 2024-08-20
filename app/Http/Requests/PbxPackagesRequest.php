<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxPackagesRequest extends FormRequest
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
            'company_id' => [
                'nullable',
            ],
            'creator_id' => [
                'nullable',
                'integer'
            ],
            'taxes_id' => [
                'nullable',
                'integer'
            ],
            'package_tax_groups_id' => [
                'nullable',
                'integer'
            ],
            'item_group_id' => [
                'nullable',
                'integer'
            ],
            'item_groups' => [
            ],
            'items' => [
            ],
            'pbx_package_name' => [
                ''
            ],
            "status_payment" => [
                ''
            ],
            'description' => [
                ''
            ],
            'qty_available' => [
                ''
            ],
            'client_limit' => [
                ''
            ],
            'inclusive_minutes' => [
                ''
            ],
            'rate' => [
                ''
            ],
            'html' => [
                ''
            ],
            'text' => [
                ''
            ],
            'rate_per_minutes' => [
                ''
            ],
            'minutes_increments' => [
                ''
            ],
            'international_dialing_code' => [
                ''
            ],
            'national_dialing_code' => [
                ''
            ],
            'type_time_increment' => [
                ''
            ],
            'extensions' => [
                ''
            ],
            'did' => [
                ''
            ],
            'avalara_options' => [
                ''
            ],
            'avalara_price' => [
                ''
            ],
            'avalara_extension' => [
                ''
            ],
            'avalara_did' => [
                ''
            ],
            'avalara_callrating' => [
                ''
            ],
            'avalara_items' => [
                ''
            ],
            'template_did_id' => [
                ''
            ],
            'template_extension_id' => [
                ''
            ],
            'prefixrate_groups_id' => [
                ''
            ],
            'prefixrate_groups_outbound_id' => [
                ''
            ],
            'custom_destination_groups' => [
                ''
            ],
            'call_ratings' => [
                ''
            ],
            'unmetered' => [
                ''
            ],
            'automatic_suspension' => [
                ''
            ],
            'all_cdrs' => [ '' ],

            'discount_start_date' => [
                ''
            ],
            'discount_end_date' => [
                ''
            ],
            'discount_time_units' => [
                ''
            ],
            'discount_term' => [
                ''
            ],
            'discount_term_type' => [
                ''
            ],
            'package_discount' => [
                ''
            ],
            'type' => [
                ''
            ],
            'value_discount' => [
                ''
            ],
            'modify_server' => [
                ''
            ],
            'taxes' => [
                'nullable',
                ''
            ],
            'tax_groups' => [
                'nullable',
                ''
            ],
            'custom_app_rate_id' => [
                'nullable',
                ''
            ],
            'suspension_type' => [
                'nullable',
                ''
            ],
            'cdrStatus' => [
                'nullable',
                []
            ],
            'avalara_custom_app_rate_item_id' => [
                'nullable',
                ''
            ],
            'avalara_extension_item_id' => [
                'nullable',
                ''
            ],
            'avalara_did_item_id' => [
                'nullable',
                ''
            ],
            'cdr_items_id' => [
                'nullable',
                ''
            ],
            'custom_destinations_item_id' => [
                'nullable',
                ''
            ],
            'inter_custom_destinations_item_id' => [
                'nullable',
                ''
            ],
            'toll_free_custom_destinations_item_id' => [
                'nullable',
                ''
            ],
            'avalara_custom_app_rate_items' => [
                'nullable',
                ''
            ],
            'avalara_services_price_item' => [
                'boolean',
            ],
            'avalara_additional_charges_item' => [
                'boolean',
            ],
            'avalara_services_price_item_id' => [
                'nullable',
                ''
            ],
            'avalara_additional_charges_item_id' => [
                'nullable',
                ''
            ],
            'avalaraBundle' => [
                'boolean',
            ],
            'bundleTransaction' => [
                'integer',
                'required_if:avalaraBundle,1'

            ],
            'bundleService' => [
                'integer',
                'required_if:avalaraBundle,1'
            ],
            'apply_tax_type' => [
                'nullable',
                ''
            ],
            'update_child_services' => [
                'nullable',
                ''
            ],
        ];
    }
}
