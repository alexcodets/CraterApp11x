<?php

namespace Crater\Http\Requests;

use Crater\Models\PrefixGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrefixGroupRequest extends FormRequest
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
                    'name' => [
                        'required',
                        'string',
                        'min:3',
                        'max:120',
                        Rule::unique('prefixrate_groups')->where(function ($query) {
                            $query->whereNull('deleted_at');
                        })
                    ],
                    'description' => 'max:65000'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                if (preg_match("/\/(\d+)$/", $this->url(), $mt)) {
                    $prefixGroup = PrefixGroup::find($mt[1]);
                }

                return [
                    'name' => [
                        'required',
                        'string',
                        'min:3',
                        'max:120',
                        Rule::unique('prefixrate_groups')->ignore($prefixGroup->id)
                            ->where(function ($query) {
                                $query->whereNull('deleted_at');
                            })
                    ],
                    'description' => 'max:65000'
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
