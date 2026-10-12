<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\TestAttemptRequest;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Services\ProgressService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProgressService $progressService) {}

    /**
     * List tests for a chapter.
     *
     * GET /api/chapters/{id}/tests
     */
    public function byChapter(Request $request, int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $tests = $chapter->tests()
            ->where('is_active', true)
            ->get(['id', 'chapter_id', 'title', 'total_marks', 'pass_marks', 'duration_minutes'])
            ->map(function ($test) use ($hasAccess) {
                return [
                    'id' => $test->id,
                    'chapter_id' => $test->chapter_id,
                    'title' => $test->title,
                    'total_marks' => $test->total_marks,
                    'pass_marks' => $test->pass_marks,
                    'duration_minutes' => $test->duration_minutes,
                    'is_locked' => ! $hasAccess,
                ];
            });

        return $this->successResponse($tests);
    }

    /**
     * Get test detail with shuffled questions (correct_option NOT sent).
     *
     * GET /api/tests/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $test = Test::where('is_active', true)->findOrFail($id);
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        if (! $hasAccess) {
            return $this->successResponse([
                'test' => $test->only(['id', 'title', 'total_marks', 'pass_marks', 'duration_minutes']),
                'questions' => [],
                'is_locked' => true,
                'subscription_required' => true,
            ], 'Subscription required to view questions and attempt this test.');
        }

        // Questions are hidden correct_option by default via model $hidden
        $questions = $test->questions()
            ->inRandomOrder()
            ->get(['id', 'test_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'marks', 'sort_order']);

        return $this->successResponse([
            'test' => $test->only(['id', 'title', 'total_marks', 'pass_marks', 'duration_minutes']),
            'questions' => $questions,
            'is_locked' => false,
            'has_access' => true,
        ]);
    }

    /**
     * Submit test answers and score server-side.
     *
     * POST /api/tests/{id}/attempt
     */
    public function attempt(TestAttemptRequest $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->hasAccess()) {
            return $this->errorResponse(
                'Trial expired. Please subscribe to attempt tests and earn rewards.',
                403,
                'TRIAL_EXPIRED',
                [],
                ['subscription_required' => true]
            );
        }

        $test = Test::where('is_active', true)->findOrFail($id);

        // Load questions with correct_option for scoring (bypasses model hidden)
        $questions = Question::where('test_id', $test->id)
            ->get(['id', 'correct_option', 'marks'])
            ->keyBy('id');

        $answers = $request->answers; // {question_id: "a"|"b"|"c"|"d"}
        $score = 0;
        $correctAnswers = [];

        foreach ($answers as $questionId => $selectedOption) {
            $question = $questions->get($questionId);
            if (! $question) {
                continue;
            }

            $isCorrect = $question->correct_option === $selectedOption;
            $correctAnswers[$questionId] = [
                'selected' => $selectedOption,
                'correct' => $question->correct_option,
                'is_correct' => $isCorrect,
            ];

            if ($isCorrect) {
                $score += $question->marks;
            }
        }

        $isPassed = $score >= $test->pass_marks;

        $attempt = TestAttempt::create([
            'test_id' => $test->id,
            'user_id' => $user->id,
            'score' => $score,
            'total_marks' => $test->total_marks,
            'is_passed' => $isPassed,
            'answers' => $answers,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $reward = null;

        if ($isPassed) {
            $chapter = $test->chapter;
            $this->progressService->markChapterComplete($user, $chapter);
            $reward = $this->progressService->awardReward($user, $chapter, 'test_pass', [
                'test_title' => $test->title,
                'score' => $score,
                'total' => $test->total_marks,
                'message' => 'Congratulations! You passed the test!',
            ]);
        }

        return $this->successResponse([
            'attempt_id' => $attempt->id,
            'score' => $score,
            'total_marks' => $test->total_marks,
            'is_passed' => $isPassed,
            'pass_marks' => $test->pass_marks,
            'correct_answers' => $correctAnswers,
            'rewards_earned' => $reward,
        ], $isPassed ? 'Test passed! Great job!' : 'Test submitted. Keep trying!');
    }
}
