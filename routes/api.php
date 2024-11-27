<?php

use App\Http\Controllers\{
    AuthController,
    ExamController,
    ProfileController,
    QuestionController,
    SchoolController,
    StudentController,
    SubjectController,
    TeacherController,
    UserController,
    UserModificationController
};
use Illuminate\Support\Facades\Route;


// If user trying to access auth routes it given response
Route::get('login', function () {
    return response()->json(['status' => false, 'message' => 'Unauthenticated']);
})->name('login');

Route::post('login', [AuthController::class, 'login']);
Route::post('register-school', [AuthController::class, 'registerSchool']);
Route::post('register-student', [AuthController::class, 'registerStudent']);
Route::post('register-teacher', [AuthController::class, 'registerTeacher']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOTPAfterForgot']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('get-profile', [ProfileController::class, 'getProfile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
    Route::post('update-user-status', [UserController::class, 'UpdateUserStatus']); // update user status

    // Teachers
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index']);
        Route::get('{teacherId}', [TeacherController::class, 'details']);
        Route::post('{teacherId}', [TeacherController::class, 'update']);
        Route::delete('{teacherId}', [TeacherController::class, 'delete']);
        Route::post('/invitation-link/send', [TeacherController::class, 'SendInviteLinkToTeacher']); // Send invite link to teacher
    });

    // School
    Route::prefix('schools')->group(function () {
        Route::get('/', [SchoolController::class, 'index']);
        Route::get('{schoolId}', [SchoolController::class, 'details']);
        Route::post('{schoolId}', [SchoolController::class, 'update']);
        Route::delete('{schoolId}', [SchoolController::class, 'delete']);
    });

    // Student
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('{studentId}', [StudentController::class, 'details']);
        Route::post('{studentId}', [StudentController::class, 'update']);
        Route::delete('{studentId}', [StudentController::class, 'delete']);
    });

    // User modification requests
    Route::prefix('modification-request')->group(function () {
        Route::get('/', [UserModificationController::class, 'index']);
        Route::post('/create', [UserModificationController::class, 'createRequest']);
        Route::post('/approved/{requestId}', [UserModificationController::class, 'approvedRequest']);
    });

    // Subjects
    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::get('/{subjectId}', [SubjectController::class, 'show']);
        Route::put('/{subjectId}', [SubjectController::class, 'update']);
        Route::delete('/{subjectId}', [SubjectController::class, 'delete']);
    });

    // Questions
    Route::prefix('questions')->group(function () {
        Route::get('/{subjectId}', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::get('/details/{questionId}', [QuestionController::class, 'show']);
        Route::put('/{questionId}', [QuestionController::class, 'update']);
        Route::delete('/{questionId}', [QuestionController::class, 'delete']);
    });

    // Exams
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::get('/attempt/{subjectId}', [ExamController::class, 'attemptExam']);
        Route::get('/{examId}', [ExamController::class, 'show']);
        Route::post('/', [ExamController::class, 'storeExam']);
    });
});

// If routes not found
Route::fallback(function () {
    return response404(__('message.not_found', ['name' => __('message.route')]));
});
