<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Classroom,
    Report,
    User,
    Schedule,
    StudentReport,
    Syllabus,
    Attendance,
    Announcement,
    ObservationScore,
    TemplateController,
};

class ClassroomController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:50',
            'description' => 'required|string',
            'owner_id'    => 'required|exists:users,id',
            'photo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('classrooms', 'public');
        }

        $classroom = Classroom::create($data);

        return redirect()->route('classroom.tab', [$classroom->id, 'pengumuman'])
               ->with('success', 'Kelas berhasil ditambahkan');
    }

    /* ========== LIST KELAS ========== */
    public function index()
    {
        $classroom = Classroom::with('owner:id,name')
                     ->withCount('students')
                     ->orderBy('name')
                     ->get();

        $teachers  = User::where('role', 'teacher')->get(['id','name']);

        return view('Classroom.index', compact('classroom','teachers'));
    }

    /* ========== DETAIL KELAS ========== */
public function showClassroomDetail(Request $r, Classroom $classroom, string $tab)
{
    /* default data */
    $data = [
        'class'               => $classroom,
        'tab'                 => strtolower($tab),

        'announcementList'    => collect(),
        'scheduleList'        => collect(),
        'attendanceList'      => collect(),
        'observationList'     => collect(),
        'reportList'          => collect(),
        'syllabusList'        => collect(),
        'studentList'         => collect(),
        'students'            => collect(),
        'schedules'           => collect(),

        'activeDate'          => null,
        'selectedSchedule'    => null,
        'selectedDescription' => null,
    ];

    switch ($data['tab']) {
        /* ── PENGUMUMAN ── */
        case 'pengumuman':
            $data['announcementList'] = $classroom->announcements()->latest()->get();
            break;

        /* ── JADWAL ── */
        case 'jadwal':
            $data['scheduleList'] = $classroom->schedules() 
                                    ->with('scheduleDetails') // Changed from 'details' to 'scheduleDetails'
                                    ->orderBy('created_at')
                                    ->get();
            break;

        /* ── PRESENSI ── */
        case 'presensi':
            $activeDate = $r->query('date', now()->toDateString());
            $attDay     = $classroom->attendances()
                           ->whereDate('attendance_date', $activeDate)
                           ->get();

            $attendanceMap         = $attDay->pluck('status','student_id');
            $data['selectedSchedule']    = $attDay->first()->schedule_id ?? null;
            $data['selectedDescription'] = $attDay->first()->description  ?? null;
            $data['activeDate']          = $activeDate;

            $totalSessions = $classroom->attendances()
                             ->distinct('attendance_date')
                             ->count('attendance_date');

            $data['studentList'] = $classroom->students()
                ->withCount([
                    'attendances as total_present' => fn($q) =>
                        $q->where('classroom_id', $classroom->id)
                          ->where('status','hadir'),
                ])
                ->orderBy('name')
                ->get()
                ->map(fn($s) => [
                    'id'           => $s->id,
                    'name'         => $s->name,
                    'totalPresent' => $s->total_present,
                    'percentage'   => $totalSessions
                                       ? round($s->total_present / max($totalSessions,1) * 100).' %'
                                       : '0 %',
                    'statusToday'  => $attendanceMap[$s->id] ?? null,
                ]);

            $data['scheduleList'] = $classroom->Schedule()
                                   ->orderBy('title')
                                   ->get(['id','title']);
            break;

        /* ── OBSERVASI ── */
                case 'observasi':
                    try {
                        $rawSchedules = $classroom->schedules()
                                    ->with('scheduleDetails')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                    
                        $data['scheduleList'] = $rawSchedules->map(function ($schedule) {
                            return [
                                'id' => $schedule->id,
                                'title' => $schedule->title,
                                'description' => $schedule->description,
                                'date' => $schedule->created_at->format('d M Y'),
                                'sub_themes' => $schedule->scheduleDetails->map(function($detail) {
                                    return [
                                        'id' => $detail->id,
                                        'title' => $detail->title,
                                        'start_date' => $detail->start_date->toDateString(),
                                        'end_date' => $detail->end_date->toDateString(),
                                        'week' => $detail->week,
                                    ];
                                })
                            ];
                        });
                        
                    } catch (\Exception $e) {
                        \Log::error('Error loading observasi data: ' . $e->getMessage());
                        $data['schedule'] = collect();
                    }
                    break;

        /* ── RAPOR ── */
            case 'rapor':
                $data['reportList'] = StudentReport::whereIn(
                                          'student_id',
                                          $classroom->students->pluck('id')
                                      )
                                      ->with(['student', 'template'])
                                      ->latest()
                                      ->get();
                break;

        /* ── PESERTA ── */
        case 'peserta':
            $data['studentList'] = $classroom->students()
                                     ->with(['parents','registrationTokens'])
                                     ->orderBy('name')
                                     ->get();
            break;

        /* ── SILABUS (opsional) ── */
        case 'silabus':
            $data['syllabusList'] = $classroom->syllabuses()->latest()->get();
            break;
            $data['syllabusList'] = $classroom->syllabuses()->get();
            break;
    }

    return view('Classroom.classroom-detail', $data);
}

