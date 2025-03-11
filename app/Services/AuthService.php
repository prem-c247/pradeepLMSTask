<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Mail\ForgotPasswordMail;
use App\Models\{InvitationLink, Role, User, UserOtp};
use Exception;
use Illuminate\Support\Facades\{Auth, Mail};

class AuthService
{
    public function login(array $credentials)
    {
        try {
            // Attempt login with credentials
            if (!Auth::attempt($credentials)) {
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

            // Create the auth token
            $token = $user->createToken('login_token')->plainTextToken;
            return response200(__('message.login_success'), ['token' => $token, 'data' => $user]);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.login')]), $e->getMessage());
        }
    }

    public function registerStudent(object $request)
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

    public function registerSchool(object $request)
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

    public function registerTeacher(object $request)
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

    public function forgotPassword(string $email)
    {
        try {
            $otp = CommonHelper::generateUniqueNumber(4);
            UserOtp::updateOrCreate(['email' => $email], [
                'otp' => $otp
            ]);

            // Send the OTP via email
            Mail::to($email)->send(new ForgotPasswordMail($otp));
            return response200(__('message.forgot'), ['otp' => $otp]);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.forgot_password')]), $e->getMessage());
        }
    }

    public function verifyOTPAfterForgot(array $validatedData)
    {
        try {
            $checkOTP = UserOtp::where(['email' => $validatedData['email'], 'otp' =>  $validatedData['otp']])->first();
            if (!$checkOTP) {
                return response400(__('message.invalid_otp'));
            }
            $checkOTP->update([
                'verified' => true
            ]);
            return response200(__('message.verified_otp'));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.otp_verification')]), $e->getMessage());
        }
    }

    public function resetPassword(array $validatedData)
    {
        try {
            $userOtp = UserOtp::where('email', $validatedData['email'])->where('verified', true)->first();
            if (!$userOtp) {
                return response400(__('message.otp_not_verified'));
            }
            $user = User::where('email', $validatedData['email'])->update(['password' => bcrypt($$validatedData['password'])]);

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
