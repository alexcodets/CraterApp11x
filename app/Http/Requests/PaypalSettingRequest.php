<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaypalSettingRequest extends FormRequest
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
        $data = [
            // 'paypal_id' => [
            //     'required',
            //     'string'
            // ],
            'email' => [
                'required',
                'email'
            ],
            'merchant_id' => [
                'required',
                'string'
            ],
            'public_key' => [
                'required',
                'string'
            ],
            'private_key' => [
                'required',
                'string'
            ],
            'enviroment' => [
                'required',
                'string'
            ],
            // 'paypal_secret' => [
            //     'required',
            //     'string'
            // ],
            // 'paypal_signature' => [
            //     'required',
            //     'string'
            // ],
            // 'test_mode' => [
            //     'required',
            //     'integer'
            // ],
            // 'developer_mode' => [
            //     'required',
            //     'integer'
            // ],
            'status' => [
                'nullable'
            ],
            'currency' => [
                'required'
            ]
        ];

        /*if ($this->getMethod() == 'PUT') {
            $data['paypal_id'] = [
                'required',
                'string'
            ];
            $data['email'] = [
                'required',
                'email'
            ];
            $data['paypal_secret'] = [
                'required',
                'string'
            ];
            $data['paypal_signature'] = [
                'required',
                'string'
            ];
            $data['test_mode'] = [
                'required',
                'integer'
            ];
            $data['developer_mode'] = [
                'required',
                'integer'
            ];
            $data['status'] = [
                'required'
            ];
            $data['currency'] = [
                'required'
            ];
        }*/

        return $data;
    }
}
