<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicClassController extends Controller
{
    use ApiResponse;

    /**
     * List all academic classes (Classes 1 through 10) with curriculum summary metadata.
     *
     * GET /api/classes
     */
    public function index(Request $request): JsonResponse
    {
        $maxClass = 10;
        $classes = [];

        // Pre-fetch counts to prevent N+1 queries
        $subjectCounts = ClassSubject::selectRaw('class_level, count(*) as count')
            ->whereHas('subject', fn ($q) => $q->where('is_active', true))
            ->groupBy('class_level')
            ->pluck('count', 'class_level');

        $chapterCounts = Chapter::selectRaw('class_level, count(*) as count')
            ->where('is_active', true)
            ->whereNotNull('class_level')
            ->groupBy('class_level')
            ->pluck('count', 'class_level');

        for ($level = 1; $level <= $maxClass; $level++) {
            $classes[] = [
                'class_level' => $level,
                'name' => "Class {$level}",
                'display_name' => "Class {$level} (Standard {$level})",
                'board' => 'WBBSE',
                'curriculum' => 'West Bengal Board (WBBSE)',
                'stage' => match (true) {
                    $level <= 4 => 'Primary',
                    $level <= 8 => 'Upper Primary',
                    default => 'Secondary (Madhyamik)',
                },
                'subjects_count' => (int) ($subjectCounts[$level] ?? 0),
                'chapters_count' => (int) ($chapterCounts[$level] ?? 0),
                'is_active' => true,
            ];
        }

        return $this->successResponse($classes, 'Academic classes retrieved successfully.');
    }

    /**
     * List all active subjects mapped to a specific class level.
     *
     * GET /api/classes/{classLevel}/subjects
     */
    public function subjects(Request $request, int $classLevel): JsonResponse
    {
        if ($classLevel < 1 || $classLevel > 12) {
            return $this->errorResponse('Invalid class level. Must be between 1 and 12.', 422, 'INVALID_CLASS_LEVEL');
        }

        $subjectIds = ClassSubject::where('class_level', $classLevel)->pluck('subject_id');

        $subjects = Subject::where('is_active', true)
            ->whereIn('id', $subjectIds)
            ->withCount(['chapters' => fn ($q) => $q->where('class_level', $classLevel)->where('is_active', true)])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'color', 'icon', 'sort_order'])
            ->map(fn ($subject) => [
                'id' => $subject->id,
                'name' => $subject->name,
                'color' => $subject->color,
                'icon' => $subject->icon,
                'sort_order' => $subject->sort_order,
                'class_level' => $classLevel,
                'chapters_count' => $subject->chapters_count,
            ]);

        return $this->successResponse([
            'class_level' => $classLevel,
            'class_name' => "Class {$classLevel}",
            'subjects' => $subjects,
        ], "Subjects for Class {$classLevel} retrieved successfully.");
    }
}
