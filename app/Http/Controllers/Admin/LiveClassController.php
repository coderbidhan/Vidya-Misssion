<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelLiveClassRequest;
use App\Http\Requests\Admin\LiveClassRequest;
use App\Http\Requests\Admin\RescheduleLiveClassRequest;
use App\Http\Requests\Admin\UpdateLiveClassStatusRequest;
use App\Models\LiveClass;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    use ApiResponse;

    /**
     * Operational statistics for Live Class Dashboard.
     *
     * GET /api/admin/live-classes/dashboard-stats
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $now = now();

        $total = LiveClass::count();
        $scheduled = LiveClass::where('status', 'scheduled')->count();
        $live = LiveClass::where('status', 'live')->count();
        $completed = LiveClass::where('status', 'completed')->count();
        $cancelled = LiveClass::where('status', 'cancelled')->count();
        $upcoming = LiveClass::where('status', 'scheduled')->where('scheduled_at', '>=', $now)->count();
        $requiringAttention = LiveClass::requiringAttention()->count();

        // Grade breakdown for Classes 1–10
        $gradeBreakdown = LiveClass::selectRaw('class_level, count(*) as count')
            ->whereNotNull('class_level')
            ->whereBetween('class_level', [1, 10])
            ->groupBy('class_level')
            ->pluck('count', 'class_level');

        $grades = [];
        for ($i = 1; $i <= 10; $i++) {
            $grades[$i] = (int) ($gradeBreakdown[$i] ?? 0);
        }

        // Platform breakdown
        $platformBreakdown = LiveClass::selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        return $this->successResponse([
            'total_classes' => $total,
            'scheduled_classes' => $scheduled,
            'live_classes' => $live,
            'completed_classes' => $completed,
            'cancelled_classes' => $cancelled,
            'upcoming_classes' => $upcoming,
            'requiring_attention' => $requiringAttention,
            'grade_breakdown' => $grades,
            'platform_breakdown' => $platformBreakdown,
        ], 'Live class dashboard metrics retrieved.');
    }

    /**
     * List live classes with searching, filtering, and pagination.
     *
     * GET /api/admin/live-classes
     */
    public function index(Request $request): JsonResponse
    {
        $query = LiveClass::with([
            'chapter:id,title,subject_id,class_level',
            'subject:id,name,color,icon',
            'teacher:id,name,email',
            'creator:id,name',
        ]);

        if ($request->filled('class_level')) {
            $classLevel = $request->integer('class_level');
            $query->where(function ($q) use ($classLevel) {
                $q->where('class_level', $classLevel)
                    ->orWhereHas('chapter', fn ($ch) => $ch->where('class_level', $classLevel));
            });
        }

        if ($request->filled('subject_id')) {
            $subjectId = $request->integer('subject_id');
            $query->where(function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId)
                    ->orWhereHas('chapter', fn ($ch) => $ch->where('subject_id', $subjectId));
            });
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->integer('chapter_id'));
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->integer('teacher_id'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'attention') {
                $query->requiringAttention();
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->input('platform'));
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('learning_objectives', 'like', "%{$search}%")
                    ->orWhere('meeting_url', 'like', "%{$search}%")
                    ->orWhereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        $classes = $query->orderByDesc('scheduled_at')
            ->paginate($request->integer('per_page', 20));

        return $this->paginatedResponse($classes);
    }

    /**
     * Get details of a single live class.
     *
     * GET /api/admin/live-classes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $class = LiveClass::with([
            'chapter.subject',
            'subject',
            'teacher:id,name,email,mobile',
            'creator:id,name',
            'updater:id,name',
        ])->findOrFail($id);

        return $this->successResponse($class);
    }

    /**
     * Create a new live class.
     *
     * POST /api/admin/live-classes
     */
    public function store(LiveClassRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;

        $class = LiveClass::create($data);

        // Optionally send in-app notification to students
        if ($request->boolean('notify_students', true) && $class->is_published) {
            $this->notifyStudentsAboutClass($class, 'scheduled');
        }

        return $this->successResponse($class->fresh(['subject', 'chapter', 'teacher:id,name']), 'Live class scheduled successfully.', 201);
    }

    /**
     * Update an existing live class.
     *
     * PUT /api/admin/live-classes/{id}
     */
    public function update(LiveClassRequest $request, int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);
        $data = $request->validated();
        $data['updated_by'] = $request->user()?->id;

        $class->update($data);

        return $this->successResponse($class->fresh(['subject', 'chapter', 'teacher:id,name']), 'Live class updated successfully.');
    }

    /**
     * Update the status of a live class.
     *
     * PATCH /api/admin/live-classes/{id}/status
     */
    public function updateStatus(UpdateLiveClassStatusRequest $request, int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);
        $newStatus = $request->status;

        $updateData = [
            'status' => $newStatus,
            'updated_by' => $request->user()?->id,
        ];

        if ($request->filled('recording_url')) {
            $updateData['recording_url'] = $request->recording_url;
        }

        if ($request->filled('cancellation_reason')) {
            $updateData['cancellation_reason'] = $request->cancellation_reason;
        }

        if ($request->has('is_published')) {
            $updateData['is_published'] = $request->boolean('is_published');
        }

        $class->update($updateData);

        // Notify students if marked live
        if ($newStatus === 'live' && $class->is_published) {
            $this->notifyStudentsAboutClass($class, 'live');
        }

        return $this->successResponse($class->fresh(), "Live class marked as {$newStatus}.");
    }

    /**
     * Reschedule a live class.
     *
     * PATCH /api/admin/live-classes/{id}/reschedule
     */
    public function reschedule(RescheduleLiveClassRequest $request, int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);

        $updateData = [
            'scheduled_at' => $request->scheduled_at,
            'status' => 'scheduled',
            'updated_by' => $request->user()?->id,
        ];

        if ($request->filled('duration_minutes')) {
            $updateData['duration_minutes'] = $request->integer('duration_minutes');
        }

        $class->update($updateData);

        if ($request->boolean('notify_students', true) && $class->is_published) {
            $this->notifyStudentsAboutClass($class, 'rescheduled');
        }

        return $this->successResponse($class->fresh(), 'Live class rescheduled successfully.');
    }

    /**
     * Cancel a live class with reason.
     *
     * PATCH /api/admin/live-classes/{id}/cancel
     */
    public function cancel(CancelLiveClassRequest $request, int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);

        $class->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason,
            'updated_by' => $request->user()?->id,
        ]);

        if ($request->boolean('notify_students', true) && $class->is_published) {
            $this->notifyStudentsAboutClass($class, 'cancelled');
        }

        return $this->successResponse($class->fresh(), 'Live class cancelled successfully.');
    }

    /**
     * Delete a live class.
     *
     * DELETE /api/admin/live-classes/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);
        $class->delete();

        return $this->successResponse(null, 'Live class deleted successfully.');
    }

    /**
     * Helper to send in-app notifications to eligible students.
     */
    private function notifyStudentsAboutClass(LiveClass $class, string $action): void
    {
        $title = match ($action) {
            'scheduled' => "New Live Class: {$class->title}",
            'rescheduled' => "Rescheduled: {$class->title}",
            'cancelled' => "Cancelled: {$class->title}",
            'live' => "LIVE NOW: {$class->title}",
            default => "Update: {$class->title}",
        };

        $body = match ($action) {
            'scheduled' => "A new live class is scheduled for {$class->scheduled_at->format('d M Y, h:i A')}.",
            'rescheduled' => "This class has been moved to {$class->scheduled_at->format('d M Y, h:i A')}.",
            'cancelled' => 'Class cancelled. Reason: '.($class->cancellation_reason ?? 'Administrative rescheduling.'),
            'live' => 'The live session has started! Join now.',
            default => 'Class schedule update.',
        };

        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->when($class->class_level, function ($q) use ($class) {
                $q->whereHas('studentProfile', function ($sp) use ($class) {
                    $sp->where('class', 'like', "%{$class->class_level}%");
                });
            })
            ->pluck('id');

        $notifications = $students->map(fn ($studentId) => [
            'user_id' => $studentId,
            'title' => $title,
            'body' => $body,
            'type' => "live_class_{$action}",
            'data' => json_encode([
                'live_class_id' => $class->id,
                'class_level' => $class->class_level,
                'scheduled_at' => $class->scheduled_at->toIso8601String(),
                'platform' => $class->platform,
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        collect($notifications)->chunk(500)->each(fn ($chunk) => Notification::insert($chunk->toArray()));
    }
}
