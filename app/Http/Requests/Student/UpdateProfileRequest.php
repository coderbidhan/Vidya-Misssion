<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'class' => ['sometimes', 'string', 'max:20'],
            'school_name' => ['sometimes', 'string', 'max:255'],
            'district' => ['sometimes', 'string', 'max:100'],
            'avatar' => ['sometimes', 'image', 'max:2048'],
        ];
    }
}
