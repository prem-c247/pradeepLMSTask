<?php

namespace App\Services;

use App\Models\Question;
use Exception;

class QuestionService extends BaseService
{
    /**
     * getQuestionsBySubject: Get all questions by the specific subject ID 
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function getQuestionsBySubject($subjectId)
    {
        try {
            $questions = Question::where('subject_id', $subjectId)->paginate(PAGINATE);
            if ($questions->isEmpty()) {
                return $this->notFound('question');
            }
            return response200(__('message.fetched', ['name' => __('message.question')]), $questions);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.exam')]), $e->getMessage());
        }
    }

    /**
     * storeQuestionAndOption: Store the questions alongwith options and its correct_answer option
     *
     * @param  mixed $request
     * @return void
     */
    public function storeQuestionWithOptions(array $validated)
    {
        try {
            $questionsData = array_map(function ($question) use ($validated) {
                if (isset($question['options'])) {
                    $question['options'] = json_encode($question['options']); // Convert options array to json
                }
                return array_merge($question, ['subject_id' => $validated['subject_id']]);
            }, $validated['questions']);

            Question::insert($questionsData);
            return response201(__('message.added', ['name' => __('message.question')]));
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.submission')]), $e->getMessage());
        }
    }

    /**
     * getQuestionDetails: Get details of a question by the specific question ID
     *
     * @param  mixed $questionId
     * @return void
     */
    public function getQuestionDetails($questionId)
    {
        try {
            $question = Question::find($questionId);
            if (!$question) {
                return $this->notFound('question');
            }
            return response200(__('message.fetched', ['name' => __('message.question')]), $question);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.details')]), $e->getMessage());
        }
    }

    /**
     * updateQuestion: Update a question details of the specific question ID
     *
     * @param  mixed $request
     * @param  mixed $questionId
     * @return void
     */
    public function updateQuestion(array $validated, int $questionId)
    {
        try {
            $question = Question::find($questionId);
            if (!$question) {
                return $this->notFound('question');
            }

            $question->update($validated);
            return response200(__('message.updated', ['name' => __('message.question')]), $question);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }

    /**
     * deleteQuestion: Delete a specific question
     *
     * @param  mixed $questionId
     * @return void
     */
    public function deleteQuestion($questionId)
    {
        try {
            $question = Question::find($questionId);
            if (!$question) {
                return $this->notFound('question');
            }
            $question->delete();
            return response200(__('message.deleted', ['name' => __('message.question')]), $question);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.deletion')]), $e->getMessage());
        }
    }
}
