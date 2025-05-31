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
    AttendanceController,
    ObservationController,
    TemplateController,
    ReportController
};

/*
|--------------------------------------------------------------------------
| PUBLIC AREA
|--------------------------------------------------------------------------
*/

// Form registrasi orang-tua
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
        ->shallow();

    // B. Manageable only by **admin & teacher**
    Route::middleware('role:admin,teacher')->group(function () {
        Route::resource('classrooms.announcements', AnnouncementController::class)
            ->only(['store', 'destroy'])
            ->shallow();
    });

    /* ── PARENT-ONLY ─────────────────────────────────────────────────── */
    Route::middleware('role:parent')->group(function () {
        Route::get('/orangtua/anak/data-anak',           [DashboardController::class, 'childrenParent']     )->name('orangtua.children');
        Route::get('/orangtua/anak/observasi',           [DashboardController::class, 'observationParent']  )->name('orangtua.observation');
        Route::get('/orangtua/anak/jadwal',              [DashboardController::class, 'scheduleParent']     )->name('orangtua.schedule');
        Route::get('/orangtua/anak/presensi',            [DashboardController::class, 'attendanceParent']   )->name('orangtua.attendance');
        Route::get('/orangtua/anak/riwayat-pengumuman',  [DashboardController::class, 'announcementParent'] )->name('orangtua.announcement');
        Route::get('/orangtua/anak/silabus',             [DashboardController::class, 'syllabusParent']     )->name('orangtua.syllabus');
        
        // Parents can view their children's reports
        Route::get('/orangtua/anak/rapor', [ReportController::class, 'parentView'])
            ->name('orangtua.rapor');
        Route::get('/orangtua/anak/rapor/{student}', [ReportController::class, 'parentViewDetail'])
            ->name('orangtua.rapor.detail');
    });

    /* ── ADMIN & TEACHER ONLY ─────────────────────────────────────────── */
    Route::middleware('role:admin,teacher')->group(function () {
        
        /* ── CLASSROOMS ──────────────────────────────────────────────── */
        Route::resource('classrooms', ClassroomController::class)->names([
            'index'   => 'Classroom.index',
            'create'  => 'Classroom.create',
            'store'   => 'Classroom.store',
            'show'    => 'Classroom.show',
            'edit'    => 'Classroom.edit',
            'update'  => 'Classroom.update',
            'destroy' => 'Classroom.destroy',
        ]);

        /* ── SCHEDULES ───────────────────────────────────────────────── */
        Route::resource('schedules', ScheduleController::class)
            ->only(['store', 'update', 'destroy']);
        Route::post('classroom/{classroom}/jadwal', [ScheduleController::class, 'store'])
            ->name('classroom.schedules.store');
        Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])
            ->name('schedules.edit');
        Route::get('/schedules/{schedule}/sub-themes', [ScheduleController::class, 'getSubThemes'])
            ->name('schedules.sub-themes');
        Route::get('/schedules/{schedule}/students', [ScheduleController::class, 'getStudents'])
            ->name('schedules.students');

        /* ── STUDENTS ────────────────────────────────────────────────── */
        Route::resource('students', StudentController::class)->only(['index','create','show','edit']);
        Route::post('/students', [StudentController::class,'store'])->name('students.store');
        Route::put ('/students/{student}', [StudentController::class,'update'])->name('students.update');
        Route::delete('/classroom/{class}/students/{student}', [StudentController::class,'destroy'])->name('students.destroy');
        Route::post('/classrooms/{class}/students', [StudentController::class,'store'])->name('students.store.inside');
        Route::post('classroom/{class}/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/classroom/{class}/students/{student}', [StudentController::class,'update'])->name('students.update.inside');

        /* ── ATTENDANCE ──────────────────────────────────────────────── */
        Route::get ('/attendance/{classroom}', [AttendanceController::class, 'index'])
            ->name('attendance.index');
        Route::post('/attendance/{classroom}', [AttendanceController::class, 'store'])
            ->name('attendance.store');
        Route::get('/kelas/{classroom}/presensi/ajax', [AttendanceController::class, 'ajax']);

        /* ── OBSERVATIONS ────────────────────────────────────────────── */
        Route::post('/observations/store', [ObservationController::class, 'store'])
            ->name('observations.store');
        Route::get('/observations/{schedule}/{detail}', [ObservationController::class, 'getObservations'])
            ->name('observations.fetch');

        /* ── RAPOR TEMPLATE MANAGEMENT ──────────────────────────────── */
        Route::prefix('rapor')->name('rapor.')->group(function () {
            // Template CRUD endpoints
            Route::get('/templates', [TemplateController::class, 'index'])
                ->name('templates.index');
            
            Route::post('/templates', [TemplateController::class, 'store'])
                ->name('templates.store');
            
            Route::get('/templates/{template}', [TemplateController::class, 'show'])
                ->name('templates.show');
            
            Route::put('/templates/{template}', [TemplateController::class, 'update'])
                ->name('templates.update');
            
            Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])
                ->name('templates.destroy');

            // Template assignment to classroom
            Route::post('/templates/{template}/assign', [TemplateController::class, 'assignToClass'])
                ->name('templates.assign');

            Route::get(
                '/classes/{classroom}/assigned-template',
                [TemplateController::class, 'getAssignedTemplate']
            )->name('classes.assigned-template');

            Route::get('/templates/{template}/assign', function () {
                return redirect()->route('rapor.templates.index');
            })->name('templates.assign.get');
            

            /* ── STUDENT REPORTS ────────────────────────────────────── */
            // Get/Create student report
            Route::get('/reports/{classroom}/{student}/{template}', [ReportController::class, 'show'])
                ->name('reports.show');
            
            // Save/Update student report scores
            Route::post('/reports', [ReportController::class, 'store'])
                ->name('reports.store');
            
            // List reports with filters
            Route::get('/reports', [ReportController::class, 'index'])
                ->name('reports.index');

            // Update specific report
            Route::put('/reports/{report}', [ReportController::class, 'update'])
                ->name('reports.update');
            
            // Delete report
            Route::delete('/reports/{report}', [ReportController::class, 'destroy'])
                ->name('reports.destroy');

            Route::get(
                '/rapor/classes/{classroom}/students',
                [\App\Http\Controllers\ReportController::class, 'getStudentsForReport']
            )->name('rapor.classes.students');

            Route::delete('/classes/{classroom}/assigned-template', [TemplateController::class, 'removeAssignedTemplate'])
                ->name('rapor.classes.remove-assigned-template');
                

        });

        /* ── ADMIN PANEL ─────────────────────────────────────────────── */
        Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');

        /* ── AJAX ENDPOINTS FOR ALPINE.JS ───────────────────────────── */
        Route::prefix('ajax')->name('ajax.')->group(function () {
            // Get students by classroom for scoring
            Route::get('/classrooms/{classroom}/students', function ($classroomId) {
                $classroom = \App\Models\Classroom::with('students')->findOrFail($classroomId);
                return response()->json([
                    'success' => true,
                    'data' => $classroom->students
                ]);
            })->name('classroom.students');

            // Get template themes and sub-themes
            Route::get('/templates/{template}/structure', function ($templateId) {
                $template = \App\Models\ReportTemplate::with('themes.subThemes')->findOrFail($templateId);
                return response()->json([
                    'success' => true,
                    'data' => $template
                ]);
            })->name('template.structure');
        });
    });

    /* ── ALL AUTHENTICATED USERS CAN VIEW ─────────────────────────────── */
    Route::middleware('role:admin,teacher,parent')->group(function () {
        // Classrooms index (viewable by all)
        Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
        
        // View report templates (read-only for parents)
        Route::get('/rapor/templates/view', [TemplateController::class, 'viewOnly'])
            ->name('rapor.templates.view');
    });

    /* ── CLASSROOM TAB DETAIL ─────────────────────────────────────────── */
    Route::get('/classroom/{classroom}/{tab}', [ClassroomController::class, 'showClassroomDetail'])
        ->name('classroom.tab');
    Route::get('/classroom/{class}/{tab}/{id}', [ClassroomController::class, 'showClassroomDetail'])
        ->name('classroom.tabs-detail');
    Route::get('/classroom/{class}/{tab}/peserta/{selectedStudentId}', [ClassroomController::class, 'showClassroomDetail'])
        ->name('classroom.student-detail');
    Route::get('/classrooms/{class}/peserta', [ClassroomController::class, 'studentsTab'])
        ->name('classroom.tab.peserta');

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

// Testing syllabus page
Route::get('/testingx', function () {
    return view('testingx');
});