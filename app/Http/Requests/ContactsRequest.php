<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactsRequest extends FormRequest
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
            'allow_receive_emails' => [
                'boolean',
                'nullable'
            ],
            'customer_id' => [
                'integer'
            ],
            'name' => [
                'string',
            ],
            'last_name' => [
                'string',
                'nullable'
            ],
            'phone' => [
                'string',
            ],
            'email' => [
                'string',
            ],
            'position' => [
                'string',
                'nullable'
            ],
            'type' => [
                'string',
                'nullable'
            ],
            'log_in_credentials' => [
                'boolean',
                'nullable'
            ],
            'password' => [
                'string',
                'nullable'
            ],
            'repeat_password' => [
                'string',
                'nullable'
            ],
            'invoices' => [
                'boolean',
            ],
            'estimates' => [
                'boolean',
            ],
            'payments' => [
                'boolean',
            ],
            'tickets' => [
                'boolean',
            ],
            'payments_accounts' => [
                'boolean',
            ],
            'reports' => [
                'boolean',
            ],
            //
            'email_estimates' => [
                'boolean',
            ],
            'email_invoices' => [
                'boolean',
            ],
            'email_payments' => [
                'boolean',
            ],
            'email_services' => [
                'boolean',
            ],
            'email_pbx_services' => [
                'boolean',
            ],
            'email_tickets' => [
                'boolean',
            ],
        ];
    }
}
