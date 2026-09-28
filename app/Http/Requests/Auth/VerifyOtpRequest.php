<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'required_without:mobile'],
            'mobile' => ['nullable', 'string', 'required_without:email'],
            'otp' => ['required', 'string', 'digits:6'],
        ];
    }
}
