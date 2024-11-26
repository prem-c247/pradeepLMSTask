<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\StoreExamRequest;
use App\Models\{Exam, ExamResponse, Question};
use Exception;

class ExamController extends Controller
{
    // Get the questions by the subject Id
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

    // The attempted exams by the student
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

    public function show($id)
    {
        $exam = Exam::with(['student', 'subject', 'responses.question'])->find($id);

        if (!$exam) {
            return $this->notFound('exam');
        }

        return response200(__('message.fetched', ['name' => __('message.exam')]), $exam);
    }
}
