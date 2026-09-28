<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:255', 'required_without:mobile'],
            'mobile' => ['nullable', 'string', 'max:20', 'required_without:email'],
        ];
    }
}
