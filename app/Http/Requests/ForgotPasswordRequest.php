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
            'username.required' => 'Please enter your username.',
            'username.max' => 'Username cannot be longer than 16 characters.',
            'mnemonic.required' => 'Please enter your mnemonic phrase.',
            'mnemonic.max' => 'Mnemonic phrase cannot be longer than 512 characters.',
        ];
    }
}