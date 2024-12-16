<?php

namespace App\Http\Requests;

use App\Models\ErrorMessages;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'phone' => 'required|string',
            'password' => 'required|string',
            'default_lang' => 'nullable|string'
        ];
    }

    /**
     * Get the custom error messages for the validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $language = $this->input('default_lang');
        return [
            'phone.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'phone')),
            'phone.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'phone')),
            'password.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'password')),
            'password.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'password')),
            'default_lang.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'default_lang')),
        ];
    }
}
