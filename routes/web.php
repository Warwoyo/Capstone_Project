<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register Orang Tua
    Route::get('/parent/register',  [AuthController::class, 'showParentRegister'])->name('parent.register');
    Route::post('/parent/register', [AuthController::class, 'parentRegister']);
});


Route::post('/students', [StudentController::class,'store'])
         ->middleware('role:admin,teacher')
         ->name('students.store');

         
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Simpan data siswa — hanya admin & guru
    Route::post('/students', [StudentController::class, 'store'])
         ->middleware('role:admin,teacher')
         ->name('students.store');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
