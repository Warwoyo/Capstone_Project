<?php
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ObservatianController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route::middleware('guest')->group(function () {
//     // Login
//     Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login']);

//     // Register Orang Tua
//     Route::get('/parent/register',  [AuthController::class, 'showParentRegister'])->name('parent.register');
//     Route::post('/parent/register', [AuthController::class, 'parentRegister']);
// });

Route::get('/classroom/11', function () {
    return view('Classroom.classroom-detail');
})->name('classroom-detail');

Route::get('/classroom/{class}/{tab}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tab');
// Route::get('/classroom/schedule', [ScheduleController::class, 'index'])->name('schedule');
Route::get('/classroom/{class}/{tab}/{id}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-detail');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');


// Route::get('/classroom/{class}/{tab}/create', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-schedule-create');

Route::get('/testing', function () {
    return view('index22');
})->name('index-test');


Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.student-detail');

Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');

Route::get('/classroom', [ClassroomController::class, 'index'])->name('Classroom.index');