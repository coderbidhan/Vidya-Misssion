<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use ApiResponse;

    /**
     * List subjects for the authenticated student's class level.
     * If the student has a class set in their profile, only subjects
     * assigned to that class (via class_subjects) are returned.
     * Falls back to all subjects if no class is set.
     *
     * GET /api/subjects
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $studentClass = $user->studentProfile?->class;

        if ($studentClass) {
            // Return only subjects mapped to this student's class
            $subjectIds = ClassSubject::where('class_level', $studentClass)
                ->pluck('subject_id');

            $subjects = Subject::where('is_active', true)
                ->whereIn('id', $subjectIds)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'color', 'icon', 'sort_order']);
        } else {
            // No class set yet — return all subjects
            $subjects = Subject::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'color', 'icon', 'sort_order']);
        }

        return $this->successResponse($subjects);
    }
}
