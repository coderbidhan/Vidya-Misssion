<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoteRequest;
use App\Models\Note;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $notes = Note::with(['chapter:id,title', 'teacher:id,name'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->paginatedResponse($notes);
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
