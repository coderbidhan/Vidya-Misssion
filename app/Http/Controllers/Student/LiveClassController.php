<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LiveClassController extends Controller
{
    use ApiResponse;

    /**
     * List live classes for a chapter (upcoming and past).
     *
     * GET /api/chapters/{id}/live-classes
     */
    public function byChapter(int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);

        $classes = $chapter->liveClasses()
            ->with(['teacher:id,name'])
            ->orderBy('scheduled_at', 'desc')
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'platform', 'meeting_url', 'scheduled_at', 'duration_minutes', 'status']);

        $upcoming = $classes->where('status', 'scheduled')->values();
        $past     = $classes->whereIn('status', ['completed', 'cancelled'])->values();

        return $this->successResponse([
            'upcoming' => $upcoming,
            'past'     => $past,
        ]);
    }
}
