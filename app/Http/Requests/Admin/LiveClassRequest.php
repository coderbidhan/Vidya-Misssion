<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LiveClassRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'chapter_id'       => ['required', 'integer', 'exists:chapters,id'],
            'teacher_id'       => ['required', 'integer', 'exists:users,id'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'platform'         => ['required', 'in:google_meet,zoom,teams,other'],
            'meeting_url'      => ['required', 'url'],
            'meeting_id'       => ['nullable', 'string', 'max:100'],
            'scheduled_at'     => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ];
    }
}
