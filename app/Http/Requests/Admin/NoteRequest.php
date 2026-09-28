<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chapter_id' => ['required', 'integer', 'exists:chapters,id'],
            'teacher_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['sometimes', 'file', 'max:20480', 'mimes:pdf,jpeg,jpg,png,doc,docx'],
            'file_url' => ['sometimes', 'url'],
            'file_type' => ['required', 'in:pdf,image,doc'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
