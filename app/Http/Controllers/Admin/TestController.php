<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestRequest;
use App\Models\Test;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $tests = Test::with(['chapter:id,title'])
            ->withCount('questions')
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->paginatedResponse($tests);
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
