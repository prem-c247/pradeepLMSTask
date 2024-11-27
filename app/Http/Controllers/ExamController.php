<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\StoreExamRequest;
use App\Models\{Exam, ExamResponse, Question};
use Exception;

class ExamController extends Controller
{
    /**
     * attemptExam: Get the questions by the subject Id for attempting exam  
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function attemptExam($subjectId)
    {
        $questions = Question::where('subject_id', $subjectId)->get([
            'id',
            'subject_id',
            'question_text',
            'options'
        ]);
        if ($questions->isEmpty()) {
            return $this->notFound('question');
        }
        return response200(__('message.fetched', ['name' => __('message.question')]), $questions);
    }

    /**
     * index: The attempted exams by the authenticated user ID
     *
     * @return void
     */
    public function index()
    {
        $exams  = Exam::where('student_id', auth()->id())
            ->with(['student', 'subject', 'responses.question'])
            ->get();
        if ($exams->isEmpty()) {
            return $this->notFound('exam');
        }
        return response200(__('message.fetched', ['name' => __('message.exam')]), $exams);
    }

    /**
     * storeExam: Store the exam attemepted by the student
     *
     * @param  mixed $request
     * @return void
     */
    public function storeExam(StoreExamRequest $request)
    {
        try {
            $validated = $request->validated();
            $exam = Exam::create([
                'student_id' => auth()->id(),
                'subject_id' => $validated['subject_id'],
            ]);
            $responses = array_map(function ($response) use ($exam) {
                return [
                    'exam_id' => $exam->id,
                    'question_id' => $response['question_id'],
                    'chosen_option' => $response['chosen_option'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $validated['responses']);

            ExamResponse::insert($responses);
            return response201(__('message.submitted', ['name' => __('message.exam')]), $exam->load('responses'));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.submission')]), $e->getMessage());
        }
    }

    /**
     * show: Get the attempted exam details with question and choosed option
     *
     * @param  mixed $examId
     * @return void
     */
    public function show($examId)
    {
        $exam = Exam::with(['student', 'subject', 'responses.question'])->find($examId);
        if (!$exam) {
            return $this->notFound('exam');
        }
        return response200(__('message.fetched', ['name' => __('message.exam')]), $exam);
    }
}
