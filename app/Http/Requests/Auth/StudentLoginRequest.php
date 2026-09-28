<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StudentLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['nullable', 'string', 'required_without:mobile'],
            'mobile' => ['nullable', 'string', 'required_without:login'],
            'password' => ['required', 'string'],
        ];
    }
}
