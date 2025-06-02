<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentReport;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ParentReportController extends Controller
{
    /**
     * Show parent report page
     */
    public function index()
    {
        return view('Orangtua.report');
    }

    /**
     * Get reports for parent's children
     */
    public function getReports()
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'parent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            // Debug: Log current user info
            Log::info("Parent user ID: {$user->id}, Name: {$user->name}, Role: {$user->role}");

            // Get children of current parent - simplified approach for testing
            $children = collect();
            
            try {
                // Check if students table has parent_id column
                $hasParentIdColumn = \Schema::hasColumn('students', 'parent_id');
                Log::info("Students table has parent_id column: " . ($hasParentIdColumn ? 'Yes' : 'No'));
                
                if ($hasParentIdColumn) {
                    $children = Student::where('parent_id', $user->id)->get();
                    Log::info("Found {$children->count()} children using parent_id column");
                }
                
                // Check if students table has parent_email column
                if ($children->isEmpty() && \Schema::hasColumn('students', 'parent_email')) {
                    $children = Student::where('parent_email', $user->email)->get();
                    Log::info("Found {$children->count()} children using parent_email column");
                }
                
                // For testing - get a few students (remove in production)
                if ($children->isEmpty()) {
                    Log::warning("No proper parent-child relationship found. Using sample students for testing.");
                    $children = Student::limit(3)->get();
                    Log::info("Testing mode: Found {$children->count()} sample students");
                }
                
            } catch (\Exception $e) {
                Log::error("Error getting children: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
            }

            if ($children->isEmpty()) {
                Log::info("No children found for parent {$user->id}");
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'debug' => [
                        'parent_id' => $user->id,
                        'children_count' => 0,
                        'message' => 'No children found for this parent'
                    ]
                ]);
            }

            $childrenIds = $children->pluck('id');
            Log::info("Children IDs: " . $childrenIds->toJson());

            // Debug: Check if there are any reports in the database
            $totalReports = \DB::table('student_reports')->count();
            Log::info("Total reports in database: {$totalReports}");

            // Debug: Check reports for these children
            $reportsForChildren = \DB::table('student_reports')
                ->whereIn('student_id', $childrenIds)
                ->count();
            Log::info("Reports for these children: {$reportsForChildren}");

            // Get reports for all children with direct DB query
            $reports = \DB::table('student_reports')
                ->join('students', 'student_reports.student_id', '=', 'students.id')
                ->leftJoin('classrooms', function($join) {
                    // Check if students table has classroom_id column
                    if (\Schema::hasColumn('students', 'classroom_id')) {
                        $join->on('students.classroom_id', '=', 'classrooms.id');
                    } else {
                        // If no classroom_id in students table, use from student_reports
                        $join->on('student_reports.classroom_id', '=', 'classrooms.id');
                    }
                })
                ->join('report_templates', 'student_reports.template_id', '=', 'report_templates.id')
                ->whereIn('student_reports.student_id', $childrenIds)
                ->where('report_templates.is_active', 1)
                ->whereNotNull('student_reports.scores') // Only get reports that have scores
                ->where('student_reports.scores', '!=', '{}') // Exclude empty scores
                ->where('student_reports.scores', '!=', '') // Exclude empty strings
                ->select(
                    'student_reports.id',
                    'student_reports.created_at',
                    'student_reports.updated_at',
                    'student_reports.classroom_id as report_classroom_id',
                    'student_reports.scores',
                    'student_reports.teacher_comment',
                    'student_reports.physical_data',
                    'student_reports.attendance_data',
                    'students.id as student_id',
                    'students.name as student_name',
                    'classrooms.id as classroom_id',
                    'classrooms.name as classroom_name',
                    'report_templates.id as template_id',
                    'report_templates.title as template_title',
                    'report_templates.semester_type'
                )
                ->orderBy('student_reports.created_at', 'desc')
                ->get();

            Log::info("Found {$reports->count()} reports after JOIN");
            
            // Debug: Log some sample scores data
            if ($reports->isNotEmpty()) {
                $sampleReport = $reports->first();
                Log::info("Sample scores data: " . ($sampleReport->scores ?? 'NULL'));
                Log::info("Sample teacher comment: " . ($sampleReport->teacher_comment ?? 'NULL'));
            }
            
            // If no reports found with JOIN, try simpler query for debugging
            if ($reports->isEmpty()) {
                Log::info("No reports found with JOIN, trying simple query");
                $simpleReports = \DB::table('student_reports')
                    ->whereIn('student_id', $childrenIds)
                    ->get();
                Log::info("Simple query found {$simpleReports->count()} reports");
                
                // If still no reports, check if we have any reports at all for these students
                foreach ($childrenIds as $studentId) {
                    $studentReports = \DB::table('student_reports')->where('student_id', $studentId)->count();
                    Log::info("Student {$studentId} has {$studentReports} reports");
                }
            }
            
            // Debug each table separately
            $studentsCount = \DB::table('students')->whereIn('id', $childrenIds)->count();
            $classroomsCount = \DB::table('classrooms')->count();
            $templatesCount = \DB::table('report_templates')->where('is_active', 1)->count();
            
            Log::info("Table counts - Students: {$studentsCount}, Classrooms: {$classroomsCount}, Active Templates: {$templatesCount}");

            // Transform data to match frontend expectations
            $transformedReports = $reports->filter(function ($report) {
                // Additional filtering - ensure the report has meaningful data
                $scores = is_string($report->scores) ? json_decode($report->scores, true) : $report->scores;
                
                // Check if scores is not empty and has actual values
                if (empty($scores) || !is_array($scores)) {
                    return false;
                }
                
                // Check if there are any non-null, non-empty scores
                $hasValidScores = false;
                foreach ($scores as $score) {
                    if (!empty($score) && $score !== null && $score !== '') {
                        $hasValidScores = true;
                        break;
                    }
                }
                
                return $hasValidScores;
            })->map(function ($report) {
                // Parse scores to get count of assessed items
                $scores = is_string($report->scores) ? json_decode($report->scores, true) : $report->scores;
                $assessedItemsCount = is_array($scores) ? count(array_filter($scores, function($score) {
                    return !empty($score) && $score !== null && $score !== '';
                })) : 0;
                
                return [
                    'id' => $report->id,
                    'student' => [
                        'id' => $report->student_id,
                        'name' => $report->student_name,
                    ],
                    'classroom' => [
                        'id' => $report->classroom_id ?: $report->report_classroom_id ?: 0,
                        'name' => $report->classroom_name ?: 'Belum ada kelas',
                    ],
                    'template' => [
                        'id' => $report->template_id,
                        'title' => $report->template_title,
                        'semester_type' => $report->semester_type,
                    ],
                    'issued_at' => $report->created_at, // Using created_at as issued_at
                    'created_at' => $report->created_at,
                    'updated_at' => $report->updated_at,
                    'assessed_items' => $assessedItemsCount, // For debugging
                    'has_teacher_comment' => !empty($report->teacher_comment),
                ];
            })->values(); // Reset array keys after filtering

            Log::info("Found {$transformedReports->count()} reports for parent {$user->id}");
            
            if ($transformedReports->isEmpty()) {
                Log::warning("No reports found after transformation");
                Log::info("Debug info: Children IDs: " . $childrenIds->toJson());
                Log::info("Raw reports count: {$reports->count()}");
            } else {
                Log::info("Sample report: " . json_encode($transformedReports->first()));
            }

            return response()->json([
                'success' => true,
                'data' => $transformedReports,
                'debug' => [
                    'parent_id' => $user->id,
                    'children_count' => $children->count(),
                    'children_ids' => $childrenIds->toArray(),
                    'reports_count' => $transformedReports->count(),
                    'total_reports_in_db' => $totalReports
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting parent reports: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data raport'
            ], 500);
        }
    }

    /**
     * View report in print-ready format
     */
    public function viewReport($studentId, $templateId)
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'parent') {
                abort(403, 'Akses tidak diizinkan');
            }

            // Verify the student belongs to this parent
            $student = null;
            
            try {
                $student = Student::where('id', $studentId)
                                 ->where('parent_id', $user->id)
                                 ->first();
            } catch (\Exception $e) {
                // If parent_id column doesn't exist, try alternative verification
                Log::warning("Parent-child relationship verification failed, using alternative method");
                
                // For testing: just get the student (should implement proper verification)
                $student = Student::find($studentId);
                
                // In production, you might want to check a separate parent_student table:
                // $hasAccess = \DB::table('parent_student')
                //     ->where('parent_id', $user->id)
                //     ->where('student_id', $studentId)
                //     ->exists();
                // if (!$hasAccess) {
                //     abort(403, 'Anda tidak memiliki akses ke data siswa ini');
                // }
            }
            
            if (!$student) {
                abort(404, 'Siswa tidak ditemukan atau bukan anak Anda');
            }

            // Get the report from student_reports table
            $report = \DB::table('student_reports')
                ->join('students', 'student_reports.student_id', '=', 'students.id')
                ->join('classrooms', 'student_reports.classroom_id', '=', 'classrooms.id')
                ->where('student_reports.student_id', $studentId)
                ->where('student_reports.template_id', $templateId)
                ->select(
                    'student_reports.*',
                    'students.name as student_name',
                    'students.nisn',
                    'students.birth_date',
                    'classrooms.name as classroom_name'
                )
                ->first();

            if (!$report) {
                abort(404, 'Raport tidak ditemukan');
            }

            // Create report object with decoded JSON fields
            $reportObj = (object) [
                'id' => $report->id,
                'classroom_id' => $report->classroom_id,
                'student_id' => $report->student_id,
                'template_id' => $report->template_id,
                'scores' => is_string($report->scores) ? json_decode($report->scores, true) : ($report->scores ?: []),
                'teacher_comment' => $report->teacher_comment,
                'parent_comment' => $report->parent_comment,
                'physical_data' => is_string($report->physical_data) ? json_decode($report->physical_data, true) : ($report->physical_data ?: []),
                'attendance_data' => is_string($report->attendance_data) ? json_decode($report->attendance_data, true) : ($report->attendance_data ?: []),
                'theme_comments' => is_string($report->theme_comments) ? json_decode($report->theme_comments, true) : ($report->theme_comments ?: []),
                'sub_theme_comments' => is_string($report->sub_theme_comments) ? json_decode($report->sub_theme_comments, true) : ($report->sub_theme_comments ?: []),
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
            ];

            // Create student object
            $studentObj = (object) [
                'id' => $studentId,
                'name' => $report->student_name,
                'nisn' => $report->nisn,
                'birth_date' => $report->birth_date,
            ];

            // Create classroom object
            $classroomObj = (object) [
                'id' => $report->classroom_id,
                'name' => $report->classroom_name,
            ];

            // Get template data from report_templates table
            $template = \DB::table('report_templates')
                ->where('id', $templateId)
                ->where('is_active', 1)
                ->first();

            if (!$template) {
                abort(404, 'Template raport tidak ditemukan atau tidak aktif');
            }

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
            
            $template->themes = $themes;

            // Prepare data for view
            $data = [
                'report' => $reportObj,
                'template' => $template,
                'student' => $studentObj,
                'classroom' => $classroomObj,
                'current_date' => now()->format('d F Y')
            ];

            Log::info("Parent {$user->id} viewing report for student {$studentId}, template {$templateId}");

            // Return the printable view
            return view('reports.student-report-pdf', $data);

        } catch (\Exception $e) {
            Log::error('Error viewing parent report: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Gagal memuat raport: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF report
     */
    public function downloadPDF($studentId, $templateId)
    {
        // This will redirect to the same view report method
        // but with different handling for PDF output
        return $this->viewReport($studentId, $templateId);
    }

    /**
     * Debug method to check parent-child relationship and reports
     */
    public function debugReports()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $debug = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'tables' => [],
            'students' => [],
            'reports' => []
        ];
        
        // Check table structures
        try {
            $debug['tables']['students_columns'] = \Schema::getColumnListing('students');
            $debug['tables']['student_reports_columns'] = \Schema::getColumnListing('student_reports');
            $debug['tables']['contacts_columns'] = \Schema::getColumnListing('contacts');
        } catch (\Exception $e) {
            $debug['tables']['error'] = $e->getMessage();
        }
        
        // Get all students for debugging
        try {
            $allStudents = \DB::table('students')
                ->leftJoin('classrooms', function($join) {
                    if (\Schema::hasColumn('students', 'classroom_id')) {
                        $join->on('students.classroom_id', '=', 'classrooms.id');
                    }
                })
                ->select(
                    'students.id',
                    'students.name',
                    'students.classroom_id',
                    'classrooms.name as classroom_name'
                )
                ->limit(10)
                ->get();
                
            $debug['students']['all_students'] = $allStudents->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'classroom' => $student->classroom_name ?: 'No classroom',
                    'classroom_id' => $student->classroom_id,
                    'parent_id' => $student->parent_id ?? 'NULL',
                    'contact_id' => $student->contact_id ?? 'NULL'
                ];
            });
        } catch (\Exception $e) {
            $debug['students']['error'] = $e->getMessage();
        }
        
        // Get all reports
        try {
            $allReports = \DB::table('student_reports')
                ->join('students', 'student_reports.student_id', '=', 'students.id')
                ->join('report_templates', 'student_reports.template_id', '=', 'report_templates.id')
                ->select(
                    'student_reports.id',
                    'student_reports.student_id',
                    'student_reports.template_id',
                    'students.name as student_name',
                    'report_templates.title as template_title'
                )
                ->get();
            
            $debug['reports']['all_reports'] = $allReports;
            $debug['reports']['count'] = $allReports->count();
        } catch (\Exception $e) {
            $debug['reports']['error'] = $e->getMessage();
        }
        
        return response()->json($debug);
    }
}