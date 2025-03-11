<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subject\{StoreSubjectRequest, UpdateSubjectRequest};
use App\Services\SubjectService;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * index: Get subject details by their ID
     *
     * @return void
     */
    public function index()
    {
        return $this->subjectService->getSubjects();
    }

    /**
     * store: Store subject information in the database 
     *
     * @param  mixed $request
     * @return void
     */
    public function store(StoreSubjectRequest $request)
    {
        return $this->subjectService->storeSubject($request->validated());
    }

    /**
     * show: Get subject details by their ID
     *
     * @param  mixed $id
     * @return void
     */
    public function show($subjectId)
    {
        return $this->subjectService->getSubjectDetails($subjectId);
    }

    /**
     * update: Update the subject details by their ID
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(UpdateSubjectRequest $request, $subjectId)
    {
        return $this->subjectService->updateSubject($request->validated(), $subjectId);
    }

    /**
     * delete: Delete subject details by their ID
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($subjectId)
    {
        return $this->subjectService->deleteSubject($subjectId);
    }
}
