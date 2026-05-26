<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/admin/class-subjects?class_level=9
     * Returns all subjects assigned to a given class level,
     * plus a list of ALL subjects so the UI can show an "assign" dropdown.
     */
    public function index(Request $request): JsonResponse
    {
        $classLevel = $request->integer('class_level');

        $assigned = ClassSubject::with('subject')
            ->where('class_level', $classLevel)
            ->get()
            ->map(fn ($cs) => [
                'id'         => $cs->subject->id,
                'name'       => $cs->subject->name,
                'color'      => $cs->subject->color,
                'icon'       => $cs->subject->icon,
                'sort_order' => $cs->subject->sort_order,
            ]);

        $all = Subject::orderBy('sort_order')->get(['id', 'name', 'color', 'icon']);

        return $this->successResponse([
            'class_level'      => $classLevel,
            'assigned_subjects' => $assigned,
            'all_subjects'     => $all,
        ]);
    }

    /**
     * GET /api/admin/class-subjects/all
     * Returns all class-subject mappings (useful for cascading dropdowns).
     */
    public function allMappings(): JsonResponse
    {
        $mappings = ClassSubject::with('subject')->get()->map(fn ($cs) => [
            'class_level' => $cs->class_level,
            'subject_id'  => $cs->subject_id,
            'subject_name'=> $cs->subject->name,
        ]);

        return $this->successResponse($mappings);
    }

    /**
     * POST /api/admin/class-subjects
     * Assign a subject to a class level.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_level' => ['required', 'integer', 'min:1', 'max:12'],
            'subject_id'  => ['required', 'integer', 'exists:subjects,id'],
        ]);

        ClassSubject::firstOrCreate($data);

        return $this->successResponse(null, "Subject assigned to Class {$data['class_level']}.", 201);
    }

    /**
     * DELETE /api/admin/class-subjects/{class_level}/{subject_id}
     * Remove a subject from a class level.
     */
    public function destroy(int $classLevel, int $subjectId): JsonResponse
    {
        ClassSubject::where('class_level', $classLevel)
            ->where('subject_id', $subjectId)
            ->delete();

        return $this->successResponse(null, "Subject removed from Class {$classLevel}.");
    }

    /**
     * GET /api/admin/class-subjects/{class_level}/chapters?subject_id=X
     * Returns chapters for a specific class + subject combination.
     */
    public function chapters(Request $request, int $classLevel): JsonResponse
    {
        $subjectId = $request->integer('subject_id');

        $chapters = \App\Models\Chapter::where('subject_id', $subjectId)
            ->where(fn ($q) =>
                $q->where('class_level', $classLevel)
                  ->orWhereNull('class_level')
            )
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse($chapters);
    }
}
