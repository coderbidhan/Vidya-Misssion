<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\RecordedClass;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RecordedClassController extends Controller
{
    use ApiResponse;

    /**
     * List recorded classes for a chapter.
     *
     * GET /api/chapters/{id}/recorded-classes
     */
    public function byChapter(int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);

        $classes = $chapter->recordedClasses()
            ->where('is_active', true)
            ->with(['teacher:id,name'])
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'video_type', 'video_url', 'gdrive_embed_url', 'thumbnail_url', 'duration_seconds']);

        return $this->successResponse($classes);
    }

    /**
     * Get a single recorded class (no gdrive_file_id exposed).
     *
     * GET /api/recorded-classes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $class = RecordedClass::where('is_active', true)
            ->with(['teacher:id,name', 'teacher.teacherProfile:user_id,qualification,avatar_url'])
            ->findOrFail($id);

        return $this->successResponse(
            $class->only(['id', 'title', 'description', 'video_type', 'video_url', 'gdrive_embed_url', 'thumbnail_url', 'duration_seconds', 'teacher'])
        );
    }
}
