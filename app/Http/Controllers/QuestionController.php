<?php

namespace App\Http\Controllers;

use App\Http\Requests\Question\{StoreQuestionRequest, UpdateQuestionRequest};
use App\Models\Question;

class QuestionController extends Controller
{
    /**
     * index: List all questions by the specific subject ID 
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function index($subjectId)
    {
        $questions = Question::where('subject_id', $subjectId)->paginate(PAGINATE);
        if ($questions->isEmpty()) {
            return $this->notFound('question');
        }
        return response200(__('message.fetched', ['name' => __('message.question')]), $questions);
    }

    /**
     * store: Store the questions alongwith options and its correct_answer option
     *
     * @param  mixed $request
     * @return void
     */
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

    /**
     * show: Get details of a question by the specific question ID
     *
     * @param  mixed $questionId
     * @return void
     */
    public function show($questionId)
    {
        $question = Question::find($questionId);
        if (!$question) {
            return $this->notFound('question');
        }
        return response200(__('message.fetched', ['name' => __('message.question')]), $question);
    }

    /**
     * update: Update a question details of the specific question ID
     *
     * @param  mixed $request
     * @param  mixed $questionId
     * @return void
     */
    public function update(UpdateQuestionRequest $request, $questionId)
    {
        $validated = $request->validated();
        $question = Question::find($questionId);
        if (!$question) {
            return $this->notFound('question');
        }

        $question->update($validated);
        return response200(__('message.updated', ['name' => __('message.question')]), $question);
    }

    /**
     * delete: Delete a specific question
     *
     * @param  mixed $questionId
     * @return void
     */
    public function delete($questionId)
    {
        $question = Question::find($questionId);
        if (!$question) {
            return $this->notFound('question');
        }
        $question->delete();
        return response200(__('message.deleted', ['name' => __('message.question')]), $question);
    }
}
