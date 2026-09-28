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

        $parsed = $this->parseVideoSource($data['gdrive_file_id']);
        $data['gdrive_file_id'] = $parsed['gdrive_file_id'];
        $data['gdrive_embed_url'] = $parsed['gdrive_embed_url'];

        $class = RecordedClass::create($data);

        return $this->successResponse($class, 'Recorded class created.', 201);
    }

    public function update(RecordedClassRequest $request, int $id): JsonResponse
    {
        $class = RecordedClass::findOrFail($id);
        $data = $request->validated();

        if (isset($data['gdrive_file_id'])) {
            $parsed = $this->parseVideoSource($data['gdrive_file_id']);
            $data['gdrive_file_id'] = $parsed['gdrive_file_id'];
            $data['gdrive_embed_url'] = $parsed['gdrive_embed_url'];
        }

        $class->update($data);

        return $this->successResponse($class->fresh(), 'Recorded class updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        RecordedClass::findOrFail($id)->delete();

        return $this->successResponse(null, 'Recorded class deleted.');
    }

    /**
     * Parse Google Drive ID/URL or YouTube link/ID to extract ID and construct embed URL.
     */
    private function parseVideoSource(string $input): array
    {
        $fileId = trim($input);
        $embedUrl = '';

        // 1. YouTube URLs (watch, embed, short link)
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $fileId, $match)) {
            $fileId = $match[1];
            $embedUrl = "https://www.youtube.com/embed/{$fileId}";
        }
        // 2. Plain YouTube Video ID (11 chars)
        elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $fileId)) {
            $embedUrl = "https://www.youtube.com/embed/{$fileId}";
        }
        // 3. Google Drive share URL
        elseif (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/i', $fileId, $match)) {
            $fileId = $match[1];
            $embedUrl = "https://drive.google.com/file/d/{$fileId}/preview";
        }
        // 4. Plain Google Drive File ID
        else {
            $embedUrl = "https://drive.google.com/file/d/{$fileId}/preview";
        }

        return [
            'gdrive_file_id' => $fileId,
            'gdrive_embed_url' => $embedUrl,
        ];
    }
}
