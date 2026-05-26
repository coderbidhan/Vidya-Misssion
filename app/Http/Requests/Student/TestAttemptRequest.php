<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class TestAttemptRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'answers'   => ['required', 'array'],
            'answers.*' => ['required', 'string', 'in:a,b,c,d'],
        ];
    }
}
