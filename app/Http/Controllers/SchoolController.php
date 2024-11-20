<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Models\{User};
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    function index(Request $request)
    {
        $query = User::school()->with('role', 'schoolDetails');

        // Apply filters
        if ($request->filled('filter')) {
            $filter = $request->filter;

            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter . '%')
                    ->orWhere('email', 'like', '%' . $filter . '%')
                    ->orWhere('status', 'like', '%' . $filter . '%');
            });
        }

        $schools = $query->paginate(PAGINATE);

        if ($schools->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'schools not found!',
                'data' => []
            ], 200);
        }

        return response()->json(['status' => true, 'message' => 'Get schools successfully.', 'data' => $schools], 200);
    }

    public function details($id)
    {
        $school = User::school()->with('schoolDetails')->find($id);

        if (!$school) {
            return response()->json(['status' => false, 'message' => 'School not found!'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'School details retrieved successfully.',
            'data' => $school
        ], 200);
    }

    public function update(UpdateSchoolRequest $request, $id)
    {
        try {
            $school = User::school()->find($id);

            if (!$school) {
                return response()->json(['status' => false, 'message' => 'School not found!'], 404);
            }

            $validated = $request->validated();

            // dd($validated);

            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), 'profile-images');

                // Remove the old image
                $oldImageName = $school->getAttributes()['profile'];
                CommonHelper::deleteImageByName($oldImageName, 'profile-images');
            }

            $school->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'School updated successfully.',
                'data' => $school
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while updating the school.', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $school = User::find($id);

            if (!$school) {
                return response()->json(['status' => false, 'message' => 'School not found!'], 404);
            }

            $school->delete();

            return response()->json([
                'status' => true,
                'message' => 'School and associated user deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting the school.', 'error' => $e->getMessage()], 500);
        }
    }
}
