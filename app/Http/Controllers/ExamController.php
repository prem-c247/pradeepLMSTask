<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\StoreExamRequest;
use App\Services\ExamService;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * attemptExam: Get the questions by the subject Id for attempting exam  
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function attemptExam($subjectId)
    {
        return $this->examService->getQuestionsForAttemptExam($subjectId);
    }

    /**
     * index: The attempted exams by the authenticated user ID
     *
     * @return void
     */
    public function index()
    {
        return  $this->examService->getAttemptedExams();
    }

    /**
     * storeExam: Store the exam attemepted by the student
     *
     * @param  mixed $request
     * @return void
     */
    public function storeExam(StoreExamRequest $request)
    {
        return  $this->examService->storeExam($request->validated());
    }

    /**
     * show: Get the attempted exam details with question and choosed option
     *
     * @param  mixed $examId
     * @return void
     */
    public function show($examId)
    {
        return  $this->examService->getExamDetails($examId);
    }
}
