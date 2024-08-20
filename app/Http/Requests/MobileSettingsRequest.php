<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MobileSettingsRequest extends FormRequest
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
            'logo' => [
                'nullable'
            ],
            'color_palette' => [
                'nullable',
            ],
            'dark_color_palette' => [
                'nullable',
            ],
            'horizontal_menu' => [
                'nullable'
            ],
            'vertical_menu' => [
                'nullable'
            ],
            'dashboard' => [
                'nullable'
            ],
            'firebase_token_notification' => [
                'nullable'
            ]
        ];
    }
}
