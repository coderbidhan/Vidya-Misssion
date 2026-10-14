<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\LiveClass;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    use ApiResponse;

    /**
     * List live classes for a chapter (upcoming, live, and past).
     * Preserves exact existing response structure for 100% backward compatibility.
     *
     * GET /api/chapters/{id}/live-classes
     */
    public function byChapter(Request $request, int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $classes = $chapter->liveClasses()
            ->where('is_published', true)
            ->with(['teacher:id,name'])
            ->orderBy('scheduled_at', 'desc')
            ->get([
                'id',
                'chapter_id',
                'teacher_id',
                'title',
                'description',
                'learning_objectives',
                'platform',
                'meeting_url',
                'recording_url',
                'scheduled_at',
                'ends_at',
                'duration_minutes',
                'status',
            ])
            ->map(function ($class) use ($hasAccess) {
                return [
                    'id' => $class->id,
                    'chapter_id' => $class->chapter_id,
                    'teacher_id' => $class->teacher_id,
                    'title' => $class->title,
                    'description' => $class->description,
                    'learning_objectives' => $class->learning_objectives,
                    'platform' => $class->platform,
                    'meeting_url' => $hasAccess ? $class->meeting_url : null,
                    'recording_url' => $hasAccess ? $class->recording_url : null,
                    'scheduled_at' => $class->scheduled_at,
                    'ends_at' => $class->ends_at,
                    'duration_minutes' => $class->duration_minutes,
                    'status' => $class->status,
                    'is_locked' => ! $hasAccess,
                    'teacher' => $class->teacher,
                ];
            });

        // 'upcoming' includes both scheduled and currently live classes for seamless student visibility
        $upcoming = $classes->whereIn('status', ['scheduled', 'live'])->values();
        $past = $classes->whereIn('status', ['completed', 'cancelled'])->values();
        $live = $classes->where('status', 'live')->values();

        return $this->successResponse([
            'upcoming' => $upcoming,
            'live' => $live,
            'past' => $past,
            'has_access' => $hasAccess,
        ]);
    }

    /**
     * List all live classes available to the student (filterable by class_level, subject_id, status).
     *
     * GET /api/live-classes
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        // Auto-detect student's class level if not passed in query
        $studentClassLevel = null;
        if ($user && $user->studentProfile && preg_match('/(\d+)/', (string) $user->studentProfile->class, $matches)) {
            $studentClassLevel = (int) $matches[1];
        }

        $classLevel = $request->filled('class_level')
            ? $request->integer('class_level')
            : $studentClassLevel;

        $query = LiveClass::where('is_published', true)
            ->with([
                'subject:id,name,color,icon',
                'chapter:id,title,sort_order',
                'teacher:id,name',
            ]);

        if ($classLevel) {
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

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // By default, hide cancelled classes from students
            $query->where('status', '!=', 'cancelled');
        }

        $classes = $query->orderBy('scheduled_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        $classes->getCollection()->transform(function ($class) use ($hasAccess) {
            return [
                'id' => $class->id,
                'class_level' => $class->class_level,
                'subject_id' => $class->subject_id,
                'chapter_id' => $class->chapter_id,
                'title' => $class->title,
                'description' => $class->description,
                'learning_objectives' => $class->learning_objectives,
                'platform' => $class->platform,
                'meeting_url' => $hasAccess ? $class->meeting_url : null,
                'recording_url' => $hasAccess ? $class->recording_url : null,
                'scheduled_at' => $class->scheduled_at,
                'ends_at' => $class->ends_at,
                'duration_minutes' => $class->duration_minutes,
                'status' => $class->status,
                'is_locked' => ! $hasAccess,
                'teacher' => $class->teacher,
                'subject' => $class->subject,
                'chapter' => $class->chapter,
            ];
        });

        return $this->paginatedResponse($classes);
    }

    /**
     * Get currently active live classes.
     *
     * GET /api/live-classes/now
     */
    public function liveNow(Request $request): JsonResponse
    {
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $studentClassLevel = null;
        if ($user && $user->studentProfile && preg_match('/(\d+)/', (string) $user->studentProfile->class, $matches)) {
            $studentClassLevel = (int) $matches[1];
        }

        $classLevel = $request->filled('class_level')
            ? $request->integer('class_level')
            : $studentClassLevel;

        $query = LiveClass::where('is_published', true)
            ->where('status', 'live')
            ->with([
                'subject:id,name,color,icon',
                'chapter:id,title',
                'teacher:id,name',
            ]);

        if ($classLevel) {
            $query->where(function ($q) use ($classLevel) {
                $q->where('class_level', $classLevel)
                    ->orWhereHas('chapter', fn ($ch) => $ch->where('class_level', $classLevel));
            });
        }

        $classes = $query->orderBy('scheduled_at', 'asc')->get()
            ->map(function ($class) use ($hasAccess) {
                return [
                    'id' => $class->id,
                    'class_level' => $class->class_level,
                    'title' => $class->title,
                    'description' => $class->description,
                    'learning_objectives' => $class->learning_objectives,
                    'platform' => $class->platform,
                    'meeting_url' => $hasAccess ? $class->meeting_url : null,
                    'scheduled_at' => $class->scheduled_at,
                    'ends_at' => $class->ends_at,
                    'duration_minutes' => $class->duration_minutes,
                    'status' => $class->status,
                    'is_locked' => ! $hasAccess,
                    'teacher' => $class->teacher,
                    'subject' => $class->subject,
                    'chapter' => $class->chapter,
                ];
            });

        return $this->successResponse($classes, 'Active live sessions retrieved.');
    }

    /**
     * Get details of a single live class.
     *
     * GET /api/live-classes/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $class = LiveClass::where('is_published', true)
            ->with([
                'subject:id,name,color,icon',
                'chapter:id,title',
                'teacher:id,name',
                'teacher.teacherProfile:user_id,qualification,avatar_url',
            ])
            ->findOrFail($id);

        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $data = [
            'id' => $class->id,
            'class_level' => $class->class_level,
            'subject_id' => $class->subject_id,
            'chapter_id' => $class->chapter_id,
            'title' => $class->title,
            'description' => $class->description,
            'learning_objectives' => $class->learning_objectives,
            'platform' => $class->platform,
            'meeting_url' => $hasAccess ? $class->meeting_url : null,
            'recording_url' => $hasAccess ? $class->recording_url : null,
            'scheduled_at' => $class->scheduled_at,
            'ends_at' => $class->ends_at,
            'duration_minutes' => $class->duration_minutes,
            'timezone' => $class->timezone,
            'status' => $class->status,
            'is_locked' => ! $hasAccess,
            'teacher' => $class->teacher,
            'subject' => $class->subject,
            'chapter' => $class->chapter,
        ];

        if (! $hasAccess) {
            $data['subscription_required'] = true;
        }

        return $this->successResponse($data);
    }
}
