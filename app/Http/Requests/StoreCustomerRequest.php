<?php

namespace App\Http\Requests;

use App\Models\ErrorMessages;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'phone' => 'required|string|max:255',
        ];
    }
    public function messages(): array
    {
        $language = auth()->user()->language;
        return [
            'name.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'name')),
            'name.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'name')),
            'name.max' => ErrorMessages::getMessage($language, 'max', ErrorMessages::getTranslation($language, 'name'), 255),

            'phone.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'phone')),
            'phone.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'phone')),
            'phone.max' => ErrorMessages::getMessage($language, 'max', ErrorMessages::getTranslation($language, 'phone'), 255),
        ];
    }
}
