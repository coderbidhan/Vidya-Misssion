<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\StudentProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicClassController extends Controller
{
    use ApiResponse;

    /**
     * List all academic classes with curriculum and student metrics.
     *
     * GET /api/admin/classes
     */
    public function index(Request $request): JsonResponse
    {
        // Pre-aggregate counts for high performance
        $subjectCounts = ClassSubject::selectRaw('class_level, count(*) as count')
            ->groupBy('class_level')
            ->pluck('count', 'class_level');

        $chapterCounts = Chapter::selectRaw('class_level, count(*) as count')
            ->whereNotNull('class_level')
            ->groupBy('class_level')
            ->pluck('count', 'class_level');

        $studentCounts = StudentProfile::selectRaw('class, count(*) as count')
            ->whereNotNull('class')
            ->groupBy('class')
            ->pluck('count', 'class');

        $liveClassCounts = LiveClass::selectRaw('class_level, count(*) as count')
            ->whereNotNull('class_level')
            ->groupBy('class_level')
            ->pluck('count', 'class_level');

        $query = AcademicClass::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('board', 'like', "%{$search}%")
                    ->orWhere('stage', 'like', "%{$search}%")
                    ->orWhere('class_level', $search);
            });
        }

        if ($request->filled('board')) {
            $query->where('board', $request->board);
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $classes = $query->ordered()->get()->map(function (AcademicClass $c) use ($subjectCounts, $chapterCounts, $studentCounts, $liveClassCounts) {
            $level = $c->class_level;

            return [
                'id' => $c->id,
                'class_level' => $c->class_level,
                'name' => $c->name,
                'display_name' => $c->display_name ?: $c->name,
                'board' => $c->board,
                'curriculum' => $c->curriculum,
                'stage' => $c->stage,
                'stream' => $c->stream,
                'icon' => $c->icon ?: '🏫',
                'description' => $c->description,
                'sort_order' => $c->sort_order,
                'is_active' => (bool) $c->is_active,
                'subjects_count' => (int) ($subjectCounts[$level] ?? 0),
                'chapters_count' => (int) ($chapterCounts[$level] ?? 0),
                'students_count' => (int) ($studentCounts[$level] ?? 0),
                'live_classes_count' => (int) ($liveClassCounts[$level] ?? 0),
                'created_at' => $c->created_at?->toIso8601String(),
                'updated_at' => $c->updated_at?->toIso8601String(),
            ];
        });

        return $this->successResponse($classes, 'Classes retrieved successfully.');
    }

    /**
     * Create a new academic class.
     *
     * POST /api/admin/classes
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_level' => ['required', 'integer', 'min:1', 'max:50', 'unique:academic_classes,class_level'],
            'name' => ['required', 'string', 'max:100'],
            'display_name' => ['nullable', 'string', 'max:150'],
            'board' => ['nullable', 'string', 'max:100'],
            'curriculum' => ['nullable', 'string', 'max:150'],
            'stage' => ['nullable', 'string', 'max:100'],
            'stream' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $defaultDisplayName = $validated['display_name'] ?? $validated['name'];
        $defaultBoard = $validated['board'] ?? 'WBBSE';
        $defaultCurriculum = $validated['curriculum'] ?? ("West Bengal Board ({$defaultBoard})");
        $defaultStage = $validated['stage'] ?? match (true) {
            $validated['class_level'] <= 4 => 'Primary',
            $validated['class_level'] <= 8 => 'Upper Primary',
            $validated['class_level'] <= 10 => 'Secondary (Madhyamik)',
            default => 'Higher Secondary',
        };

        $class = AcademicClass::create([
            'class_level' => $validated['class_level'],
            'name' => $validated['name'],
            'display_name' => $defaultDisplayName,
            'board' => $defaultBoard,
            'curriculum' => $defaultCurriculum,
            'stage' => $defaultStage,
            'stream' => $validated['stream'] ?? ($validated['class_level'] >= 11 ? 'Science / Arts / Commerce' : 'General'),
            'icon' => $validated['icon'] ?? '🏫',
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $validated['class_level'],
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return $this->successResponse($class, "{$class->name} created successfully.", 201);
    }

    /**
     * Show a specific academic class with its mapped subjects.
     *
     * GET /api/admin/classes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $class = AcademicClass::findOrFail($id);

        $mappedSubjects = ClassSubject::with('subject')
            ->where('class_level', $class->class_level)
            ->get()
            ->map(fn ($cs) => [
                'id' => $cs->subject?->id,
                'name' => $cs->subject?->name,
                'color' => $cs->subject?->color,
                'icon' => $cs->subject?->icon,
                'sort_order' => $cs->subject?->sort_order,
                'is_active' => (bool) $cs->subject?->is_active,
            ]);

        $data = $class->toArray();
        $data['assigned_subjects'] = $mappedSubjects;
        $data['subjects_count'] = $mappedSubjects->count();
        $data['chapters_count'] = Chapter::where('class_level', $class->class_level)->count();
        $data['students_count'] = StudentProfile::where('class', $class->class_level)->count();

        return $this->successResponse($data);
    }

    /**
     * Update an academic class.
     *
     * PUT /api/admin/classes/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $class = AcademicClass::findOrFail($id);

        $validated = $request->validate([
            'class_level' => ['sometimes', 'integer', 'min:1', 'max:50', Rule::unique('academic_classes', 'class_level')->ignore($class->id)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'display_name' => ['nullable', 'string', 'max:150'],
            'board' => ['nullable', 'string', 'max:100'],
            'curriculum' => ['nullable', 'string', 'max:150'],
            'stage' => ['nullable', 'string', 'max:100'],
            'stream' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $oldLevel = $class->class_level;
        $class->update($validated);

        // If class_level changed, cascade update to mapped tables
        if (isset($validated['class_level']) && $validated['class_level'] !== $oldLevel) {
            $newLevel = $validated['class_level'];
            ClassSubject::where('class_level', $oldLevel)->update(['class_level' => $newLevel]);
            Chapter::where('class_level', $oldLevel)->update(['class_level' => $newLevel]);
            StudentProfile::where('class', $oldLevel)->update(['class' => $newLevel]);
            LiveClass::where('class_level', $oldLevel)->update(['class_level' => $newLevel]);
        }

        return $this->successResponse($class, "{$class->name} updated successfully.");
    }

    /**
     * Quick toggle active status for a class.
     *
     * POST /api/admin/classes/{id}/toggle-status
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $class = AcademicClass::findOrFail($id);
        $class->is_active = ! $class->is_active;
        $class->save();

        $statusText = $class->is_active ? 'activated' : 'deactivated';

        return $this->successResponse($class, "{$class->name} {$statusText}.");
    }

    /**
     * Delete an academic class.
     *
     * DELETE /api/admin/classes/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $class = AcademicClass::findOrFail($id);
        $level = $class->class_level;

        $subjectCount = ClassSubject::where('class_level', $level)->count();
        $chapterCount = Chapter::where('class_level', $level)->count();
        $studentCount = StudentProfile::where('class', $level)->count();

        // If data is linked and force not specified, warn the admin
        if (! $request->boolean('force') && ($subjectCount > 0 || $chapterCount > 0 || $studentCount > 0)) {
            return $this->errorResponse(
                "Cannot delete {$class->name} directly because it has {$subjectCount} subject(s), {$chapterCount} chapter(s), and {$studentCount} student(s) linked to it. Deactivate the class instead, or confirm force delete.",
                409,
                'CLASS_HAS_LINKED_DATA',
                [],
                [
                    'subjects_count' => $subjectCount,
                    'chapters_count' => $chapterCount,
                    'students_count' => $studentCount,
                ]
            );
        }

        if ($request->boolean('force')) {
            ClassSubject::where('class_level', $level)->delete();
        }

        $className = $class->name;
        $class->delete();

        return $this->successResponse(null, "{$className} deleted successfully.");
    }
}
