<?php

namespace App\Http\Controllers;

use App\Http\Requests\School\UpdateSchoolRequest;
use App\Services\SchoolService;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    protected $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    /**
     * index: Get the all schools along with school details
     * Can apply multiple filter like (name, email, phone, status)
     * @param  mixed $request
     * @return void
     */
    function index(Request $request)
    {
        return $this->schoolService->getSchools($request);
    }

    /**
     * details: Get the details of specific school
     *
     * @param  mixed $schoolId
     * @return void
     */
    public function details($schoolId)
    {
        return $this->schoolService->getSchoolDetails($schoolId);
    }

    /**
     * update: Update the school details provided by school ID
     *
     * @param  mixed $request
     * @param  mixed $schoolId
     * @return void
     */
    public function update(UpdateSchoolRequest $request, $schoolId)
    {
        return $this->schoolService->updateSchool($request, $schoolId);
    }

    /**
     * delete: Delete the specific school by the school ID
     *
     * @param  mixed $schoolId
     * @return void
     */
    public function delete($schoolId)
    {
        return $this->schoolService->deleteSchool($schoolId);
    }
}
