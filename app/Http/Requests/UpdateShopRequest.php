<?php

namespace App\Http\Requests;

use App\Models\ErrorMessages;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShopRequest extends FormRequest
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
            'language' => 'required|string',
        ];
    }
    public function messages(): array
    {
        $language = null;
        if(ErrorMessages::isLanguageValid($this->input('language'))) {
            $language = $this->input('language');
        } else{
            $language = auth()->user()->language;
        }
        return [
            'name.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'name')),
            'name.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'name')),
            'name.max' => ErrorMessages::getMessage($language, 'max', ErrorMessages::getTranslation($language, 'name'), 255),
            'language.required' => ErrorMessages::getMessage($language, 'required', ErrorMessages::getTranslation($language, 'language')),
            'language.string' => ErrorMessages::getMessage($language, 'string', ErrorMessages::getTranslation($language, 'language')),
        ];
    }
}
