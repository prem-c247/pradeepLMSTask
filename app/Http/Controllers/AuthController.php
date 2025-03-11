<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\{
    ForgotPasswordRequest,
    LoginRequest,
    ResetPasswordRequest,
    VerifyOTPRequest
};
use App\Http\Requests\School\SchoolRegisterRequest;
use App\Http\Requests\Student\StudentRegisterRequest;
use App\Http\Requests\Teacher\TeacherRegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * login: Validate the login with user credentials (email and password)
     * After the success login user will get the authorization bearer token for the authentication
     * @param  mixed $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    /**
     * registerStudent: Register students, Assign the student role
     * Generate unique roll number for each student
     * @param  mixed $request
     * @return void
     */
    public function registerStudent(StudentRegisterRequest $request)
    {
        return $this->authService->registerStudent($request);
    }

    /**
     * registerSchool: Registration of school types of user
     *
     * @param  mixed $request
     * @return void
     */
    public function registerSchool(SchoolRegisterRequest $request)
    {
        return $this->authService->registerSchool($request);
    }

    /**
     * registerTeacher: Registration of teacher types of user
     *
     * @param  mixed $request
     * @return void
     */
    public function registerTeacher(TeacherRegisterRequest $request)
    {
        return $this->authService->registerTeacher($request);
    }

    /**
     * forgotPassword: Forgot user password
     * In this process we will store the OTP alongwith email in the user_opts table
     * @param  mixed $request
     * @return void
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $this->authService->forgotPassword($request->email);
    }

    /**
     * verifyOTPAfterForgot: Verified the OTP after forgotten password  
     * after the verification we will set "verified = true" in the user_otps table
     * @param  mixed $request
     * @return void
     */
    public function verifyOTPAfterForgot(VerifyOTPRequest $request)
    {
        return $this->authService->verifyOTPAfterForgot($request->validated());
    }

    /**
     * resetPassword: Reset password after the forgotten
     * After updating password, we have removed the user otp data form user_otps table
     * @param  mixed $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->authService->resetPassword($request->validated());
    }
}
