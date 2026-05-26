<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeacherRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'mobile'        => ['nullable', 'string', 'max:20'],
            'password'      => ['required', 'string', 'min:6'],
            'bio'           => ['nullable', 'string'],
            'qualification' => ['nullable', 'string', 'max:255'],
        ];
    }
}
