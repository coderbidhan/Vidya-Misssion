<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChapterRequest;
use App\Models\Chapter;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ChapterController extends Controller
{
    use ApiResponse;

    public function store(ChapterRequest $request): JsonResponse
    {
        $chapter = Chapter::create($request->validated());

        return $this->successResponse($chapter, 'Chapter created.', 201);
    }

    public function update(ChapterRequest $request, int $id): JsonResponse
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->update($request->validated());

        return $this->successResponse($chapter->fresh(), 'Chapter updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        Chapter::findOrFail($id)->delete();

        return $this->successResponse(null, 'Chapter deleted.');
    }
}
