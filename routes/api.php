<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizSubmissionController;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\ResultController;

/*
|----------------------------------------------------------------------
| API Routes
|----------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Quiz and result routes
    Route::apiResource('quizzes', QuizController::class);
    Route::apiResource('results', ResultController::class);

    // Add this endpoint to get user details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Token creation route (protected)
    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);
        return ['token' => $token->plainTextToken];
    });
});
