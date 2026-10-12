<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkQuestionsRequest;
use App\Http\Requests\Admin\QuestionRequest;
use App\Models\Question;
use App\Models\Test;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    use ApiResponse;

    /**
     * Bulk add questions to a test.
     *
     * POST /api/admin/tests/{id}/questions
     */
    public function bulkStore(BulkQuestionsRequest $request, int $testId): JsonResponse
    {
        $test = Test::findOrFail($testId);

        $questions = collect($request->questions)->map(fn ($q, $index) => [
            'test_id' => $test->id,
            'question_text' => strip_tags($q['question_text']),
            'option_a' => strip_tags($q['option_a']),
            'option_b' => strip_tags($q['option_b']),
            'option_c' => strip_tags($q['option_c']),
            'option_d' => strip_tags($q['option_d']),
            'correct_option' => $q['correct_option'],
            'marks' => $q['marks'] ?? 1,
            'sort_order' => $q['sort_order'] ?? $index,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        Question::insert($questions);

        // Recalculate total_marks
        $totalMarks = Question::where('test_id', $test->id)->sum('marks');
        $test->update(['total_marks' => $totalMarks]);

        return $this->successResponse(
            Question::where('test_id', $test->id)->makeVisible('correct_option')->get(),
            count($questions).' questions added.',
            201
        );
    }

    /**
     * Update a single question.
     *
     * PUT /api/admin/questions/{id}
     */
    public function update(QuestionRequest $request, int $id): JsonResponse
    {
        $question = Question::findOrFail($id);
        $data = $request->validated();

        foreach (['question_text', 'option_a', 'option_b', 'option_c', 'option_d'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = strip_tags($data[$field]);
            }
        }

        $question->update($data);

        // Recalculate total_marks if marks changed
        if ($request->has('marks')) {
            $totalMarks = Question::where('test_id', $question->test_id)->sum('marks');
            $question->test->update(['total_marks' => $totalMarks]);
        }

        return $this->successResponse($question->fresh()->makeVisible('correct_option'), 'Question updated.');
    }

    /**
     * Delete a single question.
     *
     * DELETE /api/admin/questions/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $question = Question::findOrFail($id);
        $testId = $question->test_id;
        $question->delete();

        // Recalculate total_marks
        $totalMarks = Question::where('test_id', $testId)->sum('marks');
        Test::find($testId)?->update(['total_marks' => $totalMarks]);

        return $this->successResponse(null, 'Question deleted.');
    }
}
