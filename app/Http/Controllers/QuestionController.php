<?php

namespace App\Http\Controllers;

use App\Http\Requests\Question\{StoreQuestionRequest, UpdateQuestionRequest};
use App\Models\{Question};

class QuestionController extends Controller
{
    // List all questions for a subject
    public function index($subjectId)
    {
        $questions = Question::where('subject_id', $subjectId)->paginate(PAGINATE);

        if ($questions->isEmpty()) {
            return $this->notFound('question');
        }

        return response200(__('message.fetched', ['name' => __('message.question')]), $questions);
    }

    // Add multiple questions
    public function store(StoreQuestionRequest $request)
    {
        $validated = $request->validated();

        $questionsData = array_map(function ($question) use ($validated) {
            if (isset($question['options'])) {
                $question['options'] = json_encode($question['options']); // Convert options array to json
            }
            return array_merge($question, ['subject_id' => $validated['subject_id']]);
        }, $validated['questions']);

        Question::insert($questionsData);

        return response201(__('message.added', ['name' => __('message.question')]));
    }

    // Get details of a single question
    public function show($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return $this->notFound('question');
        }

        return response200(__('message.fetched', ['name' => __('message.question')]), $question);
    }

    // Update a single question
    public function update(UpdateQuestionRequest $request, $id)
    {
        $validated = $request->validated();

        $question = Question::find($id);

        if (!$question) {
            return $this->notFound('question');
        }

        $question->update($validated);

        return response200(__('message.updated', ['name' => __('message.question')]), $question);
    }

    // Delete a question
    public function delete($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return $this->notFound('question');
        }

        $question->delete();

        return response200(__('message.deleted', ['name' => __('message.question')]), $question);
    }
}
