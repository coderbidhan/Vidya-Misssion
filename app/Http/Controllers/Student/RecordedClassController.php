<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\RecordedClass;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecordedClassController extends Controller
{
    use ApiResponse;

    /**
     * List recorded classes for a chapter.
     *
     * GET /api/chapters/{id}/recorded-classes
     */
    public function byChapter(Request $request, int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $classes = $chapter->recordedClasses()
            ->where('is_active', true)
            ->with(['teacher:id,name'])
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'video_url', 'gdrive_file_id', 'gdrive_embed_url', 'thumbnail_url', 'duration_seconds'])
            ->map(function ($class) use ($hasAccess) {
                return [
                    'id' => $class->id,
                    'chapter_id' => $class->chapter_id,
                    'teacher_id' => $class->teacher_id,
                    'title' => $class->title,
                    'description' => $class->description,
                    'video_url' => $hasAccess ? $class->video_url : null,
                    'gdrive_embed_url' => $hasAccess ? $class->gdrive_embed_url : null,
                    'thumbnail_url' => $class->thumbnail_url,
                    'duration_seconds' => $class->duration_seconds,
                    'is_locked' => ! $hasAccess,
                    'teacher' => $class->teacher,
                ];
            });

        return $this->successResponse($classes);
    }

    /**
     * Get a single recorded class (no gdrive_file_id exposed).
     *
     * GET /api/recorded-classes/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $class = RecordedClass::where('is_active', true)
            ->with(['teacher:id,name', 'teacher.teacherProfile:user_id,qualification,avatar_url'])
            ->findOrFail($id);

        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $data = [
            'id' => $class->id,
            'title' => $class->title,
            'description' => $class->description,
            'video_url' => $hasAccess ? $class->video_url : null,
            'gdrive_embed_url' => $hasAccess ? $class->gdrive_embed_url : null,
            'thumbnail_url' => $class->thumbnail_url,
            'duration_seconds' => $class->duration_seconds,
            'is_locked' => ! $hasAccess,
            'teacher' => $class->teacher,
        ];

        if (! $hasAccess) {
            $data['subscription_required'] = true;
        }

        return $this->successResponse($data);
    }
}
