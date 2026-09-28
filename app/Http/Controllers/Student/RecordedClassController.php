<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\RecordedClass;
use App\Services\ProgressService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecordedClassController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProgressService $progressService) {}

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
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'gdrive_embed_url', 'thumbnail_url', 'duration_seconds']);

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
            $class->only(['id', 'title', 'description', 'gdrive_embed_url', 'thumbnail_url', 'duration_seconds', 'teacher'])
        );
    }

    /**
     * Mark recorded class as fully watched and award rewards.
     *
     * POST /api/recorded-classes/{id}/complete
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $class = RecordedClass::where('is_active', true)->with('chapter')->findOrFail($id);
        $user = $request->user();

        $reward = $this->progressService->awardReward($user, $class->chapter, 'video_watch_complete', [
            'class_id' => $class->id,
            'title' => $class->title,
            'points' => 10,
            'message' => 'You earned 10 points for completing this video class!',
        ]);

        return $this->successResponse([
            'completed' => true,
            'reward' => $reward,
        ], 'Class marked as completed! Reward awarded.');
    }
}
