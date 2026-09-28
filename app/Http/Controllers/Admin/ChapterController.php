<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChapterRequest;
use App\Models\Chapter;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ChapterController extends Controller
{
    use ApiResponse;

    public function store(ChapterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chapters', 'public');
            $data['image'] = Storage::url($path);
        }

        $chapter = Chapter::create($data);

        return $this->successResponse($chapter, 'Chapter created.', 201);
    }

    public function update(ChapterRequest $request, int $id): JsonResponse
    {
        $chapter = Chapter::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chapters', 'public');
            $data['image'] = Storage::url($path);
        }

        $chapter->update($data);

        return $this->successResponse($chapter->fresh(), 'Chapter updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        Chapter::findOrFail($id)->delete();

        return $this->successResponse(null, 'Chapter deleted.');
    }
}
