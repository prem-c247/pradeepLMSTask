<?php

namespace App\Http\Controllers;

use App\Models\{User};
use Illuminate\Http\Request;

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
}
