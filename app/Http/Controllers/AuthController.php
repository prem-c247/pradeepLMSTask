<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\School\SchoolRegisterRequest;
use App\Http\Requests\Student\StudentRegisterRequest;
use App\Models\{Role, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            // Attempt login with credentials
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
            }

            // Retrieve the authenticated user
            $user = Auth::user();

            // Check if user status is PENDING or INACTIVE
            if (in_array($user->status, [User::PENDING, User::INACTIVE])) {
                auth()->logout();
                $message = $user->status == User::PENDING
                    ? 'Your account is pending. Please contact admin.'
                    : 'Your account is inactive. Please contact admin.';

                return response()->json(['status' => false, 'message' => $message], 400);
            }

            // create the auth token
            $token = $user->createToken('login_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Getting error while login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function studentRegister(StudentRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['role_id'] = Role::where('name', User::ROLE_STUDENT)->value('id') ?? null;

            $student = User::create($data);

            $data['roll_number'] = random_int(100000, 999999);

            $student->studentDetails()->create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Student registration successful.',
                'data' => $student,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error during registration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function schoolRegister(SchoolRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['role_id'] = Role::where('name', User::ROLE_STUDENT)->value('id') ?? null;

            $school = User::create($data);

            $school->schoolDetails()->create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'School registration successful. Please wait for admin approval.',
                'data' => $school,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error during registration: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function teacherRegister(){
        
    }
}
