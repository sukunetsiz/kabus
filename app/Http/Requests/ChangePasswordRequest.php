<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'current_password' => ['required'],
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
            'current_password.required' => 'Mevcut şifre gereklidir.',
            'password.required' => 'Yeni şifre gereklidir.',
            'password.string' => 'Yeni şifre bir metin dizesi olmalıdır.',
            'password.min' => 'Yeni şifre en az 8 karakter olmalıdır.',
            'password.max' => 'Yeni şifre 40 karakteri geçemez.',
            'password.confirmed' => 'Yeni şifre onayı eşleşmiyor.',
            'password.regex' => 'Yeni şifre en az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermelidir.',
            'password_confirmation.required' => 'Lütfen yeni şifrenizi onaylayın.',
            'password_confirmation.string' => 'Şifre onayı bir metin dizesi olmalıdır.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->checkRateLimit();

            if (!Hash::check($this->current_password, Auth::user()->password)) {
                RateLimiter::hit($this->throttleKey(), 60);
                $validator->errors()->add('current_password', 'Girilen şifre mevcut şifrenizle eşleşmiyor.');
            }
        });
    }

    private function checkRateLimit()
    {
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'current_password' => ["Çok fazla şifre değiştirme denemesi. Lütfen {$seconds} saniye sonra tekrar deneyin."],
            ]);
        }

        RateLimiter::hit($key, 60);
    }

    private function throttleKey()
    {
        return 'password_change|' . $this->ip();
    }
}