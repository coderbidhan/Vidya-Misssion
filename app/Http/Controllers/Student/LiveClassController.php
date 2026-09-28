<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\LiveClass;
use App\Services\ProgressService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProgressService $progressService) {}

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
        $past = $classes->whereIn('status', ['completed', 'cancelled'])->values();

        return $this->successResponse([
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    /**
     * Mark student attendance in live class and award rewards.
     *
     * POST /api/live-classes/{id}/attend
     */
    public function attend(Request $request, int $id): JsonResponse
    {
        $liveClass = LiveClass::with('chapter')->findOrFail($id);
        $user = $request->user();

        $reward = $this->progressService->awardReward($user, $liveClass->chapter, 'live_class_attendance', [
            'live_class_id' => $liveClass->id,
            'title' => $liveClass->title,
            'points' => 15,
            'message' => 'You earned 15 points for attending the live class!',
        ]);

        return $this->successResponse([
            'attended' => true,
            'reward' => $reward,
        ], 'Live class attendance recorded! Reward awarded.');
    }
}
