<?php

use App\Http\Controllers\{AuthController, ProfileController, SchoolController, StudentController, TeacherController, UserController, UserModificationController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// If user trying to access auth routes it given response
Route::get('login', function () {
    return response()->json(['status' => false, 'message' => 'Unauthenticated']);
})->name('login');


Route::post('login', [AuthController::class, 'login']);
Route::post('register-school', [AuthController::class, 'schoolRegister']);
Route::post('register-student', [AuthController::class, 'studentRegister']);
Route::post('register-teacher', [AuthController::class, 'teacherRegister']);
Route::post('forgot-password', [AuthController::class, 'forgot']);
Route::post('verify-otp', [AuthController::class, 'verifyOTPAfterForgot']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);


// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('get-profile', [ProfileController::class, 'getProfile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);

    Route::post('update-user-status', [UserController::class, 'UpdateUserStatus']);

    // Teachers
    Route::get('teachers', [TeacherController::class, 'index']);
    Route::post('send-invite-link', [TeacherController::class, 'SendInviteLinkToTeacher']);

    // Schools
    Route::get('schools', [SchoolController::class, 'index']);
    Route::get('schools/{id}', [SchoolController::class, 'details']);
    Route::post('schools/{id}', [SchoolController::class, 'update']);
    Route::delete('schools/{id}', [SchoolController::class, 'delete']);

    // Student
    Route::get('students', [StudentController::class, 'index']);

    Route::post('create-modification-request', [UserModificationController::class, 'createRequest']);
});
