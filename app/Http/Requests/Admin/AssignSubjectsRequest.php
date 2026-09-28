<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignSubjectsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignments' => ['present', 'array'],
            'assignments.*.class_level' => ['required', 'integer', 'min:1', 'max:12'],
            'assignments.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ];
    }
}
