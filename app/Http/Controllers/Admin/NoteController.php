<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoteRequest;
use App\Models\Note;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    use ApiResponse;

    /**
     * List notes with optional filtering by class, subject, chapter, status, file_type, and search.
     *
     * GET /api/admin/notes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Note::with(['chapter:id,title,subject_id,class_level', 'teacher:id,name']);

        if ($request->filled('class_level')) {
            $query->whereHas('chapter', fn ($q) => $q->where('class_level', $request->integer('class_level')));
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('chapter', fn ($q) => $q->where('subject_id', $request->integer('subject_id')));
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->integer('chapter_id'));
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->input('file_type'));
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

        $notes = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return $this->paginatedResponse($notes);
    }

    /**
     * Get details of a single note.
     *
     * GET /api/admin/notes/{id}
     */
    public function show(int $id): JsonResponse
    {
        $note = Note::with(['chapter.subject', 'teacher:id,name'])->findOrFail($id);

        return $this->successResponse($note);
    }

    public function store(NoteRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle file upload if a file was provided
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('notes', 'public');
            $data['file_url'] = Storage::url($path);
        }

        $note = Note::create(array_filter($data, fn ($v) => $v !== null));

        return $this->successResponse($note, 'Note created.', 201);
    }

    public function update(NoteRequest $request, int $id): JsonResponse
    {
        $note = Note::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('notes', 'public');
            $data['file_url'] = Storage::url($path);
        }

        $note->update(array_filter($data, fn ($v) => $v !== null));

        return $this->successResponse($note->fresh(), 'Note updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        Note::findOrFail($id)->delete();

        return $this->successResponse(null, 'Note deleted.');
    }
}
