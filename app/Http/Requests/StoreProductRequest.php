<?php

namespace App\Http\Requests;

use App\Models\ErrorMessages;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'purchase_price' => 'nullable|integer|min:0',
            'inventory' => 'required|integer',
        ];
    }
    public function messages(): array
    {
        $language = auth()->user()->language;
        return [
            'name.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'name')),
            'name.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'name')),
            'name.max' => ErrorMessages::getMessage($language, 'max', ErrorMessages::getTranslation($language, 'name'), 255),

            'price.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'price')),
            'price.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'price')),
            'price.min' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'price'), 0),

            'purchase_price.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'purchase_price')),
            'purchase_price.min' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'purchase_price'), 0),

            'inventory.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'inventory')),
            'inventory.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'inventory')),
            'inventory.gt' => ErrorMessages::getMessage($language, 'min', ErrorMessages::getTranslation($language, 'inventory'), 0),
        ];
    }
}
