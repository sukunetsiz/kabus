<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->is('register')) {
            return $this->registrationRules();
        }

        return $this->loginRules();
    }

    private function registrationRules(): array
    {
        $captchaLength = strlen($this->session()->get('captcha_code', ''));
        return [
            'username' => [
                'required',
                'string',
                'min:4',
                'max:16',
                'unique:users',
                'regex:/^[a-zA-Z0-9]+$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:40',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]])[A-Za-z\d#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]]{8,}$/',
            ],
            'password_confirmation' => ['required', 'string'],
            'reference_code' => [
                'required',
                'string',
                'size:16',
                function ($attribute, $value, $fail) {
                    $validReference = User::all()->contains(function ($user) use ($value) {
                        return $user->reference_id === $value;
                    });

                    if (!$validReference) {
                        $fail('Geçersiz referans numarası.');
                    }
                },
            ],
            'captcha' => ['required', 'string', "size:$captchaLength"],
        ];
    }

    private function loginRules(): array
    {
        $captchaLength = strlen($this->session()->get('captcha_code', ''));
        return [
            'username' => [
                'required',
                'string',
                'min:4',
                'max:16',
                'regex:/^[a-zA-Z0-9]+$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:40',
            ],
            'captcha' => ['required', 'string', "size:$captchaLength"],
        ];
    }

    public function messages(): array
    {
        $captchaLength = strlen($this->session()->get('captcha_code', ''));
        return [
            'username.required' => 'Kullanıcı adı gereklidir.',
            'username.min' => 'Kullanıcı adı en az 4 karakter olmalıdır.',
            'username.max' => 'Kullanıcı adı 16 karakteri geçemez.',
            'username.unique' => 'Bu kullanıcı adı zaten alınmış.',
            'username.regex' => 'Kullanıcı adı yalnızca harf ve rakam içerebilir.',
            'password.required' => 'Şifre gereklidir.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.max' => 'Şifre 40 karakteri geçemez.',
            'password.regex' => 'Şifre en az bir küçük harf, bir büyük harf, bir rakam ve bir özel karakter içermelidir.',
            'reference_code.required' => 'Referans numarası gereklidir.',
            'reference_code.size' => 'Referans numarası tam olarak 16 karakter olmalıdır.',
            'captcha.required' => 'CAPTCHA gereklidir.',
            'captcha.size' => "CAPTCHA tam olarak $captchaLength karakter olmalıdır.",
        ];
    }
}
