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
     * Get reports for parent's children - Updated mechanism
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

            Log::info("Loading reports for parent: {$user->id} ({$user->email})");
            
            $children = collect();
            
            try {
                // Method 1: CORRECT WAY - Using student_id in parent_profiles table
                if (\Schema::hasTable('parent_profiles') && \Schema::hasColumn('parent_profiles', 'student_id')) {
                    $children = \DB::table('students')
                        ->join('parent_profiles', 'students.id', '=', 'parent_profiles.student_id')
                        ->where('parent_profiles.email', $user->email)
                        ->select('students.*')
                        ->distinct()
                        ->get();
                    
                    Log::info("Method 1 (parent_profiles.student_id): Found {$children->count()} children");
                }
                
                // Method 2: Fallback - Check if students table has direct parent_id column
                if ($children->isEmpty() && \Schema::hasColumn('students', 'parent_id')) {
                    $children = \DB::table('students')
                        ->where('parent_id', $user->id)
                        ->get();
                    
                    Log::info("Method 2 (students.parent_id): Found {$children->count()} children");
                }
                
                // Method 3: Fallback - Check if students table has parent_email column
                if ($children->isEmpty() && \Schema::hasColumn('students', 'parent_email')) {
                    $children = \DB::table('students')
                        ->where('parent_email', $user->email)
                        ->get();
                    
                    Log::info("Method 3 (students.parent_email): Found {$children->count()} children");
                }
                
                // Method 4: Alternative - Using phone number in parent_profiles
                if ($children->isEmpty() && !empty($user->phone)) {
                    $children = \DB::table('students')
                        ->join('parent_profiles', 'students.id', '=', 'parent_profiles.student_id')
                        ->where('parent_profiles.phone', $user->phone)
                        ->select('students.*')
                        ->distinct()
                        ->get();
                    
                    Log::info("Method 4 (parent_profiles.phone): Found {$children->count()} children");
                }
                
                // Method 5: Check for user_student pivot table (if exists)
                if ($children->isEmpty() && \Schema::hasTable('user_student')) {
                    $studentIds = \DB::table('user_student')
                        ->where('user_id', $user->id)
                        ->pluck('student_id');
                    
                    if ($studentIds->isNotEmpty()) {
                        $children = \DB::table('students')
                            ->whereIn('id', $studentIds)
                            ->get();
                        
                        Log::info("Method 5 (user_student pivot): Found {$children->count()} children");
                    }
                }
                
                if ($children->isEmpty()) {
                    Log::warning("No children found for parent {$user->email} (ID: {$user->id}) using any method.");
                }
                
            } catch (\Exception $e) {
                Log::error("Error getting children: " . $e->getMessage());
            }

            // If no children found, return empty result
            if ($children->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Belum ada data anak untuk orang tua ini'
                ]);
            }

            // Get children IDs for report lookup
            $childrenIds = $children->pluck('id');
            Log::info("Children IDs found: " . $childrenIds->toJson());

            // Get reports for these children
            $reports = \DB::table('student_reports')
                ->join('students', 'student_reports.student_id', '=', 'students.id')
                ->leftJoin('classrooms', function($join) {
                    // Handle different classroom relationship patterns
                    if (\Schema::hasColumn('students', 'classroom_id')) {
                        $join->on('students.classroom_id', '=', 'classrooms.id');
                    } else if (\Schema::hasColumn('student_reports', 'classroom_id')) {
                        $join->on('student_reports.classroom_id', '=', 'classrooms.id');
                    }
                })
                ->join('report_templates', 'student_reports.template_id', '=', 'report_templates.id')
                ->whereIn('student_reports.student_id', $childrenIds)
                ->where('report_templates.is_active', 1)
                ->whereNotNull('student_reports.scores')
                ->where('student_reports.scores', '!=', '{}')
                ->where('student_reports.scores', '!=', '')
                ->select(
                    'student_reports.id',
                    'student_reports.created_at',
                    'student_reports.updated_at',
                    'student_reports.classroom_id as report_classroom_id',
                    'student_reports.scores',
                    'student_reports.teacher_comment',
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

            Log::info("Found {$reports->count()} reports for {$children->count()} children");

            // Transform data for frontend
            $transformedReports = $reports->filter(function ($report) {
                $scores = is_string($report->scores) ? json_decode($report->scores, true) : $report->scores;
                return !empty($scores) && is_array($scores) && count(array_filter($scores)) > 0;
            })->map(function ($report) {
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
                    'issued_at' => $report->created_at,
                    'created_at' => $report->created_at,
                    'updated_at' => $report->updated_at,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $transformedReports
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting parent reports: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data raport: ' . $e->getMessage()
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
                // First try parent_id column
                if (\Schema::hasColumn('students', 'parent_id')) {
                    $student = Student::where('id', $studentId)
                                     ->where('parent_id', $user->id)
                                     ->first();
                }
                
                // If not found, try parent_email
                if (!$student && \Schema::hasColumn('students', 'parent_email')) {
                    $student = Student::where('id', $studentId)
                                     ->where('parent_email', $user->email)
                                     ->first();
                }
                
                // If still not found, try contacts relationship
                if (!$student) {
                    try {
                        $matchingContacts = \DB::table('contacts')
                            ->where('email', $user->email)
                            ->pluck('id');
                        
                        if ($matchingContacts->isNotEmpty()) {
                            $student = Student::where('id', $studentId)
                                             ->whereIn('contact_id', $matchingContacts)
                                             ->first();
                        }
                    } catch (\Exception $e) {
                        Log::warning("Contacts table lookup failed: " . $e->getMessage());
                    }
                }
                
            } catch (\Exception $e) {
                Log::error("Error verifying parent-child relationship: " . $e->getMessage());
            }
            
            if (!$student) {
                abort(403, 'Anda tidak memiliki akses ke data siswa ini');
            }

            // Get the report from student_reports table
            $report = \DB::table('student_reports')
                ->join('students', 'student_reports.student_id', '=', 'students.id')
                ->leftJoin('classrooms', function($join) {
                    if (\Schema::hasColumn('students', 'classroom_id')) {
                        $join->on('students.classroom_id', '=', 'classrooms.id');
                    } else {
                        $join->on('student_reports.classroom_id', '=', 'classrooms.id');
                    }
                })
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
                'name' => $report->classroom_name ?: 'Belum ada kelas',
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
            // First check for parent's children specifically
            $parentChildren = collect();
            
            if (\Schema::hasColumn('students', 'parent_id')) {
                $parentChildren = \DB::table('students')
                    ->where('parent_id', $user->id)
                    ->select('id', 'name', 'parent_id')
                    ->get();
            } elseif (\Schema::hasColumn('students', 'parent_email')) {
                $parentChildren = \DB::table('students')
                    ->where('parent_email', $user->email)
                    ->select('id', 'name', 'parent_email')
                    ->get();
            }
            
            $debug['students']['parent_children'] = $parentChildren;
            $debug['students']['parent_children_count'] = $parentChildren->count();
                
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

    /**
     * Show children data - to debug and understand data structure
     */
    public function showChildren()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'parent') {
            abort(403, 'Akses tidak diizinkan');
        }

        $children = collect();
        $debugInfo = [];
        
        try {
            // Method 1: Check if students table has parent_id column
            $hasParentIdColumn = \Schema::hasColumn('students', 'parent_id');
            $debugInfo['has_parent_id_column'] = $hasParentIdColumn;
            
            if ($hasParentIdColumn) {
                $children = Student::where('parent_id', $user->id)->with(['parentProfiles', 'classrooms'])->get();
                $debugInfo['method_1_count'] = $children->count();
            }
            
            // Method 2: Check if students table has parent_email column
            if ($children->isEmpty() && \Schema::hasColumn('students', 'parent_email')) {
                $children = Student::where('parent_email', $user->email)->with(['parentProfiles', 'classrooms'])->get();
                $debugInfo['method_2_count'] = $children->count();
            }
            
            // Method 3: Check parent_profiles table relationship
            if ($children->isEmpty()) {
                try {
                    $parentProfileIds = \DB::table('parent_profiles')
                        ->where('email', $user->email)
                        ->orWhere('phone', $user->phone ?? '')
                        ->pluck('id');
                    
                    $debugInfo['parent_profile_ids'] = $parentProfileIds->toArray();
                    
                    if ($parentProfileIds->isNotEmpty()) {
                        $studentIds = \DB::table('student_parent_profiles')
                            ->whereIn('parent_profile_id', $parentProfileIds)
                            ->pluck('student_id');
                        
                        $children = Student::whereIn('id', $studentIds)->with(['parentProfiles', 'classrooms'])->get();
                        $debugInfo['method_3_count'] = $children->count();
                    }
                } catch (\Exception $e) {
                    $debugInfo['method_3_error'] = $e->getMessage();
                }
            }
            
            // Method 4: Check contacts relationship
            if ($children->isEmpty()) {
                try {
                    $matchingContacts = \DB::table('contacts')
                        ->where('email', $user->email)
                        ->pluck('id');
                    
                    $debugInfo['contact_ids'] = $matchingContacts->toArray();
                    
                    if ($matchingContacts->isNotEmpty()) {
                        $children = Student::whereIn('contact_id', $matchingContacts)->with(['parentProfiles', 'classrooms'])->get();
                        $debugInfo['method_4_count'] = $children->count();
                    }
                } catch (\Exception $e) {
                    $debugInfo['method_4_error'] = $e->getMessage();
                }
            }
            
            $debugInfo['final_children_count'] = $children->count();
            
        } catch (\Exception $e) {
            $debugInfo['error'] = $e->getMessage();
        }
        
        return view('Orangtua.children', compact('children', 'debugInfo'));
    }

    /**
     * Helper method to create test data for parent-child relationship
     * Only use this for testing/debugging
     */
    public function createTestData()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'parent') {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        try {
            // Check if students table has parent_id column
            if (\Schema::hasColumn('students', 'parent_id')) {
                // Create a test student linked to this parent
                $testStudent = new Student();
                $testStudent->name = 'Anak Test ' . $user->name;
                $testStudent->student_number = 'TEST' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                $testStudent->birth_date = now()->subYears(5)->format('Y-m-d');
                $testStudent->gender = 'male';
                $testStudent->parent_id = $user->id;
                $testStudent->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Test student created successfully',
                    'student' => $testStudent
                ]);
            } elseif (\Schema::hasColumn('students', 'parent_email')) {
                // Create a test student linked to this parent via email
                $testStudent = new Student();
                $testStudent->name = 'Anak Test ' . $user->name;
                $testStudent->student_number = 'TEST' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                $testStudent->birth_date = now()->subYears(5)->format('Y-m-d');
                $testStudent->gender = 'male';
                $testStudent->parent_email = $user->email;
                $testStudent->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Test student created successfully via email',
                    'student' => $testStudent
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No suitable parent relationship column found in students table'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error creating test data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create test data: ' . $e->getMessage()
            ], 500);
        }
    }
}