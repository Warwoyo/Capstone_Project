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


Route::get('/orangtua', [DashboardController::class, 'indexParent'])->name('orangtua.index');
Route::get('/orangtua/anak/data-anak', [DashboardController::class, 'childrenParent'])->name('orangtua.children');
Route::get('/orangtua/anak/observasi', [DashboardController::class, 'observationParent'])->name('orangtua.observation');
Route::get('/orangtua/anak/jadwal', [DashboardController::class, 'scheduleParent'])->name('orangtua.schedule');
Route::get('/orangtua/anak/presensi', [DashboardController::class, 'attendanceParent'])->name('orangtua.attendance');
Route::get('/orangtua/anak/riwayat-pengumuman', [DashboardController::class, 'announcementParent'])->name('orangtua.announcement');



// Route::get('/classroom/{class}/{tab}/create', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-schedule-create');

Route::get('/testing', function () {
    return view('index22');
})->name('index-test');


Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.student-detail');

Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');

Route::get('/classroom', [ClassroomController::class, 'index'])->name('Classroom.index');


