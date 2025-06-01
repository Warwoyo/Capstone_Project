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
    ReportController,
    SyllabusController
};

/*
|--------------------------------------------------------------------------
| PUBLIC AREA
|--------------------------------------------------------------------------
*/

// Form registrasi orang-tua
Route::get ('/parent/register', [ParentRegisterController::class, 'create'])->name('parent.register.form');
Route::post('/parent/register', [ParentRegisterController::class, 'store'])->name('parent.register');

// Cleanup route for orphaned contacts (temporary solution)
Route::get('/cleanup-contacts', function() {
    $orphanedContacts = \App\Models\UserContact::whereNotExists(function ($query) {
        $query->select('id')
            ->from('users')
            ->whereColumn('users.id', 'user_contacts.user_id');
    })->get();
    
    $count = $orphanedContacts->count();
    
    foreach ($orphanedContacts as $contact) {
        $contact->delete();
    }
    
    return "Cleaned up {$count} orphaned contacts. You can now try registration again.";
});

/*
|--------------------------------------------------------------------------
| GUEST-ONLY (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get ('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\CustomLoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA (semua user yang sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* ── Auth misc ────────────────────────────────────────────────────── */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Teacher password change routes (accessible by teachers with temp passwords)
    Route::get('/teacher/change-password', [App\Http\Controllers\Auth\TeacherPasswordController::class, 'showChangePasswordForm'])->name('teacher.password.form');
    Route::post('/teacher/change-password', [App\Http\Controllers\Auth\TeacherPasswordController::class, 'changePassword'])->name('teacher.password.change');
    
    // Apply temp password middleware to dashboard and other protected routes
    Route::middleware([\App\Http\Middleware\CheckTempPassword::class])->group(function () {
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

    /* ── PARENT-ONLY (VIEW ACCESS ONLY) ─────────────────────────────── */
    Route::middleware('role:parent')->group(function () {
        Route::prefix('orangtua')->name('orangtua.')->group(function () {
            // Dashboard orangtua
            Route::get('/dashboard', [DashboardController::class, 'parentDashboard'])->name('dashboard');
            
            // Data anak (view only)
            Route::get('/anak/data-anak', [DashboardController::class, 'childrenParent'])->name('children');
            
            // Jadwal pembelajaran anak (view only)
            Route::get('/anak/jadwal', [DashboardController::class, 'scheduleParent'])->name('schedule');
            
            // Presensi anak (view only)
            Route::get('/anak/presensi', [DashboardController::class, 'attendanceParent'])->name('attendance');
            Route::get('/anak/presensi/{student}', [DashboardController::class, 'attendanceParentDetail'])->name('attendance.detail');
            
            // Observasi perkembangan anak (view only)
            Route::get('/anak/observasi', [DashboardController::class, 'observationParent'])->name('observation');
            Route::get('/anak/observasi/{student}', [DashboardController::class, 'observationParentDetail'])->name('observation.detail');
            
            // Pengumuman (view only)
            Route::get('/anak/pengumuman', [DashboardController::class, 'announcementParent'])->name('announcement');
            Route::get('/anak/pengumuman/{announcement}', [DashboardController::class, 'announcementParentDetail'])->name('announcement.detail');
            
            // Silabus pembelajaran (view only)
            Route::get('/anak/silabus', [DashboardController::class, 'syllabusParent'])->name('syllabus');
            Route::get('/anak/silabus/{syllabus}', [DashboardController::class, 'syllabusParentDetail'])->name('syllabus.detail');
            
            // Rapor anak (view only)
            Route::get('/anak/rapor', [ReportController::class, 'parentView'])->name('rapor');
            Route::get('/anak/rapor/{student}', [ReportController::class, 'parentViewDetail'])->name('rapor.detail');
            Route::get('/anak/rapor/{student}/download', [ReportController::class, 'parentDownloadReport'])->name('rapor.download');
            
            // Kelas anak (view classroom info)
            Route::get('/anak/kelas', [DashboardController::class, 'classroomParent'])->name('classroom');
            Route::get('/anak/kelas/{classroom}', [DashboardController::class, 'classroomParentDetail'])->name('classroom.detail');
        });
    });

    /* ── ADMIN-ONLY ROUTES ──────────────────────────────────────────── */
    Route::middleware('role:admin')->group(function () {
        // User management (admin only) - redirects to main admin page
        Route::get('/admin/users', [AdminController::class, 'fetchParentList'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        
        // Parent registration management
        Route::get('/admin/orangtua', [AdminController::class, 'fetchParentList'])->name('Admin.index');
        Route::post('/admin/orangtua/{parent}/reset-token', [AdminController::class, 'resetParentToken'])->name('admin.parents.reset-token');
        Route::post('/admin/orangtua/generate-token', [AdminController::class, 'generateNewToken'])->name('admin.parents.generate-token');
        Route::delete('/admin/orangtua/token/{token}/delete', [AdminController::class, 'deleteUnusedToken'])->name('admin.parents.delete-token');
        
        // Teacher password reset
        Route::post('/admin/guru/{teacher}/reset-password', [AdminController::class, 'resetTeacherPassword'])->name('admin.teachers.reset-password');
        
        // System settings
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    });

    /* ── TEACHER-ONLY ROUTES ─────────────────────────────────────────── */
    Route::middleware('role:teacher')->group(function () {
        // Teacher can only manage their assigned classrooms
        Route::get('/teacher/classrooms', [ClassroomController::class, 'teacherClassrooms'])->name('teacher.classrooms');
        
        // Teacher-specific attendance and observation routes
        Route::get('/teacher/attendance', [AttendanceController::class, 'teacherIndex'])->name('teacher.attendance');
        Route::get('/teacher/observations', [ObservationController::class, 'teacherIndex'])->name('teacher.observations');
    });

    /* ── ADMIN & TEACHER ONLY (CRUD ACCESS) ─────────────────────────────── */
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

            // Add route for multiple assigned templates
            Route::get('/classes/{classroom}/assigned-templates', [TemplateController::class, 'getAssignedTemplates'])
                ->name('classes.assigned-templates');
                
            // Alternative route for debugging assigned templates
            Route::get('/debug/classes/{classroom}/assigned-templates', function ($classroomId) {
                $assignments = \Illuminate\Support\Facades\DB::table('template_assignments')
                    ->join('rapor_templates', 'template_assignments.template_id', '=', 'rapor_templates.id')
                    ->where('template_assignments.classroom_id', $classroomId)
                    ->select('rapor_templates.*', 'template_assignments.assigned_at', 'template_assignments.id as assignment_id')
                    ->get();
                
                return response()->json([
                    'success' => true,
                    'data' => $assignments,
                    'count' => $assignments->count(),
                    'class_id' => $classroomId
                ]);
            })->name('debug.classes.assigned-templates');

            Route::get('/templates/{template}/assign', function () {
                return redirect()->route('rapor.templates.index');
            })->name('templates.assign.get');
            
            // Simple route to check if assignments exist for a class
            Route::get('/classes/{classroom}/check-assignments', function ($classroomId) {
                $assignmentCount = \Illuminate\Support\Facades\DB::table('template_assignments')
                    ->where('classroom_id', $classroomId)
                    ->count();
                
                $allAssignments = \Illuminate\Support\Facades\DB::table('template_assignments')
                    ->where('classroom_id', $classroomId)
                    ->get();
                
                return response()->json([
                    'success' => true,
                    'class_id' => $classroomId,
                    'assignment_count' => $assignmentCount,
                    'assignments' => $allAssignments,
                    'table_exists' => \Illuminate\Support\Facades\Schema::hasTable('template_assignments')
                ]);
            })->name('classes.check-assignments');

            // Direct route to get assigned template IDs for a class
            Route::get('/classes/{classroom}/assigned-template-ids', function ($classroomId) {
                $templateIds = \Illuminate\Support\Facades\DB::table('template_assignments')
                    ->where('classroom_id', $classroomId)
                    ->pluck('template_id')
                    ->toArray();
                
                return response()->json([
                    'success' => true,
                    'template_ids' => $templateIds,
                    'count' => count($templateIds),
                    'class_id' => $classroomId
                ]);
            })->name('classes.assigned-template-ids');
            

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
                
            // Add route for removing specific template from classroom
            Route::delete('/classes/{classroom}/assigned-template/{templateId}', [TemplateController::class, 'removeAssignedTemplate'])
                ->name('classes.remove-assigned-template');
                

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

            // Add route for attendance summary AJAX
            Route::get('/classrooms/{classroom}/attendance-summary', [ClassroomController::class, 'getAttendanceSummary']);
            
            // Add route for students in classroom
            Route::get('/classrooms/{classroom}/students', function ($classroomId) {
                $classroom = \App\Models\Classroom::with('students')->findOrFail($classroomId);
                return response()->json([
                    'success' => true,
                    'data' => $classroom->students
                ]);
            });
        });
    });

    /* ── SHARED VIEW ACCESS (ALL AUTHENTICATED USERS) ─────────────────────────────── */
    Route::middleware('role:admin,teacher,parent')->group(function () {
        // Classrooms index (viewable by all authenticated users)
        Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
        
        // View announcements (read-only access for all users)
        Route::get('/announcements', [AnnouncementController::class, 'publicIndex'])->name('announcements.public');
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'publicShow'])->name('announcements.public.show');
        
        // View report templates (read-only for parents)
        Route::get('/rapor/templates/view', [TemplateController::class, 'viewOnly'])->name('rapor.templates.view');
        
        // View syllabus (all users can view)
        Route::get('/syllabus', [SyllabusController::class, 'publicIndex'])->name('syllabus.public');
        Route::get('/syllabus/{syllabus}', [SyllabusController::class, 'view'])->name('syllabus.view');
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

    }); // Close temp password middleware group

});

