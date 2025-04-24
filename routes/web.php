<?php
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::get('/register', function () {
    return view('Auth.register');
})->name('register');

Route::get('/login', function () {
    return view('Auth.login');
})->name('login');

Route::get('/classroom', [ClassroomController::class, 'index'])->name('Classroom.index');

Route::get('/sidebar', function () {
    return view('layouts.dashboard');
})->name('sidebar');