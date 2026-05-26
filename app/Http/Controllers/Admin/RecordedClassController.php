<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordedClassRequest;
use App\Models\RecordedClass;
use App\Services\GoogleDriveService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RecordedClassController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly GoogleDriveService $driveService) {}

    public function index(): JsonResponse
    {
        $classes = RecordedClass::with(['chapter:id,title', 'teacher:id,name'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->paginatedResponse($classes);
    }

    public function store(RecordedClassRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($data['gdrive_file_id']);

        $class = RecordedClass::create($data);

        return $this->successResponse($class, 'Recorded class created.', 201);
    }

    public function update(RecordedClassRequest $request, int $id): JsonResponse
    {
        $class = RecordedClass::findOrFail($id);
        $data  = $request->validated();

        if (isset($data['gdrive_file_id'])) {
            $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($data['gdrive_file_id']);
        }

        $class->update($data);

        return $this->successResponse($class->fresh(), 'Recorded class updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        RecordedClass::findOrFail($id)->delete();

        return $this->successResponse(null, 'Recorded class deleted.');
    }
}
