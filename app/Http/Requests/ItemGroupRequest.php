<?php

namespace Crater\Http\Requests;

use Crater\Models\ItemGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemGroupRequest extends FormRequest
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
                        Rule::unique('item_groups')->where(function ($query) {
                            $query->whereNull('deleted_at');
                        })
                    ],
                    'description' => 'max:65000',
                    'no_taxable' => 'nullable',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                if (preg_match("/\/(\d+)$/", $this->url(), $mt)) {
                    $itemGroup = ItemGroup::find($mt[1]);
                }

                return [
                    'name' => [
                        'required',
                        'string',
                        'min:3',
                        'max:120',
                        Rule::unique('item_groups')->ignore($itemGroup->id)
                            ->where(function ($query) {
                                $query->whereNull('deleted_at');
                            })
                    ],
                    'description' => 'max:65000',
                    'no_taxable' => 'nullable',
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
