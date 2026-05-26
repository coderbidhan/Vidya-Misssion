<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Subject;
use App\Models\Test;
use App\Models\User;
use App\Models\UserSubscription;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    use ApiResponse;

    /**
     * Platform overview stats.
     *
     * GET /api/admin/analytics/overview
     */
    public function overview(): JsonResponse
    {
        $totalStudents      = User::where('role', 'student')->count();
        $totalTeachers      = User::where('role', 'teacher')->count();
        $trialUsers         = User::where('role', 'student')
            ->whereNotNull('trial_starts_at')
            ->where('trial_ends_at', '>=', now())
            ->count();
        $activeSubscriptions = UserSubscription::where('payment_status', 'completed')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->count();
        $totalRevenue       = UserSubscription::where('payment_status', 'completed')->sum(
            \DB::raw('(SELECT price FROM subscriptions WHERE subscriptions.id = user_subscriptions.subscription_id)')
        );

        return $this->successResponse([
            'total_students'        => $totalStudents,
            'total_teachers'        => $totalTeachers,
            'trial_users'           => $trialUsers,
            'active_subscriptions'  => $activeSubscriptions,
            'total_revenue'         => round((float) $totalRevenue, 2),
        ]);
    }

    /**
     * Most popular subjects by chapter completion count.
     *
     * GET /api/admin/analytics/popular-subjects
     */
    public function popularSubjects(): JsonResponse
    {
        $subjects = Subject::withCount(['chapters as completion_count' => function ($q) {
            $q->whereHas('progress', fn ($p) => $p->where('is_completed', true));
        }])->orderByDesc('completion_count')->get(['id', 'name', 'color']);

        return $this->successResponse($subjects);
    }

    /**
     * Test performance stats.
     *
     * GET /api/admin/analytics/test-performance
     */
    public function testPerformance(): JsonResponse
    {
        $tests = Test::withCount([
            'attempts',
            'attempts as passed_count' => fn ($q) => $q->where('is_passed', true),
        ])->with('chapter:id,title,subject_id')
        ->paginate(20);

        $tests->getCollection()->transform(fn ($test) => [
            'id'          => $test->id,
            'title'       => $test->title,
            'chapter'     => $test->chapter,
            'attempts'    => $test->attempts_count,
            'passed'      => $test->passed_count,
            'pass_rate'   => $test->attempts_count > 0
                ? round(($test->passed_count / $test->attempts_count) * 100, 1) . '%'
                : '0%',
        ]);

        return $this->paginatedResponse($tests);
    }
}
