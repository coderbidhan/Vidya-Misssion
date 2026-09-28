<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Progress;
use App\Models\Subject;
use App\Services\ProgressService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProgressService $progressService) {}

    /**
     * List chapters for a subject with progress status.
     *
     * GET /api/subjects/{id}/chapters
     */
    public function bySubject(Request $request, int $subjectId): JsonResponse
    {
        $subject = Subject::where('is_active', true)->findOrFail($subjectId);
        $user = $request->user();

        // Check if user has an active paid subscription
        $hasActiveSubscription = $user->hasActiveSubscription();
        $studentClass = $user->studentProfile?->class;

        if ($request->filled('class_level')) {
            $classParam = strtolower((string) $request->class_level);
            $targetClass = in_array($classParam, ['all', '0', 'null', 'none']) ? null : (int) $request->class_level;
        } else {
            $targetClass = $studentClass ? (int) $studentClass : null;
        }

        $completedChapterIds = Progress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('chapter_id')
            ->toArray();

        $chapters = $subject->chapters()
            ->where('is_active', true)
            ->when($targetClass, fn ($q) => $q->where(fn ($inner) => $inner->where('class_level', $targetClass)
                ->orWhereNull('class_level')
            ))
            ->orderBy('class_level')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($chapter) => [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'image' => $chapter->image,
                'color' => $chapter->color,
                'theme' => $chapter->theme,
                'class_level' => $chapter->class_level,
                'sort_order' => $chapter->sort_order,
                'is_completed' => in_array($chapter->id, $completedChapterIds),
            ]);

        return $this->successResponse([
            'subject' => $subject->only(['id', 'name', 'color', 'icon', 'image']),
            'class_level' => $targetClass,
            'is_trial_mode' => ! $hasActiveSubscription,
            'is_subscription_active' => $hasActiveSubscription,
            'chapters' => $chapters,
        ]);
    }

    /**
     * Get a single chapter with its completion status.
     *
     * GET /api/chapters/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->with('subject')->findOrFail($id);
        $user = $request->user();

        $isCompleted = Progress::where('user_id', $user->id)
            ->where('chapter_id', $id)
            ->where('is_completed', true)
            ->exists();

        return $this->successResponse([
            'chapter' => $chapter,
            'is_completed' => $isCompleted,
        ]);
    }

    /**
     * Mark a chapter as completed and award a reward.
     *
     * POST /api/chapters/{id}/complete
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($id);
        $user = $request->user();

        $this->progressService->markChapterComplete($user, $chapter);
        $reward = $this->progressService->awardReward($user, $chapter, 'chapter_complete', [
            'chapter_title' => $chapter->title,
            'message' => 'Congratulations! Chapter completed!',
        ]);

        return $this->successResponse([
            'completed' => true,
            'reward' => $reward,
        ], 'Chapter marked as completed!');
    }
}
