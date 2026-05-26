<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSubjectsRequest;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\CreateTeacherRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            ->with('studentProfile')
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'));

        return $this->paginatedResponse($query->paginate(20));
    }

    /**
     * Update a student account (e.g. status).
     *
     * PUT /api/admin/students/{id}
     */
    public function updateStudent(Request $request, int $id): JsonResponse
    {
        $student = User::where('role', 'student')->findOrFail($id);
        
        if ($request->has('is_active')) {
            $student->update(['is_active' => $request->boolean('is_active')]);
        }

        return $this->successResponse($student, 'Student updated.');
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
            'name'        => strip_tags($request->name),
            'email'       => $request->email,
            'mobile'      => $request->mobile,
            'password'    => Hash::make($request->password),
            'role'        => 'teacher',
            'is_verified' => true,
            'is_active'   => true,
        ]);

        TeacherProfile::create([
            'user_id'       => $teacher->id,
            'bio'           => strip_tags($request->bio ?? ''),
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
                    'bio'           => strip_tags($request->bio ?? $teacher->teacherProfile?->bio ?? ''),
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
                'subject_id'  => $assignment['subject_id'],
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
            'name'        => strip_tags($request->name),
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'admin',
            'is_verified' => true,
            'is_active'   => true,
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
}
