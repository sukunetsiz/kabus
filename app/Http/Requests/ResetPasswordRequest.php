<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:40',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]])[A-Za-z\d#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]]{8,}$/',
            ],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Sıfırlama jetonu gereklidir.',
            'password.required' => 'Şifre gereklidir.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.max' => 'Şifre 40 karakteri geçemez.',
            'password.confirmed' => 'Şifre onayı eşleşmiyor.',
            'password.regex' => 'Şifre en az bir küçük harf, bir büyük harf, bir rakam ve bir özel karakter içermelidir.',
            'password_confirmation.required' => 'Lütfen şifrenizi onaylayın.',
        ];
    }
}