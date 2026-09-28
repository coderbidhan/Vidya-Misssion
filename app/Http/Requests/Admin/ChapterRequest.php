<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChapterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'class_level' => ['nullable', 'integer', 'min:1', 'max:12'],
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
            'theme' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
