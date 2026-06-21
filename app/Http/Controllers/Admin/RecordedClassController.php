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
        $videoType = $data['video_type'] ?? 'google_drive';
        $videoUrl = $data['video_url'] ?? null;
        $fileId = $data['gdrive_file_id'] ?? null;

        if ($videoUrl) {
            if ($videoType === 'youtube') {
                $youtubeId = $this->extractYoutubeId($videoUrl) ?: $videoUrl;
                $data['video_type'] = 'youtube';
                $data['gdrive_file_id'] = $youtubeId;
                $data['gdrive_embed_url'] = "https://www.youtube.com/embed/{$youtubeId}";
            } else {
                $driveId = $this->extractGoogleDriveId($videoUrl) ?: $videoUrl;
                $data['video_type'] = 'google_drive';
                $data['gdrive_file_id'] = $driveId;
                $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($driveId);
            }
        } elseif ($fileId) {
            $data['video_type'] = 'google_drive';
            $data['gdrive_file_id'] = $fileId;
            $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($fileId);
        } else {
            return $this->errorResponse('Video URL or Google Drive file ID is required.', 422);
        }

        $class = RecordedClass::create($data);

        return $this->successResponse($class, 'Recorded class created.', 201);
    }

    public function update(RecordedClassRequest $request, int $id): JsonResponse
    {
        $class = RecordedClass::findOrFail($id);
        $data = $request->validated();

        $videoType = $data['video_type'] ?? $class->video_type ?? 'google_drive';
        $videoUrl = $data['video_url'] ?? null;
        $fileId = $data['gdrive_file_id'] ?? null;

        if ($videoUrl) {
            if ($videoType === 'youtube') {
                $youtubeId = $this->extractYoutubeId($videoUrl) ?: $videoUrl;
                $data['video_type'] = 'youtube';
                $data['gdrive_file_id'] = $youtubeId;
                $data['gdrive_embed_url'] = "https://www.youtube.com/embed/{$youtubeId}";
            } else {
                $driveId = $this->extractGoogleDriveId($videoUrl) ?: $videoUrl;
                $data['video_type'] = 'google_drive';
                $data['gdrive_file_id'] = $driveId;
                $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($driveId);
            }
        } elseif ($fileId) {
            $data['video_type'] = 'google_drive';
            $data['gdrive_file_id'] = $fileId;
            $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($fileId);
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
     * Extract YouTube video ID from a URL.
     */
    private function extractYoutubeId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract Google Drive file ID from a URL.
     */
    private function extractGoogleDriveId(string $url): ?string
    {
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
