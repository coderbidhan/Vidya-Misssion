<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LiveClassRequest;
use App\Http\Requests\Admin\UpdateLiveClassStatusRequest;
use App\Models\LiveClass;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    use ApiResponse;

    /**
     * List live classes with optional filtering by class, subject, chapter, status, and search.
     *
     * GET /api/admin/live-classes
     */
    public function index(Request $request): JsonResponse
    {
        $query = LiveClass::with(['chapter:id,title,subject_id,class_level', 'teacher:id,name']);

        if ($request->filled('class_level')) {
            $query->whereHas('chapter', fn ($q) => $q->where('class_level', $request->integer('class_level')));
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('chapter', fn ($q) => $q->where('subject_id', $request->integer('subject_id')));
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->integer('chapter_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
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
        $class = LiveClass::with(['chapter.subject', 'teacher:id,name'])->findOrFail($id);

        return $this->successResponse($class);
    }

    public function store(LiveClassRequest $request): JsonResponse
    {
        $class = LiveClass::create($request->validated());

        return $this->successResponse($class, 'Live class created.', 201);
    }

    public function update(LiveClassRequest $request, int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);
        $class->update($request->validated());

        return $this->successResponse($class->fresh(), 'Live class updated.');
    }

    /**
     * Update only the status of a live class.
     *
     * PATCH /api/admin/live-classes/{id}/status
     */
    public function updateStatus(UpdateLiveClassStatusRequest $request, int $id): JsonResponse
    {
        $class = LiveClass::findOrFail($id);
        $class->update(['status' => $request->status]);

        return $this->successResponse($class->fresh(), 'Live class status updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        LiveClass::findOrFail($id)->delete();

        return $this->successResponse(null, 'Live class deleted.');
    }
}
