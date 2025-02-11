<?php

namespace App\Http\Requests;

use App\Models\ErrorMessages;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'amount' => 'required|integer:min:1',
        ];
    }
    public function messages(): array
    {
        $language = auth()->user()->language;
        return [
            'amount.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'amount')),
            'amount.integer' => ErrorMessages::getMessage($language, 'integer', ErrorMessages::getTranslation($language, 'amount')),
            'amount.min' => ErrorMessages::getMessage($language, 'min', ErrorMessages::getTranslation($language, 'amount'), 1),
        ];
    }
}
