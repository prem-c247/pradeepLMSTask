<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\{User};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StudentController extends Controller
{
    function index(Request $request)
    {
        $query = User::student()->with('role', 'studentDetails.school.schoolDetails');
        // Apply filters
        if ($request->filled('filter')) {
            $filter = $request->filter;

            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter . '%')
                    ->orWhere('last_name', 'like', '%' . $filter . '%')
                    ->orWhere('email', 'like', '%' . $filter . '%')
                    ->orWhere('status', 'like', '%' . $filter . '%');
            });
        }
        $students = $query->paginate(PAGINATE);
        if ($students->isEmpty()) {
            return $this->notFound('student');
        }
        return response200(__('message.fetched', ['name' => __('message.student')]), $students);
    }

    public function details($id)
    {
        $student = User::student()->with('studentDetails')->find($id);
        if (!$student) {
            return $this->notFound('student');
        }
        return response200(__('message.fetched', ['name' => __('message.student')]), $student);
    }

    public function update(UpdateStudentRequest $request, $id)
    {
        try {
            $student = User::student()->find($id);
            if (!$student) {
                return $this->notFound('student');
            }
            $validated = $request->validated();
            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), 'profile-images');
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

    public function delete($id)
    {
        try {
            $student = User::student()->find($id);
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
