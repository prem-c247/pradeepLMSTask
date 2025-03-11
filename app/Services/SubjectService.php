<?php

namespace App\Services;

use App\Models\{Subject, User};
use Exception;

class SubjectService extends BaseService
{
    /**
     * getSubjects: Get subject details by their ID
     *
     * @return void
     */
    public function getSubjects()
    {
        try {
            $query = Subject::latest();
            $authUser = auth()->user();

            // Determine the subjects to fetch based on the authenticated user's role.
            // Each role filters subjects according to its associated school or user details. 
            switch ($authUser->role_id) {
                case User::ROLE_SCHOOL:
                    $query->where('school_user_id', $authUser->id);
                    break;

                case User::ROLE_STUDENT:
                    $query->where('school_user_id', $authUser->studentDetails?->school_id ?? 0);
                    break;

                case User::ROLE_TEACHER:
                    $query->where('school_user_id', $authUser->teacherDetails?->school_id ?? 0);
                    break;
            }
            $subjects = $query->paginate(PAGINATE);
            if ($subjects->isEmpty()) {
                return $this->notFound('subject');
            }
            return response200(__('message.fetched', ['name' => __('message.subject')]), $subjects);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.subject')]), $e->getMessage());
        }
    }

    /**
     * storeSubject: Store subject information in the database 
     *
     * @param  mixed $request
     * @return void
     */
    public function storeSubject(array $validatedData)
    {
        try {
            $authUser = auth()->user();
            // Teacher only can create subject
            if ($authUser->role_id !== User::ROLE_TEACHER) {
                return response401(__('message.not_access'));
            }

            $validatedData['school_user_id'] = $authUser->teacherDetails?->school_id ?? 0;
            $validatedData['teacher_user_id'] = $authUser->id;
            $subject = Subject::create($validatedData);

            return response201(__('message.created', ['name' => __('message.subject')]), $subject);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.submission')]), $e->getMessage());
        }
    }

    /**
     * getSubjectDetails: Get subject details by their ID
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function getSubjectDetails(int $subjectId)
    {
        try {
            $subject = Subject::with(['schoolUser', 'teacherUser'])->find($subjectId);
            if (!$subject) {
                return $this->notFound('subject');
            }
            return response200(__('message.fetched', ['name' => __('message.subject')]), $subject);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.subject')]), $e->getMessage());
        }
    }

    /**
     * updateSubject: Update the subject details by their ID
     *
     * @param  mixed $request
     * @param  mixed $subjectId
     * @return void
     */
    public function updateSubject(array $validated, int $subjectId)
    {
        try {
            $subject = Subject::find($subjectId);
            if (!$subject) {
                return $this->notFound('subject');
            }

            $subject->update($validated);
            return response200(__('message.updated', ['name' => __('message.subject')]), $subject);
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }

    /**
     * delete: Delete subject details by their ID
     *
     * @param  mixed $subjectId
     * @return void
     */
    public function deleteSubject(int $subjectId)
    {
        try {
            $subject = Subject::find($subjectId);
            if (!$subject) {
                return $this->notFound('subject');
            }

            $subject->delete();
            return response200(__('message.deleted', ['name' => __('message.subject')]));
        } catch (Exception $e) {
            return response200(__('message.server_error', ['name' => __('message.deletion')]), $e->getMessage());
        }
    }
}
