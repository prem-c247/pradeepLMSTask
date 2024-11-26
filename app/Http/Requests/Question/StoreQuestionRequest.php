<?php

namespace App\Http\Requests\Question;

use App\Http\Requests\BaseFormRequest;

class StoreQuestionRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.options' => 'required|array',
            'questions.*.correct_answer' => 'required|integer',
        ];
    }
}
