<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Note;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use ApiResponse;

    /**
     * List notes for a chapter.
     *
     * GET /api/chapters/{id}/notes
     */
    public function byChapter(Request $request, int $chapterId): JsonResponse
    {
        $chapter = Chapter::where('is_active', true)->findOrFail($chapterId);
        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $notes = $chapter->notes()
            ->where('is_active', true)
            ->with(['teacher:id,name'])
            ->get(['id', 'chapter_id', 'teacher_id', 'title', 'description', 'file_type'])
            ->map(function ($note) use ($hasAccess) {
                return [
                    'id' => $note->id,
                    'chapter_id' => $note->chapter_id,
                    'teacher_id' => $note->teacher_id,
                    'title' => $note->title,
                    'description' => $note->description,
                    'file_type' => $note->file_type,
                    'is_locked' => ! $hasAccess,
                    'teacher' => $note->teacher,
                ];
            });

        return $this->successResponse($notes);
    }

    /**
     * Get a single note with its file URL.
     *
     * GET /api/notes/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $note = Note::where('is_active', true)
            ->with(['teacher:id,name'])
            ->findOrFail($id);

        $user = $request->user();
        $hasAccess = $user ? $user->hasAccess() : false;

        $data = [
            'id' => $note->id,
            'title' => $note->title,
            'description' => $note->description,
            'file_url' => $hasAccess ? $note->file_url : null,
            'file_type' => $note->file_type,
            'is_locked' => ! $hasAccess,
            'teacher' => $note->teacher,
        ];

        if (! $hasAccess) {
            $data['subscription_required'] = true;
        }

        return $this->successResponse($data);
    }
}
