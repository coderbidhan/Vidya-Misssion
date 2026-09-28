<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SetupProfileRequest;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Models\Chapter;
use App\Models\Progress;
use App\Models\Reward;
use App\Models\StudentProfile;
use App\Models\TestAttempt;
use App\Services\TrialService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TrialService $trialService) {}

    /**
     * Get the authenticated user's profile.
     *
     * GET /api/profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('studentProfile');

        return $this->successResponse([
            'user' => $user,
            'profile' => $user->studentProfile,
        ]);
    }

    /**
     * Complete the student profile and start the free trial.
     *
     * POST /api/profile/setup
     */
    public function setup(SetupProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->trial_starts_at !== null) {
            return $this->errorResponse('Profile has already been set up.', 409, 'ALREADY_SETUP');
        }

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'class' => $request->class,
            'school_name' => $request->school_name,
            'district' => $request->district,
        ]);

        // Update the user's name and start the trial
        $user->update(['name' => $request->name]);
        $this->trialService->startTrial($user);
        $user->refresh();

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'role', 'trial_starts_at', 'trial_ends_at']),
            'profile' => $profile,
            'trial_ends_at' => $user->trial_ends_at,
        ], 'Profile set up! Your 3-day free trial has started.');
    }

    /**
     * Update the authenticated student's profile.
     *
     * PUT /api/profile/update
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (! $profile) {
            $profile = StudentProfile::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'class' => $request->input('class', '9'),
                'school_name' => $request->input('school_name'),
                'district' => $request->input('district'),
            ]);
        }

        $updateData = $request->only(['name', 'class', 'school_name', 'district']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar_url'] = Storage::url($path);
        }

        $profile->update($updateData);

        if (isset($updateData['name'])) {
            $user->update(['name' => $updateData['name']]);
        }

        return $this->successResponse($profile->fresh(), 'Profile updated successfully.');
    }

    /**
     * Upload / Update avatar photo for the student.
     *
     * POST /api/profile/avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        $user = $request->user();
        $profile = $user->studentProfile;

        if (! $profile) {
            $profile = StudentProfile::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'class' => '9',
            ]);
        }

        if ($profile->avatar_url) {
            $oldPath = str_replace(Storage::url(''), '', $profile->avatar_url);
            $oldPath = ltrim($oldPath, '/');
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $profile->update(['avatar_url' => Storage::url($path)]);

        return $this->successResponse([
            'avatar_url' => Storage::url($path),
            'profile' => $profile->fresh(),
        ], 'Avatar uploaded successfully.');
    }

    /**
     * Get comprehensive Student Dashboard stats and summary.
     *
     * GET /api/student/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user()->load('studentProfile');
        $profile = $user->studentProfile;
        $studentClass = $profile?->class;

        // 1. Subscription Info
        $activeSubscription = $user->activeSubscription();

        $totalSpent = $user->userSubscriptions()
            ->where('payment_status', 'completed')
            ->join('subscriptions', 'user_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->sum('subscriptions.price');

        // 2. Exam Reports / Stats
        $attempts = TestAttempt::where('user_id', $user->id)
            ->with('test:id,title,total_marks,pass_marks,chapter_id')
            ->orderByDesc('completed_at')
            ->get();

        $totalExams = $attempts->count();
        $passedExams = $attempts->where('is_passed', true)->count();
        $failedExams = $totalExams - $passedExams;
        $averageScore = $totalExams > 0 ? round($attempts->avg(fn ($a) => $a->total_marks > 0 ? ($a->score / $a->total_marks) * 100 : 0), 1) : 0;
        $recentAttempts = $attempts->take(5)->map(fn ($a) => [
            'attempt_id' => $a->id,
            'test_title' => $a->test?->title ?? 'Test',
            'score' => $a->score,
            'total_marks' => $a->total_marks,
            'is_passed' => $a->is_passed,
            'percentage' => $a->total_marks > 0 ? round(($a->score / $a->total_marks) * 100, 1) : 0,
            'completed_at' => $a->completed_at?->toIso8601String(),
        ]);

        // 3. Progress & Attendance Counts
        $completedChaptersCount = Progress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        $videoWatchedCount = Reward::where('user_id', $user->id)
            ->where('reward_type', 'video_watch_complete')
            ->count();

        $liveClassAttendedCount = Reward::where('user_id', $user->id)
            ->where('reward_type', 'live_class_attendance')
            ->count();

        $totalRewards = Reward::where('user_id', $user->id)->count();

        // 4. Pending Tasks in Student's Class
        $totalChaptersInClass = $studentClass
            ? Chapter::where('is_active', true)->where('class_level', $studentClass)->count()
            : 0;
        $pendingChaptersCount = max(0, $totalChaptersInClass - $completedChaptersCount);

        return $this->successResponse([
            'student' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'class' => $profile?->class,
                'school_name' => $profile?->school_name,
                'district' => $profile?->district,
                'avatar_url' => $profile?->avatar_url,
                'trial_ends_at' => $user->trial_ends_at?->toIso8601String(),
                'is_trial_active' => $user->trial_ends_at && now()->lessThanOrEqualTo($user->trial_ends_at),
            ],
            'subscription' => [
                'has_active' => (bool) $activeSubscription,
                'plan_name' => $activeSubscription?->subscription?->name,
                'amount_paid' => $activeSubscription?->subscription?->price ?? 0,
                'total_spent' => (float) $totalSpent,
                'starts_at' => $activeSubscription?->starts_at?->toIso8601String(),
                'ends_at' => $activeSubscription?->ends_at?->toIso8601String(),
                'days_remaining' => $activeSubscription ? max(0, (int) now()->diffInDays($activeSubscription->ends_at, false)) : 0,
            ],
            'exam_stats' => [
                'total_attempted' => $totalExams,
                'passed_count' => $passedExams,
                'failed_count' => $failedExams,
                'average_score' => $averageScore,
                'recent_reports' => $recentAttempts,
            ],
            'learning_activity' => [
                'completed_chapters' => $completedChaptersCount,
                'videos_watched' => $videoWatchedCount,
                'live_classes_attended' => $liveClassAttendedCount,
                'pending_tasks_count' => $pendingChaptersCount,
                'rewards_earned_count' => $totalRewards,
            ],
        ]);
    }

    /**
     * List all test/exam attempt reports for the student.
     *
     * GET /api/student/exam-reports
     */
    public function examReports(Request $request): JsonResponse
    {
        $user = $request->user();

        $reports = TestAttempt::where('user_id', $user->id)
            ->with(['test:id,title,chapter_id,total_marks,pass_marks', 'test.chapter:id,title,subject_id', 'test.chapter.subject:id,name,color'])
            ->orderByDesc('completed_at')
            ->get()
            ->map(fn ($attempt) => [
                'id' => $attempt->id,
                'test_id' => $attempt->test_id,
                'test_title' => $attempt->test?->title,
                'chapter_name' => $attempt->test?->chapter?->title,
                'subject_name' => $attempt->test?->chapter?->subject?->name,
                'score' => $attempt->score,
                'total_marks' => $attempt->total_marks,
                'pass_marks' => $attempt->test?->pass_marks,
                'is_passed' => $attempt->is_passed,
                'percentage' => $attempt->total_marks > 0 ? round(($attempt->score / $attempt->total_marks) * 100, 1) : 0,
                'answers' => $attempt->answers,
                'started_at' => $attempt->started_at?->toIso8601String(),
                'completed_at' => $attempt->completed_at?->toIso8601String(),
            ]);

        return $this->successResponse($reports);
    }

    /**
     * Get recent learning history across videos, live classes, exams, notes.
     *
     * GET /api/student/history
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        // Recent test attempts
        $recentTests = TestAttempt::where('user_id', $user->id)
            ->with(['test:id,title,chapter_id', 'test.chapter:id,title,subject_id', 'test.chapter.subject:id,name'])
            ->orderByDesc('completed_at')
            ->take(10)
            ->get()
            ->map(fn ($a) => [
                'type' => 'exam',
                'title' => $a->test?->title,
                'chapter' => $a->test?->chapter?->title,
                'subject' => $a->test?->chapter?->subject?->name,
                'score' => $a->score.'/'.$a->total_marks,
                'is_passed' => $a->is_passed,
                'timestamp' => $a->completed_at?->toIso8601String(),
            ]);

        // Recent rewards / completions
        $recentRewards = Reward::where('user_id', $user->id)
            ->with(['chapter:id,title,subject_id', 'chapter.subject:id,name'])
            ->orderByDesc('earned_at')
            ->take(10)
            ->get()
            ->map(fn ($r) => [
                'type' => $r->reward_type,
                'title' => $r->reward_data['title'] ?? $r->reward_data['chapter_title'] ?? 'Completed Activity',
                'chapter' => $r->chapter?->title,
                'subject' => $r->chapter?->subject?->name,
                'points' => $r->reward_data['points'] ?? 0,
                'timestamp' => $r->earned_at?->toIso8601String(),
            ]);

        return $this->successResponse([
            'recent_exams' => $recentTests,
            'recent_activities' => $recentRewards,
        ]);
    }

    /**
     * Delete the authenticated student's profile, user account, and all associated data.
     *
     * DELETE /api/profile
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Delete user's avatar image if it exists
        $profile = $user->studentProfile;
        if ($profile && $profile->avatar_url) {
            // Extract the relative path from the storage URL
            // e.g. /storage/avatars/xxxx.png -> avatars/xxxx.png
            $relativePath = str_replace(Storage::url(''), '', $profile->avatar_url);
            $relativePath = ltrim($relativePath, '/');
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        // 2. Revoke and delete all API tokens
        $user->tokens()->delete();

        // 3. Delete user record (cascades database-level deletions)
        $user->delete();

        return $this->successResponse(null, 'Your account and all associated data have been permanently deleted.');
    }
}