// Syllabus routes
Route::middleware(['auth'])->group(function () {
    Route::post('/syllabus/store/{classroom}', [SyllabusController::class, 'store'])->name('syllabus.store');
    Route::get('/syllabus/view/{syllabus}', [SyllabusController::class, 'view'])->name('syllabus.view');
    Route::delete('/syllabus/{syllabus}', [SyllabusController::class, 'destroy'])->name('syllabus.destroy');
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

// Debug routes for rapor - simplified
Route::group(['prefix' => 'rapor/debug'], function () {
    Route::get('/class/{classId}/assignments', function ($classId) {
        $assignments = \Illuminate\Support\Facades\DB::table('template_assignments')
            ->where('classroom_id', $classId)
            ->get();
        
        return response()->json([
            'class_id' => $classId,
            'assignments' => $assignments,
            'count' => $assignments->count()
        ]);
    });
});

// Additional rapor routes for saving student reports
Route::post('/rapor/classes/{classId}/reports', [ClassroomController::class, 'saveStudentReport'])
    ->name('rapor.classes.reports.store');

Route::get('/rapor/classes/{classId}/reports', [ClassroomController::class, 'getClassReports'])
    ->name('rapor.classes.reports.index');

Route::get('/rapor/classes/{classId}/reports/{studentId}/{templateId}', [ClassroomController::class, 'getStudentReport'])
    ->name('rapor.classes.reports.show');

Route::get('/rapor/classes/{classId}/reports/{studentId}/{templateId}/pdf', [ClassroomController::class, 'generateReportPDF'])
    ->name('rapor.classes.reports.pdf');

Route::delete('/rapor/classes/{classId}/reports/{studentId}', [ClassroomController::class, 'deleteStudentReport'])
    ->name('rapor.classes.reports.destroy');

// Template management routes - these are handled by the proper TemplateController above
// The routes in the rapor.templates group should handle these endpoints

// Attendance summary route
Route::get('/ajax/classrooms/{classroom}/attendance-summary', [ClassroomController::class, 'getAttendanceSummary'])
    ->name('classroom.attendance.summary');