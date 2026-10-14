<?php

namespace App\Http\Requests\Admin;

use App\Models\LiveClass;
use App\Models\User;
use App\Services\MeetingProviderService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LiveClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $classId = $this->route('id') ?? $this->route('live_class');
        $isCreate = $this->isMethod('POST');

        return [
            'class_level' => ['nullable', 'integer', 'min:1', 'max:12'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'chapter_id' => ['nullable', 'integer', 'exists:chapters,id'],
            'teacher_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'learning_objectives' => ['nullable', 'string', 'max:5000'],
            'platform' => ['required', 'in:google_meet,zoom,teams,youtube_live,other'],
            'meeting_url' => ['required', 'url', 'max:1000'],
            'meeting_id' => ['nullable', 'string', 'max:100'],
            'scheduled_at' => [
                'required',
                'date',
                $isCreate ? 'after_or_equal:'.now()->subMinutes(5)->toDateTimeString() : 'date',
            ],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:scheduled,live,completed,cancelled'],
            'is_published' => ['nullable', 'boolean'],
            'recording_url' => ['nullable', 'url', 'max:1000'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // 1. Ensure at least chapter_id OR (class_level and subject_id) is provided
            if (empty($this->chapter_id) && (empty($this->class_level) || empty($this->subject_id))) {
                $validator->errors()->add('chapter_id', 'Please select either a chapter, or both a class level and subject.');
            }

            // 2. Validate teacher role
            if ($this->teacher_id) {
                $teacher = User::find($this->teacher_id);
                if ($teacher && ! in_array($teacher->role, ['teacher', 'admin'])) {
                    $validator->errors()->add('teacher_id', 'The assigned user must have a teacher or admin role.');
                }
            }

            // 3. Validate meeting URL security and provider format
            if (! empty($this->meeting_url)) {
                $validation = MeetingProviderService::validateMeetingUrl($this->meeting_url, $this->platform);
                if (! $validation['valid']) {
                    $validator->errors()->add('meeting_url', $validation['error'] ?? 'Invalid meeting URL.');
                }
            }

            // 4. Check teacher schedule overlap
            if ($this->teacher_id && $this->scheduled_at && $this->duration_minutes) {
                $classId = $this->route('id') ?? $this->route('live_class');
                $start = Carbon::parse($this->scheduled_at);
                $end = $start->copy()->addMinutes((int) $this->duration_minutes);

                $overlapExists = LiveClass::where('teacher_id', $this->teacher_id)
                    ->whereIn('status', ['scheduled', 'live'])
                    ->when($classId, fn ($q) => $q->where('id', '!=', $classId))
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhere(function ($sub) use ($start) {
                                $sub->where('scheduled_at', '<=', $start)
                                    ->where('ends_at', '>', $start);
                            });
                    })
                    ->exists();

                if ($overlapExists) {
                    $validator->errors()->add('teacher_id', 'The selected teacher already has another live class scheduled during this time window.');
                }
            }
        });
    }
}
