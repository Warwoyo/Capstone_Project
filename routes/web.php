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
    ParentRegisterController,
    AttendanceController
};


/*
|--------------------------------------------------------------------------
| PUBLIC AREA
|--------------------------------------------------------------------------
*/

// — Form registrasi orang-tua
Route::get ('/parent/register', [ParentRegisterController::class, 'create'])->name('parent.register.form');
Route::post('/parent/register', [ParentRegisterController::class, 'store'])->name('parent.register');

/*
|--------------------------------------------------------------------------
| GUEST-ONLY (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get ('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA (semua user yang sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* ── Auth misc ────────────────────────────────────────────────────── */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get ('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    /* ── ANNOUNCEMENTS ────────────────────────────────────────────────── */
    // A. Viewable by **all** logged-in users
    Route::resource('classrooms.announcements', AnnouncementController::class)
        ->only(['index', 'show'])
        ->shallow();   // contoh URL: /classrooms/1/announcements  &  /announcements/9

    // B. Manageable only by **admin & teacher**
    Route::middleware('role:admin,teacher')->group(function () {
        // Route::resource('classrooms.announcements', AnnouncementController::class)
        //     ->only(['store', 'update', 'destroy'])
        //     ->shallow();
        Route::resource('classrooms.announcements', AnnouncementController::class)
            ->only(['store','index','show'])
            ->shallow();
        Route::resource('classrooms.announcements', AnnouncementController::class)
            ->only(['store','destroy','index','show'])
            ->shallow();
    });

    /* ── CLASSROOMS ───────────────────────────────────────────────────── */
    // A. Guru & Admin = full CRUD
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

        // Jadwal
        Route::resource('classrooms.schedules', ScheduleController::class)->shallow();

        // Admin panel
        Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');
    });

    // B. Admin, Guru, dan Orang-tua = hak lihat
    Route::middleware('role:admin,teacher,parent')->group(function () {
        Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');

    });

    /* ── CLASSROOM TAB DETAIL (tetap) ─────────────────────────────────── */
    Route::get('/classroom/{classroom}/{tab}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tab');
    Route::get('/classroom/{class}/{tab}/{id}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.tabs-detail');
    Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])->name('classroom.student-detail');
    Route::get('/classrooms/{class}/peserta', [ClassroomController::class, 'studentsTab'])->name('classroom.tab.peserta');

    /* ── STUDENTS CRUD (khusus di dalam kelas) ───────────────────────── */
    Route::resource('students', StudentController::class)->only(['index','create','show','edit']);
    Route::post('/students',                      [StudentController::class,'store'])->name('students.store');
    Route::put ('/students/{student}',            [StudentController::class,'update'])->name('students.update');
    Route::delete('/classroom/{class}/students/{student}', [StudentController::class,'destroy'])->name('students.destroy');
    Route::post  ('/classrooms/{class}/students', [StudentController::class,'store'])->name('students.store.inside');
    Route::post  ('classroom/{class}/students', [StudentController::class, 'store'])->name('students.store');
    Route::put   ('/classroom/{class}/students/{student}', [StudentController::class,'update'])->name('students.update.inside');
    Route::get ('/attendance/{classroom}', [AttendanceController::class, 'index'])
         ->name('attendance.index');
    Route::post('/attendance/{classroom}', [AttendanceController::class, 'store'])
     ->name('attendance.store');
    Route::get('/kelas/{classroom}/presensi/ajax', [AttendanceController::class, 'ajax']);
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.stores');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::resource('schedules', ScheduleController::class);
    
    // Additional classroom-specific schedule route
    Route::post('classroom/{classroom}/jadwal', [ScheduleController::class, 'store'])
        ->name('classroom.schedules.store');
    



});

/*
|--------------------------------------------------------------------------
| FALLBACK & TESTING
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.index')
        : redirect()->route('login');
});

Route::get('/testing', fn () => view('index22'))->name('index-test');

/* Orang-tua dashboard sample */
Route::get('/orangtua',                       [DashboardController::class, 'indexParent']      )->name('orangtua.index');
Route::get('/orangtua/anak/data-anak',        [DashboardController::class, 'childrenParent']   )->name('orangtua.children');
Route::get('/orangtua/anak/observasi',        [DashboardController::class, 'observationParent'])->name('orangtua.observation');
Route::get('/orangtua/anak/jadwal',           [DashboardController::class, 'scheduleParent']   )->name('orangtua.schedule');
Route::get('/orangtua/anak/presensi',         [DashboardController::class, 'attendanceParent'] )->name('orangtua.attendance');
Route::get('/orangtua/anak/riwayat-pengumuman',[DashboardController::class, 'announcementParent'])->name('orangtua.announcement');


//testing syllabus page
Route::get('/testingx', function () {
    return view('testingx');
});