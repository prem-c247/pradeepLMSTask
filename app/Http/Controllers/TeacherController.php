<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\{SendInviteLinkRequest, UpdateTeacherRequest};
use App\Services\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * index: Get all teachers alongwith teachers details and addresses
     * Can apply multiple filter like (name, email, phone, status)
     *
     * @param  mixed $request
     * @return void
     */
    function index(Request $request)
    {
        return $this->teacherService->getTeachers($request->all());
    }

    /**
     * SendInviteLinkToTeacher: Send the invite link to given email. 
     * Via the link user can registered himself as teacher 
     * @param  mixed $request
     * @return void
     */
    function SendInviteLinkToTeacher(SendInviteLinkRequest $request)
    {
        return $this->teacherService->SendInviteLinkToTeacher($request->email);
    }

    /**
     * details: Get the teacher details by their ID
     *
     * @param  mixed $teacherId
     * @return void
     */
    public function details($teacherId)
    {
        return $this->teacherService->getTeacherDetails($teacherId);
    }

    /**
     * update: Update the teacher details by their ID
     *
     * @param  mixed $request
     * @param  mixed $teacherId
     * @return void
     */
    public function update(UpdateTeacherRequest $request, $teacherId)
    {
        return $this->teacherService->updateTeacher($request, $teacherId);
    }

    /**
     * delete: Delete teacher by their ID
     *
     * @param  mixed $teacherId
     * @return void
     */
    public function delete($teacherId)
    {
        return $this->teacherService->deleteTeacher($teacherId);
    }
}
