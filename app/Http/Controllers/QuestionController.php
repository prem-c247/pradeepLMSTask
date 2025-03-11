<?php

namespace App\Http\Controllers;

use App\Http\Requests\Question\{StoreQuestionRequest, UpdateQuestionRequest};
use App\Services\QuestionService;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    /**
     * index: List all questions by the specific subject ID 
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function index($subjectId)
    {
        return $this->questionService->getQuestionsBySubject($subjectId);
    }

    /**
     * store: Store the questions alongwith options and its correct_answer option
     *
     * @param  mixed $request
     * @return void
     */
    public function store(StoreQuestionRequest $request)
    {
        return $this->questionService->storeQuestionWithOptions($request->validated());
    }

    /**
     * show: Get details of a question by the specific question ID
     *
     * @param  mixed $questionId
     * @return void
     */
    public function show($questionId)
    {
        return $this->questionService->getQuestionDetails($questionId);
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
        return $this->questionService->updateQuestion($request->validated(), $questionId);
    }

    /**
     * delete: Delete a specific question
     *
     * @param  mixed $questionId
     * @return void
     */
    public function delete($questionId)
    {
        return $this->questionService->deleteQuestion($questionId);
    }
}
