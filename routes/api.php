<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParentReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Parent Reports API
Route::middleware(['auth:web'])->group(function () {
    Route::get('/parent/reports', [ParentReportController::class, 'getReports']);
    Route::get('/parent/reports/debug', [ParentReportController::class, 'debugReports']);
});