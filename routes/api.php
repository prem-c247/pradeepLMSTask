<?php

use App\Http\Controllers\{AuthController, ProfileController, TeacherController};
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


// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('get-profile', [ProfileController::class, 'getProfile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);

    Route::post('send-invite-link', [TeacherController::class, 'SendInviteLinkToTeacher']);
});
