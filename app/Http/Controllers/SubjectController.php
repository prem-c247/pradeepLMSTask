<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subject\{StoreSubjectRequest, UpdateSubjectRequest};
use App\Models\{Subject, User};

class SubjectController extends Controller
{
    public function index()
    {
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
    }

    public function store(StoreSubjectRequest $request)
    {
        $authUser = auth()->user();

        // Teacher only can create subject
        if ($authUser->role_id !== User::ROLE_TEACHER) {
            return response401(__('message.not_access'));
        }

        $validatedData = $request->validated();

        $validatedData['school_user_id'] = $authUser->teacherDetails?->school_id ?? 0;
        $validatedData['teacher_user_id'] = $authUser->id;

        $subject = Subject::create($validatedData);

        return response201(__('message.created', ['name' => __('message.subject')]), $subject);
    }

    public function show($id)
    {
        $subject = Subject::with(['schoolUser', 'teacherUser'])->find($id);

        if (!$subject) {
            return $this->notFound('subject');
        }

        return response200(__('message.fetched', ['name' => __('message.subject')]), $subject);
    }

    public function update(UpdateSubjectRequest $request, $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return $this->notFound('subject');
        }

        $validated = $request->validated();

        $subject->update($validated);

        return response200(__('message.updated', ['name' => __('message.subject')]), $subject);
    }

    public function delete($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return $this->notFound('subject');
        }

        $subject->delete();

        return response200(__('message.deleted', ['name' => __('message.subject')]));
    }
}
