<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Note;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    use ApiResponse;

    /**
     * List notes for a chapter.
     *
     * GET /api/chapters/{id}/notes
     */
    public function byChapter(int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);

        $notes = $chapter->notes()
            ->where('is_active', true)
            ->with(['teacher:id,name'])
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'file_type']);

        return $this->successResponse($notes);
    }

    /**
     * Get a single note with its file URL.
     *
     * GET /api/notes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $note = Note::where('is_active', true)
            ->with(['teacher:id,name'])
            ->findOrFail($id);

        return $this->successResponse($note->only(['id', 'title', 'description', 'file_url', 'file_type', 'teacher']));
    }
}
