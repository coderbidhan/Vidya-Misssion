<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubjectRequest;
use App\Models\Subject;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        return $this->successResponse(Subject::orderBy('sort_order')->get());
    }

    public function store(SubjectRequest $request): JsonResponse
    {
        $subject = Subject::create($request->validated());

        return $this->successResponse($subject, 'Subject created.', 201);
    }

    public function update(SubjectRequest $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->update($request->validated());

        return $this->successResponse($subject->fresh(), 'Subject updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        Subject::findOrFail($id)->delete();

        return $this->successResponse(null, 'Subject deleted.');
    }

    /**
     * GET /api/admin/subjects/{id}/chapters
     */
    public function chapters(int $subjectId): JsonResponse
    {
        $subject = Subject::findOrFail($subjectId);
        $chapters = $subject->chapters()->orderBy('sort_order')->get();

        return $this->successResponse($chapters);
    }
}
