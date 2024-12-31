<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|string|max:16',
            'mnemonic' => 'required|string|max:512',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Lütfen kullanıcı adınızı girin.',
            'username.max' => 'Kullanıcı adı 16 karakterden uzun olamaz.',
            'mnemonic.required' => 'Lütfen anımsatıcı cümlenizi girin.',
            'mnemonic.max' => 'Anımsatıcı cümle 512 karakterden uzun olamaz.',
        ];
    }
}