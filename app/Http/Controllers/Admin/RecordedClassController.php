<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordedClassRequest;
use App\Models\RecordedClass;
use App\Services\GoogleDriveService;
use App\Services\YouTubeService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecordedClassController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly GoogleDriveService $driveService) {}

    /**
     * List recorded classes with optional filtering by class, subject, chapter, status, and search.
     *
     * GET /api/admin/recorded-classes
     */
    public function index(Request $request): JsonResponse
    {
        $query = RecordedClass::with(['chapter:id,title,subject_id,class_level', 'teacher:id,name']);

        if ($request->filled('class_level')) {
            $query->whereHas('chapter', fn ($q) => $q->where('class_level', $request->integer('class_level')));
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('chapter', fn ($q) => $q->where('subject_id', $request->integer('subject_id')));
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->integer('chapter_id'));
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $classes = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return $this->paginatedResponse($classes);
    }

    /**
     * Get details of a single recorded class.
     *
     * GET /api/admin/recorded-classes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $class = RecordedClass::with(['chapter.subject', 'teacher:id,name'])->findOrFail($id);

        return $this->successResponse($class);
    }

    public function store(RecordedClassRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (! empty($data['video_url'])) {
            $ytId = YouTubeService::extractVideoId($data['video_url']);
            if ($ytId) {
                $data['video_url'] = "https://www.youtube.com/watch?v={$ytId}";
                $data['gdrive_file_id'] = $data['gdrive_file_id'] ?? $ytId;
                $data['gdrive_embed_url'] = "https://www.youtube.com/embed/{$ytId}";
            }
        } elseif (! empty($data['gdrive_file_id'])) {
            $ytId = YouTubeService::extractVideoId($data['gdrive_file_id']);
            if ($ytId) {
                $data['video_url'] = "https://www.youtube.com/watch?v={$ytId}";
                $data['gdrive_embed_url'] = "https://www.youtube.com/embed/{$ytId}";
            } else {
                $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($data['gdrive_file_id']);
            }
        }

        $class = RecordedClass::create($data);

        return $this->successResponse($class, 'Recorded class created.', 201);
    }

    public function update(RecordedClassRequest $request, int $id): JsonResponse
    {
        $class = RecordedClass::findOrFail($id);
        $data = $request->validated();

        if (array_key_exists('video_url', $data)) {
            if (! empty($data['video_url'])) {
                $ytId = YouTubeService::extractVideoId($data['video_url']);
                if ($ytId) {
                    $data['video_url'] = "https://www.youtube.com/watch?v={$ytId}";
                    $data['gdrive_file_id'] = $ytId;
                    $data['gdrive_embed_url'] = "https://www.youtube.com/embed/{$ytId}";
                }
            } else {
                $data['video_url'] = null;
                $data['gdrive_file_id'] = null;
                $data['gdrive_embed_url'] = null;
            }
        }

        if (isset($data['gdrive_file_id']) && ! isset($data['gdrive_embed_url'])) {
            $ytId = YouTubeService::extractVideoId($data['gdrive_file_id']);
            if ($ytId) {
                $data['video_url'] = "https://www.youtube.com/watch?v={$ytId}";
                $data['gdrive_embed_url'] = "https://www.youtube.com/embed/{$ytId}";
            } else {
                $data['gdrive_embed_url'] = $this->driveService->getEmbedUrl($data['gdrive_file_id']);
            }
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
