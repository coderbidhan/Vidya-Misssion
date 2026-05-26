<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'test_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'marks',
        'sort_order',
    ];

    /**
     * Fields hidden from student-facing API responses.
     * Controllers must explicitly make available for admin/scoring.
     */
    protected $hidden = ['correct_option'];

    protected function casts(): array
    {
        return [
            'marks'      => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * The test this question belongs to.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
