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
        $rules = [
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
            'captcha' => ['required', 'string', "size:$captchaLength"],
        ];

        // Reference code validation rules
        $referenceCodeRules = [
            'string',
            'size:16',
            function ($attribute, $value, $fail) {
                if ($value !== null) {
                    $validReference = User::all()->contains(function ($user) use ($value) {
                        return $user->reference_id === $value;
                    });

                    if (!$validReference) {
                        $fail('Invalid reference number.');
                    }
                }
            },
        ];

        // Add required rule if configured
        if (config('marketplace.require_reference_code', true)) {
            array_unshift($referenceCodeRules, 'required');
        } else {
            array_unshift($referenceCodeRules, 'nullable');
        }

        $rules['reference_code'] = $referenceCodeRules;
        return $rules;
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
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 4 characters.',
            'username.max' => 'Username cannot exceed 16 characters.',
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'Username can only contain letters and numbers.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot exceed 40 characters.',
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'reference_code.required' => 'Reference number is required.',
            'reference_code.size' => 'Reference number must be exactly 16 characters.',
            'captcha.required' => 'CAPTCHA is required.',
            'captcha.size' => "CAPTCHA must be exactly $captchaLength characters.",
        ];
    }
}
