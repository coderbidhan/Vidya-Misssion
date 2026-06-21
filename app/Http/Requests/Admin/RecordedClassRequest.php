<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RecordedClassRequest extends FormRequest
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
            'video_type' => ['nullable', 'string', 'in:google_drive,youtube'],
            'video_url' => ['nullable', 'string'],
            'gdrive_file_id' => ['nullable', 'string', 'max:255'],
            'thumbnail_url' => ['nullable', 'url'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
