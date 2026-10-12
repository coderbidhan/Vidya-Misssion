<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\RecordedClass;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Teacher dashboard stats.
     *
     * GET /api/teacher/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $stats = [
            'recorded_classes' => RecordedClass::where('teacher_id', $teacher->id)->count(),
            'live_classes' => LiveClass::where('teacher_id', $teacher->id)->count(),
            'upcoming_classes' => LiveClass::where('teacher_id', $teacher->id)->where('status', 'scheduled')->count(),
            'notes_uploaded' => Note::where('teacher_id', $teacher->id)->count(),
            'subjects_assigned' => $teacher->subjects()->count(),
        ];

        return $this->successResponse($stats);
    }

    /**
     * Get subjects assigned to this teacher.
     *
     * GET /api/teacher/subjects
     */
    public function subjects(Request $request): JsonResponse
    {
        $subjects = $request->user()->subjects()
            ->where('is_active', true)
            ->with(['chapters' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->get();

        return $this->successResponse($subjects);
    }
}
