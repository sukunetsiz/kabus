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
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.string' => 'New password must be a string.',
            'password.min' => 'New password must be at least 8 characters.',
            'password.max' => 'New password cannot exceed 40 characters.',
            'password.confirmed' => 'New password confirmation does not match.',
            'password.regex' => 'New password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password_confirmation.required' => 'Please confirm your new password.',
            'password_confirmation.string' => 'Password confirmation must be a string.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->checkRateLimit();

            if (!Hash::check($this->current_password, Auth::user()->password)) {
                RateLimiter::hit($this->throttleKey(), 60);
                $validator->errors()->add('current_password', 'The entered password does not match your current password.');
            }
        });
    }

    private function checkRateLimit()
    {
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'current_password' => ["Too many password change attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        RateLimiter::hit($key, 60);
    }

    private function throttleKey()
    {
        return 'password_change|' . $this->ip();
    }
}