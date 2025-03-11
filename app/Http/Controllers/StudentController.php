<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\UpdateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * index: Get the all students along with details
     * Can apply multiple filter like (name, email, phone, status)
     *
     * @param  mixed $request
     * @return void
     */
    function index(Request $request)
    {
        return $this->studentService->getStudents($request->all());
    }

    /**
     * details: Get student details by their ID.
     *
     * @param  mixed $studentId
     * @return void
     */
    public function details($studentId)
    {
        return $this->studentService->studentDetails($studentId);
    }

    /**
     * update: Update student details by their ID
     *
     * @param  mixed $request
     * @param  mixed $studentId
     * @return void
     */
    public function update(UpdateStudentRequest $request, $studentId)
    {
        return $this->studentService->studentDetails($request, $studentId);
    }

    /**
     * delete: Delete student by their ID
     *
     * @param  mixed $studentId
     * @return void
     */
    public function delete($studentId)
    {
        return $this->studentService->deleteStudent($studentId);
    }
}
