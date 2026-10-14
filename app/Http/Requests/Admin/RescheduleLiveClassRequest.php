<?php

namespace App\Http\Requests\Admin;

use App\Models\LiveClass;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RescheduleLiveClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', 'date', 'after_or_equal:'.now()->subMinutes(5)->toDateTimeString()],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:480'],
            'reschedule_reason' => ['nullable', 'string', 'max:500'],
            'notify_students' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $classId = $this->route('id') ?? $this->route('live_class');
            $liveClass = LiveClass::find($classId);

            if ($liveClass && $liveClass->teacher_id && $this->scheduled_at) {
                $duration = $this->duration_minutes ?? $liveClass->duration_minutes ?? 60;
                $start = Carbon::parse($this->scheduled_at);
                $end = $start->copy()->addMinutes((int) $duration);

                $overlapExists = LiveClass::where('teacher_id', $liveClass->teacher_id)
                    ->whereIn('status', ['scheduled', 'live'])
                    ->where('id', '!=', $liveClass->id)
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhere(function ($sub) use ($start) {
                                $sub->where('scheduled_at', '<=', $start)
                                    ->where('ends_at', '>', $start);
                            });
                    })
                    ->exists();

                if ($overlapExists) {
                    $validator->errors()->add('scheduled_at', 'The assigned teacher has a scheduling conflict during this new time window.');
                }
            }
        });
    }
}
