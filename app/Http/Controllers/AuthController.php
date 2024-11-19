<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\School\SchoolRegisterRequest;
use App\Http\Requests\Student\StudentRegisterRequest;
use App\Http\Requests\Teacher\TeacherRegisterRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\{InvitationLink, Role, Teacher, User, UserOtp};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Mail, Validator};

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

            $data['role_id'] = Role::where('id', User::ROLE_STUDENT)->value('id') ?? null;

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

            $data['role_id'] = Role::where('id', User::ROLE_SCHOOL)->value('id') ?? null;

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

    public function teacherRegister(TeacherRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Create the user (teacher role)
            $teacherRoleId = Role::where('id', User::ROLE_TEACHER)->value('id');

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $teacherRoleId,
                'status' => User::ACTIVE
            ]);

            // Create the teacher profile
            Teacher::create([
                'user_id' => $user->id,
                'school_id' => decrypt($data['token']), // teacher will assigned to this school
                'experience' => $data['experience'] ?? null
            ]);

            // Mark the invitation as registered
            InvitationLink::where('email', $data['email'])->update([
                'status' => InvitationLink::REGISTERED,
                'accepted_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Teacher registered successfully.',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Getting error during teacher registration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgot(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users,email'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $otp = random_int(0000, 9999);

            UserOtp::updateOrCreate(['email' => $request->email], [
                'otp' => $otp
            ]);

            // Send the OTP via email
            Mail::to($request->email)->send(new ForgotPasswordMail($otp));

            return response()->json(['status' => true, 'message' => 'Forgot successfully send OTP on your registered email', 'otp' => $otp], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'message' => 'Error while forget password', 'error' => $e->getMessage()], 500);
        }
    }

    // verify the OTP after forgot password
    public function verifyOTPAfterForgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'otp'   => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $checkOTP = UserOtp::where(['email' => $request->email, 'otp' => $request->otp])->first();

        if (!$checkOTP) {
            return response()->json(['status' =>  false, 'message' => 'OTP is invalid'], 403);
        }

        $checkOTP->update([
            'verified' => true
        ]);

        return response()->json(['status' =>  true, 'message' => 'OTP verified successfully'], 201);
    }

    // reset password after the forgotten
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users,email',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $userOtp = UserOtp::where('email', $request->email)->where('verified', true)->first();

            if (!$userOtp) {
                return response()->json(['status' => false, 'message' => 'OTP is not verified. Please verify the OTP before reseting password'], 400);
            }

            $user = User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);

            // delete the otp data from OTP table
            if ($user) {
                $userOtp->delete();
            }

            return response()->json(['status' => true, 'message' => 'The password has been reset successfully.'], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'message' => 'Error while reseting password', 'error' => $e->getMessage()], 500);
        }
    }
}
