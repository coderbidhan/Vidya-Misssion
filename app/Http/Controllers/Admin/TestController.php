<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestRequest;
use App\Models\Test;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use ApiResponse;

    /**
     * List tests with optional filtering by class, subject, chapter, status, and search.
     *
     * GET /api/admin/tests
     */
    public function index(Request $request): JsonResponse
    {
        $query = Test::with(['chapter:id,title,subject_id,class_level'])
            ->withCount('questions');

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
            $query->where('title', 'like', "%{$search}%");
        }

        $tests = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return $this->paginatedResponse($tests);
    }

    /**
     * Get details of a single test along with its questions.
     *
     * GET /api/admin/tests/{id}
     */
    public function show(int $id): JsonResponse
    {
        $test = Test::with(['chapter.subject', 'questions' => fn ($q) => $q->orderBy('sort_order')])
            ->findOrFail($id);

        return $this->successResponse($test);
    }

    public function store(TestRequest $request): JsonResponse
    {
        $test = Test::create($request->validated());

        return $this->successResponse($test, 'Test created.', 201);
    }

    public function update(TestRequest $request, int $id): JsonResponse
    {
        $test = Test::findOrFail($id);
        $test->update($request->validated());

        return $this->successResponse($test->fresh(), 'Test updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        Test::findOrFail($id)->delete();

        return $this->successResponse(null, 'Test deleted.');
    }
}
