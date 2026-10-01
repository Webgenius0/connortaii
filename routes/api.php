<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\DynamicPageController;
use App\Http\Controllers\Api\HealthSafetyItemController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\SystemSettingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthSafetyResponseController;
use App\Http\Controllers\Api\InspectionCheckpointController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\InspectionReportController;
use App\Http\Controllers\Api\InspectionSectionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);
Route::get('reports/{reportId}/download', [InspectionReportController::class, 'download'])->name('reports.download');
Route::get('pages', [DynamicPageController::class, 'index']);
Route::get('pages/{slug}', [DynamicPageController::class, 'show']);
Route::controller(RegisterController::class)->prefix('users/register')->group(function () {
    // User Register
    Route::post('/', 'userRegister');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');
    //email exists check
    Route::post('/email-exists', 'emailExists');
});
Route::controller(LoginController::class)->prefix('users/login')->group(function () {

    // User Login
    Route::post('/', 'userLogin');

    // Verify Email
    Route::post('/email-verify', 'emailVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    //Reset Password
    Route::post('/reset-password', 'resetPassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(SystemSettingController::class)->group(function () {
        Route::get('/site-settings', 'index');
    });

    Route::controller(SocialMediaController::class)->group(function () {
        Route::get('/social-links', 'index');
    });

    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('/', 'userDetails');
        Route::post('/update', 'updateUser');
        Route::post('/update-password', 'updatePassword');
        Route::delete('/delete-account', 'deleteAccount');
        Route::post('/logout', 'logoutUser');
    });


});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('inspections', [InspectionController::class, 'index']);
    Route::post('inspections', [InspectionController::class, 'store']);
    Route::get('inspections/{id}', [InspectionController::class, 'show']);
    Route::post('inspections/{id}/stop', [InspectionController::class, 'stop']);
    Route::post('inspections/{id}/complete', [InspectionController::class, 'complete']);

    Route::put('health-safety-responses/{id}', [HealthSafetyResponseController::class, 'update']);

    Route::get('sections/{id}', [InspectionSectionController::class, 'show']);
    Route::put('sections/{id}/accessibility', [InspectionSectionController::class, 'updateAccessibility']);

    Route::post('checkpoints/{id}', [InspectionCheckpointController::class, 'update']); // POST, কারণ photo file আছে
    Route::delete('checkpoint-photos/{photoId}', [InspectionCheckpointController::class, 'destroyPhoto']);

    Route::get('inspections/{inspectionId}/sections', [InspectionSectionController::class, 'indexByInspection']);
    Route::get('checkpoints/{id}', [InspectionCheckpointController::class, 'show']);

    Route::get('health-safety-items', [HealthSafetyItemController::class, 'index']);
    Route::post('health-safety-items', [HealthSafetyItemController::class, 'store']);
    Route::get('health-safety-items/{id}', [HealthSafetyItemController::class, 'show']);
    Route::put('health-safety-items/{id}', [HealthSafetyItemController::class, 'update']);
    Route::delete('health-safety-items/{id}', [HealthSafetyItemController::class, 'destroy']);
    
    Route::get('reports', [InspectionReportController::class, 'myReports']);
    Route::post('inspections/{id}/report', [InspectionReportController::class, 'generate']);
    Route::get('inspections/{id}/reports', [InspectionReportController::class, 'index']);

});
