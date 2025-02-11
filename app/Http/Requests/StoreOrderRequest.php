<?php

namespace App\Http\Requests;

use App\Models\ErrorMessages;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'nullable|integer',
            'discount' => 'nullable|integer',
            'products' => 'required|array',
            'products.*' => 'required|int|min:1',
            'paid' => 'nullable|integer|min:0',
        ];
    }
    public function messages(): array
    {
        $language = auth()->user()->language;
        return [
            'customer_id.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'customer')),
            'discount.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'discount')),

            'products.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'products')),
            'products.array' => ErrorMessages::getMessage($language, 'array', ErrorMessages::getTranslation($language, 'products')),

            'products.*.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'products.*')),
            'products.*.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'products.*')),
            'products.*.min' => ErrorMessages::getMessage($language, 'min', ErrorMessages::getTranslation($language, 'products.*'), 1),

            'paid.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'paid')),
            'paid.min' => ErrorMessages::getMessage($language, 'min', ErrorMessages::getTranslation($language, 'paid'), 1),
        ];
    }
}
