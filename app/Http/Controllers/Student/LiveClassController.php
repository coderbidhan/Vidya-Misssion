<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    use ApiResponse;

    /**
     * List live classes for a chapter (upcoming and past).
     *
     * GET /api/chapters/{id}/live-classes
     */
    public function byChapter(Request $request, int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $classes = $chapter->liveClasses()
            ->with(['teacher:id,name'])
            ->orderBy('scheduled_at', 'desc')
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'platform', 'meeting_url', 'scheduled_at', 'duration_minutes', 'status'])
            ->map(function ($class) use ($hasAccess) {
                return [
                    'id' => $class->id,
                    'chapter_id' => $class->chapter_id,
                    'teacher_id' => $class->teacher_id,
                    'title' => $class->title,
                    'description' => $class->description,
                    'platform' => $class->platform,
                    'meeting_url' => $hasAccess ? $class->meeting_url : null,
                    'scheduled_at' => $class->scheduled_at,
                    'duration_minutes' => $class->duration_minutes,
                    'status' => $class->status,
                    'is_locked' => ! $hasAccess,
                    'teacher' => $class->teacher,
                ];
            });

        $upcoming = $classes->where('status', 'scheduled')->values();
        $past = $classes->whereIn('status', ['completed', 'cancelled'])->values();

        return $this->successResponse([
            'upcoming' => $upcoming,
            'past' => $past,
            'has_access' => $hasAccess,
        ]);
    }
}
