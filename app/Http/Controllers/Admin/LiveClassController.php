<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LiveClassRequest;
use App\Http\Requests\Admin\UpdateLiveClassStatusRequest;
use App\Models\LiveClass;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LiveClassController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $classes = LiveClass::with(['chapter:id,title', 'teacher:id,name'])
            ->orderByDesc('scheduled_at')
            ->paginate(20);

        return $this->paginatedResponse($classes);
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
