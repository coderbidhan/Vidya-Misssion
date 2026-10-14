<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSubjectsRequest;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\CreateTeacherRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\DeviceSessionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * List all students (paginated, searchable).
     *
     * GET /api/admin/students
     */
    public function students(Request $request): JsonResponse
    {
        $query = User::where('role', 'student')
            ->with(['studentProfile', 'userSubscriptions' => function ($q) {
                $q->where('payment_status', 'completed')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->with('subscription');
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('mobile', 'like', '%'.$search.'%')
                        ->orWhereHas('studentProfile', function ($sp) use ($search) {
                            $sp->where('name', 'like', '%'.$search.'%')
                                ->orWhere('school_name', 'like', '%'.$search.'%')
                                ->orWhere('district', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->status;
                if ($status === 'active') {
                    $q->whereNotNull('trial_ends_at')->where('trial_ends_at', '>=', now());
                } elseif ($status === 'expired') {
                    $q->whereNotNull('trial_ends_at')->where('trial_ends_at', '<', now());
                } elseif ($status === 'subscribed') {
                    $q->whereHas('userSubscriptions', function ($sq) {
                        $sq->where('payment_status', 'completed')
                            ->where('starts_at', '<=', now())
                            ->where('ends_at', '>=', now());
                    });
                }
            })
            ->orderByDesc('created_at');

        $paginator = $query->paginate(20);
        $paginator->getCollection()->transform(function ($student) {
            $student->account_password = $student->plain_password;
            return $student;
        });

        return $this->paginatedResponse($paginator);
    }

    /**
     * Create a new student from the admin panel.
     *
     * POST /api/admin/students
     */
    public function createStudent(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'class' => ['nullable', 'integer', 'min:1', 'max:12'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
        ]);

        $student = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'plain_password' => $data['password'],
                'role' => 'student',
                'is_verified' => true,
                'is_active' => true,
            ]);

            \App\Models\StudentProfile::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'class' => $data['class'] ?? null,
                'school_name' => $data['school_name'] ?? null,
                'district' => $data['district'] ?? null,
            ]);

            app(\App\Services\TrialService::class)->startTrial($user);

            return $user->load('studentProfile');
        });

        $student->account_password = $data['password'];

        return $this->successResponse($student, 'Student account created successfully.', 201);
    }

    /**
     * Update a student account (e.g. status or details).
     *
     * PUT /api/admin/students/{id}
     */
    public function updateStudent(Request $request, int $id): JsonResponse
    {
        $student = User::where('role', 'student')->findOrFail($id);

        if ($request->has('is_active')) {
            $student->update(['is_active' => $request->boolean('is_active')]);
        }

        if ($request->filled('password')) {
            $student->update([
                'password' => Hash::make($request->password),
                'plain_password' => $request->password,
            ]);
            $student->account_password = $request->password;
        }

        return $this->successResponse($student, 'Student updated successfully.');
    }

    /**
     * Set or reset a student password from the admin panel.
     *
     * POST /api/admin/students/{id}/password
     */
    public function updateStudentPassword(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $student = User::where('role', 'student')->findOrFail($id);
        $student->update([
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
        ]);

        return $this->successResponse([
            'id' => $student->id,
            'account_password' => $request->password,
        ], 'Student password updated successfully.');
    }

    /**
     * Grant free access to a student (complimentary subscription or extended trial).
     *
     * POST /api/admin/students/{id}/grant-access
     */
    public function grantFreeAccess(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'access_type' => 'required|in:subscription,trial',
            'duration_days' => 'required|integer|min:1|max:3650',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $student = User::where('role', 'student')->findOrFail($id);
        $durationDays = (int) $request->duration_days;
        $reason = $request->input('reason', 'Complimentary access granted by admin');
        $adminId = $request->user()?->id;

        if ($request->access_type === 'trial') {
            $now = now();
            $baseDate = ($student->trial_ends_at && $student->trial_ends_at->isFuture())
                ? $student->trial_ends_at
                : $now;

            $student->update([
                'trial_starts_at' => $student->trial_starts_at ?: $now,
                'trial_ends_at' => (clone $baseDate)->addDays($durationDays),
            ]);

            return $this->successResponse([
                'student' => $student->fresh(),
                'type' => 'trial',
                'trial_ends_at' => $student->trial_ends_at->toIso8601String(),
            ], "Granted {$durationDays} days free trial access to {$student->name}.");
        }

        // access_type === 'subscription'
        $subscription = $request->subscription_id
            ? Subscription::findOrFail($request->subscription_id)
            : Subscription::where('is_active', true)->orderByDesc('duration_days')->first();

        if (! $subscription) {
            return $this->errorResponse('No active subscription plan found to assign.', 422);
        }

        $latestActive = $student->userSubscriptions()
            ->where('payment_status', 'completed')
            ->where('ends_at', '>=', now())
            ->orderByDesc('ends_at')
            ->first();

        $startsAt = $latestActive ? $latestActive->ends_at : now();
        $endsAt = (clone $startsAt)->addDays($durationDays);

        $userSubscription = DB::transaction(function () use ($student, $subscription, $durationDays, $startsAt, $endsAt, $reason, $adminId) {
            $userSub = UserSubscription::create([
                'user_id' => $student->id,
                'subscription_id' => $subscription->id,
                'gateway_order_id' => null,
                'payment_id' => 'admin_free_'.uniqid(),
                'payment_gateway' => 'admin_grant',
                'payment_status' => 'completed',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            $payment = Payment::create([
                'user_id' => $student->id,
                'payable_type' => UserSubscription::class,
                'payable_id' => $userSub->id,
                'purpose' => 'subscription_purchase',
                'gateway' => 'admin_grant',
                'gateway_payment_id' => 'admin_grant_'.uniqid(),
                'amount' => 0,
                'currency' => 'INR',
                'status' => 'captured',
                'gateway_status' => 'captured',
                'payment_method' => 'admin_complimentary',
                'receipt' => 'adm_'.strtoupper(Str::random(8)),
                'is_captured' => true,
                'captured_at' => now(),
                'metadata' => [
                    'reason' => $reason,
                    'granted_by' => $adminId,
                    'plan_name' => $subscription->name,
                    'duration_days' => $durationDays,
                ],
            ]);

            $userSub->update(['payment_record_id' => $payment->id]);

            return $userSub;
        });

        return $this->successResponse([
            'user_subscription' => $userSubscription->load('subscription'),
            'type' => 'subscription',
            'ends_at' => $endsAt->toIso8601String(),
        ], "Granted {$durationDays} days complimentary '{$subscription->name}' access to {$student->name}.");
    }

    /**
     * List all teachers (paginated).
     *
     * GET /api/admin/teachers
     */
    public function teachers(Request $request): JsonResponse
    {
        $teachers = User::where('role', 'teacher')
            ->with(['teacherProfile', 'subjects' => function ($query) {
                $query->select('subjects.id', 'subjects.name', 'teacher_subjects.class_level');
            }])
            ->when($request->filled('class_level'), function ($q) use ($request) {
                $q->whereHas('subjects', function ($q2) use ($request) {
                    $q2->where('teacher_subjects.class_level', $request->class_level);
                    if ($request->filled('subject_id')) {
                        $q2->where('teacher_subjects.subject_id', $request->subject_id);
                    }
                });
            })
            ->paginate(20);

        return $this->paginatedResponse($teachers);
    }

    /**
     * Create a new teacher account.
     *
     * POST /api/admin/teachers
     */
    public function createTeacher(CreateTeacherRequest $request): JsonResponse
    {
        $teacher = User::create([
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
            'is_verified' => true,
            'is_active' => true,
        ]);

        TeacherProfile::create([
            'user_id' => $teacher->id,
            'bio' => strip_tags($request->bio ?? ''),
            'qualification' => strip_tags($request->qualification ?? ''),
        ]);

        return $this->successResponse($teacher->load('teacherProfile'), 'Teacher account created.', 201);
    }

    /**
     * Update a teacher account.
     *
     * PUT /api/admin/teachers/{id}
     */
    public function updateTeacher(UpdateTeacherRequest $request, int $id): JsonResponse
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $data = $request->only(['name', 'email', 'mobile', 'is_active']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        if ($request->has('bio') || $request->has('qualification')) {
            $teacher->teacherProfile()->updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'bio' => strip_tags($request->bio ?? $teacher->teacherProfile?->bio ?? ''),
                    'qualification' => strip_tags($request->qualification ?? $teacher->teacherProfile?->qualification ?? ''),
                ]
            );
        }

        return $this->successResponse($teacher->fresh()->load('teacherProfile'), 'Teacher updated.');
    }

    /**
     * Deactivate (soft-delete) a teacher.
     *
     * DELETE /api/admin/teachers/{id}
     */
    public function deleteTeacher(int $id): JsonResponse
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->update(['is_active' => false]);

        return $this->successResponse(null, 'Teacher deactivated.');
    }

    /**
     * Assign (sync) subjects to a teacher.
     *
     * POST /api/admin/teachers/{id}/assign-subjects
     */
    public function assignSubjects(AssignSubjectsRequest $request, int $id): JsonResponse
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        // Convert [{class_level, subject_id}] to sync array format for Eloquent
        $syncData = [];
        foreach ($request->assignments as $assignment) {
            // Eloquent sync needs an array of properties if there are duplicates,
            // but since we can't easily sync duplicates with the same subject_id using sync()
            // we have to delete and insert manually or use detach+attach.
            $syncData[] = [
                'subject_id' => $assignment['subject_id'],
                'class_level' => $assignment['class_level'],
            ];
        }

        $teacher->subjects()->detach();
        foreach ($syncData as $data) {
            $teacher->subjects()->attach($data['subject_id'], ['class_level' => $data['class_level']]);
        }

        return $this->successResponse(
            $teacher->subjects()->get(['subjects.id', 'subjects.name', 'teacher_subjects.class_level']),
            'Subjects assigned.'
        );
    }

    /**
     * List all admins.
     */
    public function admins(Request $request): JsonResponse
    {
        $admins = User::where('role', 'admin')->paginate(20);

        return $this->paginatedResponse($admins);
    }

    /**
     * Create a new admin account.
     */
    public function createAdmin(CreateAdminRequest $request): JsonResponse
    {
        $admin = User::create([
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);

        return $this->successResponse($admin, 'Admin account created.', 201);
    }

    /**
     * Update an admin account.
     */
    public function updateAdmin(UpdateAdminRequest $request, int $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $data = $request->only(['name', 'email', 'is_active']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return $this->successResponse($admin->fresh(), 'Admin updated.');
    }

    /**
     * Deactivate an admin account.
     */
    public function deleteAdmin(int $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        // Prevent deleting the last active admin
        if (User::where('role', 'admin')->where('is_active', true)->count() <= 1 && $admin->is_active) {
            return $this->errorResponse('Cannot deactivate the last active admin.', 400);
        }

        $admin->update(['is_active' => false]);

        return $this->successResponse(null, 'Admin deactivated.');
    }

    /**
     * Revoke a user's active device session (admin action).
     *
     * POST /api/admin/users/{id}/revoke-session
     */
    public function revokeSession(int $id, DeviceSessionService $deviceSessionService): JsonResponse
    {
        $user = User::findOrFail($id);
        $deviceSessionService->revokeAllSessions($user);

        return $this->successResponse(null, "Active session for user {$user->name} revoked.");
    }
}
