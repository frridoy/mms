<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LookupController;
use App\Http\Controllers\API\MonthlyExpenseController;
use App\Http\Controllers\API\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();

    });
     Route::get('/test', function (Request $request) {
         return response()->json([
             'message' => 'authenticated'
         ]);
    });

    //team management
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/{team}', [TeamController::class, 'show']);
    Route::put('/teams/{team}', [TeamController::class, 'update']);

    //lookup management
    Route::get('/lookups', [LookupController::class, 'index']);
    Route::post('/lookups', [LookupController::class, 'store']);
    Route::get('/lookups/{id}', [LookupController::class, 'show']);
    Route::put('/lookups/{id}', [LookupController::class, 'update']);
    Route::delete('/lookups/{id}', [LookupController::class, 'destroy']);

    //monthly expenses
    Route::get('/monthly-expenses', [MonthlyExpenseController::class, 'index']);
    Route::post('/monthly-expenses', [MonthlyExpenseController::class, 'store']);
    Route::get('/monthly-expenses/{id}', [MonthlyExpenseController::class, 'show']);
    Route::put('/monthly-expenses/{id}', [MonthlyExpenseController::class, 'update']);
    Route::delete('/monthly-expenses/{id}', [MonthlyExpenseController::class, 'destroy']);
});

