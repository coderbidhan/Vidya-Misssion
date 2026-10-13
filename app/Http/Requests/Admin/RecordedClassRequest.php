<?php

namespace App\Http\Requests\Admin;

use App\Services\YouTubeService;
use Illuminate\Foundation\Http\FormRequest;

class RecordedClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'chapter_id' => [$isPost ? 'required' : 'sometimes', 'integer', 'exists:chapters,id'],
            'teacher_id' => [$isPost ? 'required' : 'sometimes', 'integer', 'exists:users,id'],
            'title' => [$isPost ? 'required' : 'sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'gdrive_file_id' => [$isPost ? 'required_without:video_url' : 'nullable', 'nullable', 'string', 'max:255'],
            'thumbnail_url' => ['nullable', 'url'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $videoUrl = $this->input('video_url');
            if (! empty($videoUrl)) {
                if (! YouTubeService::isValidYouTube($videoUrl)) {
                    $validator->errors()->add('video_url', 'Please provide a valid YouTube video URL or 11-character video ID.');
                }
            }
        });
    }
}
