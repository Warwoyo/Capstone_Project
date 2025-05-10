<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AdminController,
    StudentController,
    ClassroomController,
    ScheduleController,
    AnnouncementController,
    DashboardController,
    ParentRegisterController
};

// =========================
// Public Routes
// =========================

// Form registrasi orang tua
Route::get('parent/register', [ParentRegisterController::class, 'create'])->name('parent.register.form');
Route::post('parent/register', [ParentRegisterController::class, 'store'])->name('parent.register');

// =========================
// Guest Middleware
// =========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// =========================
// Authenticated Middleware
// =========================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Role: admin, teacher
    Route::middleware('role:admin,teacher')->group(function () {

        // Classroom Resource
        Route::resource('classrooms', ClassroomController::class)->names([
            'index'   => 'Classroom.index',
            'create'  => 'Classroom.create',
            'store'   => 'Classroom.store',
            'show'    => 'Classroom.show',
            'edit'    => 'Classroom.edit',
            'update'  => 'Classroom.update',
            'destroy' => 'Classroom.destroy',
        ]);

        // Nested Resources (announcements, schedules)
        Route::resource('classrooms.announcements', AnnouncementController::class)->shallow();
        Route::resource('classrooms.schedules', ScheduleController::class)->shallow();

        // Admin panel
        Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');
    });

    // Role: admin, teacher, parent
    Route::middleware('role:admin,teacher,parent')->group(function () {
        Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    });

    // Classroom Tab Details
    Route::get('/classroom/{class}/{tab}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tab');
    Route::get('/classroom/{class}/{tab}/{id}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-detail');
    Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.student-detail');

    // Tab peserta
    Route::get('/classrooms/{class}/peserta', [ClassroomController::class, 'studentsTab'])->name('classroom.tab.peserta');

    // CRUD siswa
    Route::resource('students', StudentController::class)->only(['index', 'create', 'show', 'edit']);
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // CRUD siswa spesifik dalam kelas
    Route::post('/classrooms/{class}/students', [StudentController::class, 'store'])->name('students.store');
});

// =========================
// Fallback & Testing Routes
// =========================

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.index')
        : redirect()->route('login');
});

Route::get('/testing', function () {
    return view('index22');
})->name('index-test');
