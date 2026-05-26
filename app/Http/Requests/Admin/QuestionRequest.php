<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'question_text'  => ['required', 'string'],
            'option_a'       => ['required', 'string'],
            'option_b'       => ['required', 'string'],
            'option_c'       => ['required', 'string'],
            'option_d'       => ['required', 'string'],
            'correct_option' => ['required', 'in:a,b,c,d'],
            'marks'          => ['nullable', 'integer', 'min:1'],
            'sort_order'     => ['nullable', 'integer'],
        ];
    }
}
