<?php

namespace Crater\Http\Requests;

use Crater\Models\BwConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BandwidthRequest extends FormRequest
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
        switch ($this->method()) {
            case 'GET':
            case 'POST': {
                return [
                    'user' => [
                        'required',
                        'string',
                        'min:4',
                        Rule::unique('bw_configs')->where(function ($query) {
                            $query->whereNull('deleted_at');
                        })
                    ],
                    'password' => 'required',
                    'account_id' => 'required',
                    'url' => 'required',
                    'account_name' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                if (preg_match("/\/(\d+)$/", $this->url(), $mt)) {
                    $config = BwConfig::find($mt[1]);
                }

                return [
                    'user' => [
                        'required',
                        'string',
                        'min:4',
                        Rule::unique('bw_configs')->ignore($config->id)
                            ->where(function ($query) {
                                $query->whereNull('deleted_at');
                            })
                    ],
                    'account_id' => 'required',
                    'url' => 'required',
                    'account_name' => 'required',
                ];
            }
            case 'DELETE': {
                return [];
            }
            default:
                break;
        }
    }
}
