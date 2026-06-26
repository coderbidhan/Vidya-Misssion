<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StudentRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:100'],
            'mobile' => [
                'required',
                'string',
                'unique:users,mobile',
                'regex:/^[6-9]\d{9}$/',
            ],
            'class' => ['required', 'integer', 'between:1,12'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'The mobile number must be a valid 10-digit Indian mobile number.',
            'class.between' => 'The class must be between 1 and 12.',
        ];
    }
}
