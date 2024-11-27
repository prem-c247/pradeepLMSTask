<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StudentController extends Controller
{    
    /**
     * index: Get the all students along with details
     * Can apply multiple filter like (name, email, phone, status)
     *
     * @param  mixed $request
     * @return void
     */
    function index(Request $request)
    {
        $query = User::student()->with('role', 'studentDetails.school.schoolDetails');
        // Apply filters
        if ($request->filled('filter')) {
            $filter = $request->filter;
            $columns = ['last_name', 'status', 'email', 'phone'];
            $query->where(function ($subQuery) use ($filter, $columns) {
                $subQuery->where('first_name', 'like', '%' . $filter . '%');
                foreach ($columns as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $filter . '%');
                }
            });
        }

        $students = $query->paginate(PAGINATE);
        if ($students->isEmpty()) {
            return $this->notFound('student');
        }
        return response200(__('message.fetched', ['name' => __('message.student')]), $students);
    }
    
    /**
     * details: Get student details by their ID.
     *
     * @param  mixed $studentId
     * @return void
     */
    public function details($studentId)
    {
        $student = User::student()->with('studentDetails')->find($studentId);
        if (!$student) {
            return $this->notFound('student');
        }
        return response200(__('message.fetched', ['name' => __('message.student')]), $student);
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
        try {
            $student = User::student()->find($studentId);
            if (!$student) {
                return $this->notFound('student');
            }
            $validated = $request->validated();
            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), PROFILE_IMAGE_DIR);

                // Remove the old image
                $oldImageName = $student->getAttributes()['profile'];
                if ($oldImageName) {
                    CommonHelper::deleteImageByName($oldImageName, PROFILE_IMAGE_DIR);
                }
            }
            // Update the student's basic information
            $student->update(Arr::only($validated, ['name', 'email', 'phone', 'profile', 'status']));
            // Update the student's details
            $student->studentDetails()->update(Arr::only($validated, ['roll_number', 'parents_name']));
            
            // get the address validation rule array's keys and update the address
            $addressValidationArray = CommonHelper::getAddressValidationRules();
            $addressValidationArrayKeys = array_keys($addressValidationArray);
            $student->addresses()->update(Arr::only($validated, $addressValidationArrayKeys));

            // load the all related data
            $student = $student->load('studentDetails', 'addresses');
            return response200(__('message.updated', ['name' => __('message.student')]), $student);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }
    
    /**
     * delete: Delete student by their ID
     *
     * @param  mixed $studentId
     * @return void
     */
    public function delete($studentId)
    {
        try {
            $student = User::student()->find($studentId);
            if (!$student) {
                return $this->notFound('student');
            }

            $student->delete();
            return response200(__('message.deleted', ['name' => __('message.student')]), $student);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.deletion')]), $e->getMessage());
        }
    }
}
