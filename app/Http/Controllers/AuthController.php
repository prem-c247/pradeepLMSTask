<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Auth\{
    ForgotPasswordRequest,
    LoginRequest,
    ResetPasswordRequest,
    VerifyOTPRequest
};
use App\Http\Requests\School\SchoolRegisterRequest;
use App\Http\Requests\Student\StudentRegisterRequest;
use App\Http\Requests\Teacher\TeacherRegisterRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\{InvitationLink, Role, User, UserOtp};
use Illuminate\Support\Facades\{Auth, Mail};
use Exception;

class AuthController extends Controller
{
    /**
     * login: Validate the login with user credentials (email and password)
     * After the success login user will get the authorization bearer token for the authentication
     * @param  mixed $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        try {
            // Attempt login with credentials
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response401(__('message.invalid_credentials'));
            }
            // Retrieve the authenticated user
            $user = Auth::user();

            // Check if user status is PENDING or INACTIVE
            if (in_array($user->status, [User::PENDING, User::INACTIVE])) {
                auth()->logout();
                $message = $user->status === User::PENDING
                    ? __('message.account_status', ['status' => __('message.pending')])
                    : __('message.account_status', ['status' => __('message.inactive')]);

                return response400($message);
            }
            // create the auth token
            $token = $user->createToken('login_token')->plainTextToken;
            return response200(__('message.login_success'), ['token' => $token, 'data' => $user]);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.login')]), $e->getMessage());
        }
    }

    /**
     * registerStudent: Register students, Assign the student role
     * Generate unique roll number for each student
     * @param  mixed $request
     * @return void
     */
    public function registerStudent(StudentRegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['role_id'] = Role::where('id', User::ROLE_STUDENT)->value('id') ?? null;

            // upload profile image by the helper function
            if ($request->hasFile('profile_image')) {
                $validatedData['profile'] = CommonHelper::fileUpload($request->file('profile_image'), PROFILE_IMAGE_DIR);
            }
            $student = User::create($validatedData);
            $validatedData['roll_number'] = CommonHelper::generateUniqueNumber(10);
            $student->studentDetails()->create($validatedData);
            $student->addresses()->create($validatedData);

            // load the details and addresses
            $student = $student->load('studentDetails', 'addresses');
            return response201(__('message.registered', ['name' => __('message.student')]), $student);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.registration')]), $e->getMessage());
        }
    }

    /**
     * registerSchool: Registration of school types of user
     *
     * @param  mixed $request
     * @return void
     */
    public function registerSchool(SchoolRegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['role_id'] = Role::where('id', User::ROLE_SCHOOL)->value('id') ?? null;

            // upload profile image by the helper function
            if ($request->hasFile('profile_image')) {
                $validatedData['profile'] = CommonHelper::fileUpload($request->file('profile_image'), PROFILE_IMAGE_DIR);
            }
            $school = User::create($validatedData);
            // store additional details
            $school->schoolDetails()->create($validatedData);
            // store the address
            $school->addresses()->create($validatedData);

            // load the details and addresses
            $school = $school->load('schoolDetails', 'addresses');
            return response201(__('message.registered', ['name' => __('message.school')]), $school);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.registration')]), $e->getMessage());
        }
    }

    /**
     * registerTeacher: Registration of teacher types of user
     *
     * @param  mixed $request
     * @return void
     */
    public function registerTeacher(TeacherRegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            // Create the user (teacher role)
            $teacherRoleId = Role::where('id', User::ROLE_TEACHER)->value('id') ?? 0;
            $validatedData['role_id'] = $teacherRoleId;
            $validatedData['status'] = User::ACTIVE;
            $validatedData['school_id'] = decrypt($validatedData['token']); // teacher will assigned to this school

            // remove the token form the validatedData array
            unset($validatedData['token']);

            // upload profile image by the helper function
            if ($request->hasFile('profile_image')) {
                $validatedData['profile'] = CommonHelper::fileUpload($request->file('profile_image'), PROFILE_IMAGE_DIR);
            }
            $teacher = User::create($validatedData);
            // Create the teacher profile
            $teacher->teacherDetails()->create($validatedData);
            // store the address
            $teacher->addresses()->create($validatedData);

            // load the details and addresses
            $teacher = $teacher->load('teacherDetails', 'addresses');

            // Mark the invitation as registered
            InvitationLink::where('email', $validatedData['email'])->update([
                'status' => InvitationLink::REGISTERED,
                'accepted_at' => now()
            ]);
            return response201(__('message.registered', ['name' => __('message.teacher')]), $teacher);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.registration')]), $e->getMessage());
        }
    }

    /**
     * forgotPassword: Forgot user password
     * In this process we will store the OTP alongwith email in the user_opts table
     * @param  mixed $request
     * @return void
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $otp = CommonHelper::generateUniqueNumber(4);
            UserOtp::updateOrCreate(['email' => $request->email], [
                'otp' => $otp
            ]);

            // Send the OTP via email
            Mail::to($request->email)->send(new ForgotPasswordMail($otp));
            return response200(__('message.forgot'), ['otp' => $otp]);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.forgot_password')]), $e->getMessage());
        }
    }

    /**
     * verifyOTPAfterForgot: Verified the OTP after forgotten password  
     * after the verification we will set "verified = true" in the user_otps table
     * @param  mixed $request
     * @return void
     */
    public function verifyOTPAfterForgot(VerifyOTPRequest $request)
    {
        $checkOTP = UserOtp::where(['email' => $request->email, 'otp' => $request->otp])->first();
        if (!$checkOTP) {
            return response400(__('message.invalid_otp'));
        }
        $checkOTP->update([
            'verified' => true
        ]);
        return response200(__('message.verified_otp'));
    }

    /**
     * resetPassword: Reset password after the forgotten
     * After updating password, we have removed the user otp data form user_otps table
     * @param  mixed $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $userOtp = UserOtp::where('email', $request->email)->where('verified', true)->first();
            if (!$userOtp) {
                return response400(__('message.otp_not_verified'));
            }
            $user = User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);

            // delete the otp data from OTP table
            if ($user) {
                $userOtp->delete();
            }
            return response200(__('message.password_reset_success'));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.reset_password')]), $e->getMessage());
        }
    }
}
