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
        $hasActiveSubscription = $user->hasActiveSubscription();

        // Determine target class level:
        // 1. If explicit class_level is provided (e.g. ?class_level=10), use it.
        // 2. If class_level is 'all' or '0', return all subjects without class filter.
        // 3. Otherwise default to the student's enrolled profile class if present.
        if ($request->filled('class_level')) {
            $classParam = strtolower((string) $request->class_level);
            $targetClass = in_array($classParam, ['all', '0', 'null', 'none']) ? null : (int) $request->class_level;
        } else {
            $targetClass = $studentClass ? (int) $studentClass : null;
        }

        if ($targetClass) {
            // Return only subjects mapped to this class
            $subjectIds = ClassSubject::where('class_level', $targetClass)
                ->pluck('subject_id');

            $subjects = Subject::where('is_active', true)
                ->whereIn('id', $subjectIds)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'color', 'icon', 'image', 'sort_order']);
        } else {
            // Return all active subjects
            $subjects = Subject::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'color', 'icon', 'image', 'sort_order']);
        }

        return $this->successResponse([
            'class_level' => $targetClass,
            'is_trial_mode' => ! $hasActiveSubscription,
            'is_subscription_active' => $hasActiveSubscription,
            'subjects' => $subjects,
        ]);
    }
}
