<?php
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// GUEST - Belum login
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/parent/register',  [AuthController::class, 'showParentRegister'])->name('parent.register');
    Route::post('/parent/register', [AuthController::class, 'parentRegister']);
});

// AUTH - Sudah login
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Redirect Berdasarkan Role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Admin & Teacher Resource
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('classrooms', ClassroomController::class)->names([
            'index'   => 'Classroom.index',
            'create'  => 'Classroom.create',
            'store'   => 'Classroom.store',
            'show'    => 'Classroom.show',
            'edit'    => 'Classroom.edit',
            'update'  => 'Classroom.update',
            'destroy' => 'Classroom.destroy',
        ]);
    });
    Route::resource('students', StudentController::class);
    // Route tambahan lainnya (classroom/tab/detail/dll)
    Route::get('/classroom/{class}/{tab}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tab');
    Route::get('/classroom/{class}/{tab}/{id}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-detail');
    Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.student-detail');

    // Admin panel
    Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');
});

// routes/web.php
Route::middleware(['auth','role:admin,teacher'])->group(function () {
    Route::resource('classrooms', ClassroomController::class);
    Route::resource('classrooms.announcements', ClassroomAnnouncementController::class)->shallow();
    Route::resource('classrooms.schedules', ScheduleController::class)->shallow();
    // dst…
});

// Orang tua -> hanya baca
// Route::middleware(['auth','role:parent'])->group(function () {
//     Route::get('dashboard', ParentDashboardController::class)->name('parent.dashboard');
// });



Route::middleware(['auth','role:admin,teacher'])->group(function () {
    // hanya store (POST) → nama otomatis "students.store"
    Route::post('/students', [StudentController::class, 'store'])
         ->name('students.store');
});

// Route::get('/classroom/11', function () {
//     return view('Classroom.classroom-detail');
// })->name('classroom-detail');

// Route::get('/classroom/{class}/{tab}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tab');
// // Route::get('/classroom/schedule', [ScheduleController::class, 'index'])->name('schedule');
// Route::get('/classroom/{class}/{tab}/{id}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-detail');

Route::get('/classroom/{class}/{tab}', [ClassroomController::class, 'showClassroomDetail'])
      ->name('classroom.tab');

Route::get('/testing', function () {
    return view('index22');
})->name('index-test');


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.index')
        : redirect()->route('login');
});


Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.student-detail');



Route::resource('students', StudentController::class);

// routes/web.php
Route::middleware(['auth','role:admin,teacher,parent'])->group(function () {
    Route::get('/classrooms', [ClassroomController::class, 'index'])
        ->name('classrooms.index');
});