/* ========== GET ATTENDANCE SUMMARY ========== */
    public function getAttendanceSummary(Classroom $classroom)
    {
        try {
            $students = $classroom->students;
            $attendanceData = [];
            
            foreach ($students as $student) {
                $attendanceStats = $classroom->attendances()
                    ->where('student_id', $student->id)
                    ->selectRaw('
                        student_id,
                        COUNT(CASE WHEN status = "hadir" THEN 1 END) as present_count,
                        COUNT(CASE WHEN status = "sakit" THEN 1 END) as sick_count,
                        COUNT(CASE WHEN status = "ijin" THEN 1 END) as permission_count,
                        COUNT(CASE WHEN status = "alpha" THEN 1 END) as absent_count,
                        COUNT(*) as total_sessions
                    ')
                    ->groupBy('student_id')
                    ->first();
                
                if ($attendanceStats) {
                    $attendanceData[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'present_count' => $attendanceStats->present_count ?? 0,
                        'sick_count' => $attendanceStats->sick_count ?? 0,
                        'permission_count' => $attendanceStats->permission_count ?? 0,
                        'absent_count' => $attendanceStats->absent_count ?? 0,
                        'total_sessions' => $attendanceStats->total_sessions ?? 0,
                    ];
                } else {
                    // Student with no attendance records
                    $attendanceData[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'present_count' => 0,
                        'sick_count' => 0,
                        'permission_count' => 0,
                        'absent_count' => 0,
                        'total_sessions' => 0,
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $attendanceData,
                'classroom_id' => $classroom->id,
                'classroom_name' => $classroom->name,
                'total_students' => count($attendanceData)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting attendance summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attendance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save student report scores and data
     */
    public function saveStudentReport(Request $request, $classId)
    {
        try {
            \Log::info('Save student report request data:', $request->all());
            
            $validatedData = $request->validate([
                'template_id' => 'required|integer',
                'student_id' => 'required|integer|exists:students,id',
                'scores' => 'nullable|array',
                'teacher_comment' => 'nullable|string|max:1000',
                'parent_comment' => 'nullable|string|max:1000',
                'physical_data' => 'nullable|array',
                'attendance_data' => 'nullable|array',
                'theme_comments' => 'nullable|array',
                'sub_theme_comments' => 'nullable|array'
            ]);

            // Check if classroom exists and user has permission
            $classroom = Classroom::findOrFail($classId);
            
            // Check if student belongs to this classroom
            $studentInClass = $classroom->students()->where('students.id', $validatedData['student_id'])->exists();
            if (!$studentInClass) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak terdaftar di kelas ini'
                ], 400);
            }

            // Check if report already exists
            $existingReport = StudentReport::where([
                'classroom_id' => $classId,
                'student_id' => $validatedData['student_id'],
                'template_id' => $validatedData['template_id']
            ])->first();

            $reportData = [
                'classroom_id' => $classId,
                'student_id' => $validatedData['student_id'],
                'template_id' => $validatedData['template_id'],
                'scores' => json_encode($validatedData['scores'] ?? []),
                'teacher_comment' => $validatedData['teacher_comment'] ?? '',
                'parent_comment' => $validatedData['parent_comment'] ?? '',
                'physical_data' => json_encode($validatedData['physical_data'] ?? []),
                'attendance_data' => json_encode($validatedData['attendance_data'] ?? []),
                'theme_comments' => json_encode($validatedData['theme_comments'] ?? []),
                'sub_theme_comments' => json_encode($validatedData['sub_theme_comments'] ?? []),
            ];

            if ($existingReport) {
                // Update existing report
                $existingReport->update($reportData);
                $report = $existingReport;
                \Log::info('Updated existing report:', ['report_id' => $report->id]);
            } else {
                // Create new report
                $report = StudentReport::create($reportData);
                \Log::info('Created new report:', ['report_id' => $report->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rapor berhasil disimpan',
                'data' => $report
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in save student report:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Save student report error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan rapor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reports for a classroom
     */
    public function getClassReports($classId)
    {
        try {
            $reports = \App\Models\StudentReport::with(['student', 'template'])
                ->where('classroom_id', $classId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $reports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat rapor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete student report
     */
    public function deleteStudentReport($classId, $studentId)
    {
        try {
            $deleted = \App\Models\StudentReport::where([
                'classroom_id' => $classId,
                'student_id' => $studentId
            ])->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rapor berhasil dihapus',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rapor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific student report
     */
    public function getStudentReport($classId, $studentId, $templateId)
    {
        try {
            $report = StudentReport::where([
                'classroom_id' => $classId,
                'student_id' => $studentId,
                'template_id' => $templateId
            ])->first();
            
            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ], 404);
            }
            
            // Safely decode JSON fields - check if already decoded
            $report->scores = is_string($report->scores) ? json_decode($report->scores, true) : ($report->scores ?: []);
            $report->physical_data = is_string($report->physical_data) ? json_decode($report->physical_data, true) : ($report->physical_data ?: []);
            $report->attendance_data = is_string($report->attendance_data) ? json_decode($report->attendance_data, true) : ($report->attendance_data ?: []);
            $report->theme_comments = is_string($report->theme_comments) ? json_decode($report->theme_comments, true) : ($report->theme_comments ?: []);
            $report->sub_theme_comments = is_string($report->sub_theme_comments) ? json_decode($report->sub_theme_comments, true) : ($report->sub_theme_comments ?: []);
            
            return response()->json([
                'success' => true,
                'data' => $report
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get student report error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat laporan siswa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for student report
     */
    public function generateReportPDF($classId, $studentId, $templateId)
    {
        try {
            // Get the report data
            $report = StudentReport::where([
                'classroom_id' => $classId,
                'student_id' => $studentId,
                'template_id' => $templateId
            ])->with(['student', 'classroom'])->first();
            
            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ], 404);
            }
            
            // Safely decode JSON fields - check if already decoded
            $report->scores = is_string($report->scores) ? json_decode($report->scores, true) : ($report->scores ?: []);
            $report->physical_data = is_string($report->physical_data) ? json_decode($report->physical_data, true) : ($report->physical_data ?: []);
            $report->attendance_data = is_string($report->attendance_data) ? json_decode($report->attendance_data, true) : ($report->attendance_data ?: []);
            $report->theme_comments = is_string($report->theme_comments) ? json_decode($report->theme_comments, true) : ($report->theme_comments ?: []);
            $report->sub_theme_comments = is_string($report->sub_theme_comments) ? json_decode($report->sub_theme_comments, true) : ($report->sub_theme_comments ?: []);
            
            // Get template data using the specific template_id from the report
            $template = null;
            
            // First try to get template directly from database
            try {
                $templateData = \DB::table('report_templates')
                    ->where('id', $templateId)
                    ->where('is_active', true)
                    ->first();
                
                if ($templateData) {
                    // Get themes and sub-themes
                    $themes = \DB::table('template_themes')
                        ->where('template_id', $templateId)
                        ->orderBy('order')
                        ->get()
                        ->map(function ($theme) {
                            $subThemes = \DB::table('template_sub_themes')
                                ->where('theme_id', $theme->id)
                                ->orderBy('order')
                                ->get();
                            
                            $theme->sub_themes = $subThemes;
                            $theme->subThemes = $subThemes;
                            
                            return $theme;
                        });
                    
                    $templateData->themes = $themes;
                    $template = $templateData;
                    
                    \Log::info("Successfully loaded template {$templateId} from database");
                } else {
                    \Log::warning("Template {$templateId} not found in database or inactive");
                }
            } catch (\Exception $e) {
                \Log::warning("Could not fetch template from database: " . $e->getMessage());
                
                // Fallback: try to get template from TemplateController
                try {
                    $templateController = app(\App\Http\Controllers\TemplateController::class);
                    $templateResponse = $templateController->show($templateId);
                    
                    if ($templateResponse instanceof \Illuminate\Http\JsonResponse) {
                        $templateData = json_decode($templateResponse->getContent(), true);
                        
                        // TemplateController::show() returns template data directly (not wrapped in success/data)
                        if (isset($templateData['id'])) {
                            $template = (object) $templateData;
                            
                            // Verify template ID matches
                            if ($template->id != $templateId) {
                                \Log::warning("Template ID mismatch: requested {$templateId}, got {$template->id}");
                                $template = null;
                            } else {
                                \Log::info("Successfully loaded template {$templateId} from TemplateController");
                            }
                        }
                    }
                } catch (\Exception $e2) {
                    \Log::warning("Could not fetch template from TemplateController: " . $e2->getMessage());
                }
            }
            
            // If template not found, show warning and return error
            if (!$template) {
                \Log::error("Template dengan ID {$templateId} tidak ditemukan");
                
                // Debug: Check what templates actually exist
                try {
                    $existingTemplates = \DB::table('report_templates')
                        ->where('is_active', true)
                        ->select('id', 'title', 'semester_type')
                        ->get();
                    \Log::info("Available active templates: " . json_encode($existingTemplates));
                    
                    $allTemplates = \DB::table('report_templates')
                        ->select('id', 'title', 'semester_type', 'is_active')
                        ->get();
                    \Log::info("All templates in database: " . json_encode($allTemplates));
                } catch (\Exception $e) {
                    \Log::error("Error checking available templates: " . $e->getMessage());
                }
                
                return response()->json([
                    'success' => false,
                    'message' => "Template dengan ID {$templateId} tidak ditemukan. Pastikan template telah dibuat dan ditetapkan untuk kelas ini."
                ], 404);
            }
            
            // Prepare data for view
            $data = [
                'report' => $report,
                'template' => $template,
                'student' => $report->student,
                'classroom' => $report->classroom,
                'current_date' => now()->format('d F Y')
            ];
            
            // Return the printable view directly
            return view('reports.student-report-pdf', $data);
            
        } catch (\Exception $e) {
            \Log::error('Generate report error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan: ' . $e->getMessage()
            ], 500);
        }
    }
}
