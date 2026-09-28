<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SetupProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'class' => ['required', 'string', 'max:20'],
            'school_name' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:100'],
        ];
    }
}
