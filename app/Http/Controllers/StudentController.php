<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\{User};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    function index(Request $request)
    {
        $query = User::student()->with('role', 'studentDetails.school.schoolDetails');

        // Apply filters
        if ($request->filled('filter')) {
            $filter = $request->filter;

            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter . '%')
                    ->orWhere('email', 'like', '%' . $filter . '%')
                    ->orWhere('status', 'like', '%' . $filter . '%');
            });
        }

        // Filter by the school name
        if ($request->filled('school_name')) {
            $query->whereHas('studentDetails.school', function ($q) use ($request) {
                $q->where('name', 'like', "%$request->school_name%");
            });
        }

        $students = $query->paginate(PAGINATE);

        if ($students->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'students not found!',
                'data' => []
            ], 200);
        }

        return response()->json(['status' => true, 'message' => 'Get students successfully.', 'data' => $students], 200);
    }

    public function details($id)
    {
        $student = User::student()->with('studentDetails')->find($id);

        if (!$student) {
            return response()->json(['status' => false, 'message' => 'student not found!'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'student details retrieved successfully.',
            'data' => $student
        ], 200);
    }

    public function update(UpdateStudentRequest $request, $id)
    {
        try {
            $student = User::student()->find($id);

            if (!$student) {
                return response()->json(['status' => false, 'message' => 'student not found!'], 404);
            }

            $validated = $request->validated();

            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), 'profile-images');

                // Remove the old image
                $oldImageName = $student->getAttributes()['profile'];
                CommonHelper::deleteImageByName($oldImageName, 'profile-images');
            }

            DB::beginTransaction();

            // Update the student's basic information
            $student->update(Arr::only($validated, ['name', 'email', 'phone', 'profile', 'status']));

            // Update the student's details
            $student->studentDetails()->update(Arr::only($validated, ['roll_number', 'parents_name']));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'student updated successfully.',
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => 'An error occurred while updating the student.', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $student = User::student()->find($id);

            if (!$student) {
                return response()->json(['status' => false, 'message' => 'student not found!'], 404);
            }

            $student->delete();

            return response()->json([
                'status' => true,
                'message' => 'student and associated user deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the student.', 'error' => $e->getMessage()], 500);
        }
    }
}
