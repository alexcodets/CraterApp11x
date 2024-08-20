<?php

namespace Crater\Http\Requests;

class CustomSearchReportRequest extends BaseApiRequest
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
            'extension' => 'nullable|array',
            'typeSearch' => 'required|in:Tenant,Department,Extension',
            'startDate' => ['required', 'regex:/(\d{4}-\d{2}-\d{2}_\d{2}:\d{2}:\d{2})/u'],
            'endDate' => ['required', 'regex:/(\d{4}-\d{2}-\d{2}_\d{2}:\d{2}:\d{2})/u'],
            'includeServicesSuspended' => 'boolean',
            'tenant' => 'required_if:typeSearch,Tenant|array',
            'tenant.*.tenantid' => 'required_if:typeSearch,Tenant|int',
            'tenant.*.pbx_server_id' => 'required_if:typeSearch,Tenant|int',
            'departments' => 'required_if:typeSearch,Department|array',
            'departments.*.id' => 'required_if:typeSearch,Department|int',
            'extensions' => 'required_if:typeSearch,Extension|array',
            'extensions.*.id' => 'required_if:typeSearch,Extension|int',

        ];
    }

    public function messages(): array
    {
        return [
            'extensions.*.id.int' => 'Extension id must be a number',
            'endDate.regex' => 'The end date format is invalid, the right format is: "Y-m-d_H:i:s".',
            'startDate.regex' => 'The start date format is invalid, the right format is: "Y-m-d_H:i:s".',
            'includeServicesSuspended.boolean' => 'The include services suspended field must be true or false (0,1).',
            'typeSearch.in' => 'The selected type search is invalid, the avaliable options are: Tenant, Department, and Extension.',

        ];
    }
}
