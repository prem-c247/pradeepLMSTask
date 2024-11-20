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
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index']);
        Route::get('{id}', [TeacherController::class, 'details']);
        Route::post('{id}', [TeacherController::class, 'update']);
        Route::delete('{id}', [TeacherController::class, 'delete']);
    });

    // Send invite link to teacher
    Route::post('send-invite-link', [TeacherController::class, 'SendInviteLinkToTeacher']);

    // School
    Route::prefix('schools')->group(function () {
        Route::get('/', [SchoolController::class, 'index']);
        Route::get('{id}', [SchoolController::class, 'details']);
        Route::post('{id}', [SchoolController::class, 'update']);
        Route::delete('{id}', [SchoolController::class, 'delete']);
    });

    // Student
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('{id}', [StudentController::class, 'details']);
        Route::post('{id}', [StudentController::class, 'update']);
        Route::delete('{id}', [StudentController::class, 'delete']);
    });

    // User modification requests
    Route::prefix('modification-request')->group(function () {
        Route::get('/', [UserModificationController::class, 'index']);
        Route::post('/create', [UserModificationController::class, 'createRequest']);
        Route::post('/approved/{id}', [UserModificationController::class, 'approvedRequest']);
    });
});
