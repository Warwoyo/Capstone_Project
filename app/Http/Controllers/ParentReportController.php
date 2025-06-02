<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentReport;
use App\Models\ReportTemplate;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParentReportController extends Controller
{
    /**
     * Show the parent report dashboard
     */
    public function index()
    {
        try {
            $user = Auth::user();
            Log::info('Parent accessing reports', ['user_id' => $user->id, 'user_name' => $user->name]);
            
            // Get all student reports directly - show all available reports for testing
            $reports = StudentReport::with([
                'student',
                'template', 
                'classroom'
            ])
            ->join('students', 'student_reports.student_id', '=', 'students.id')
            ->orderBy('student_reports.created_at', 'desc')
            ->select('student_reports.*')
            ->get();
            
            Log::info('Total reports found', ['count' => $reports->count()]);
            
            // Group reports by student for better display
            $reportsByStudent = $reports->groupBy('student_id');
            
            return view('Orangtua.rapot.index', compact('reports', 'reportsByStudent'));
            
        } catch (\Exception $e) {
            Log::error('Error loading parent report index: ' . $e->getMessage());
            return redirect()->route('orangtua.dashboard')->with('error', 'Gagal memuat halaman rapor');
        }
    }

    /**
     * Show reports for a specific child
     */
    public function showChild($childId)
    {
        try {
            $user = Auth::user();
            
            // Find the child with flexible matching
            $child = Student::where('id', $childId)->first();
            
            if (!$child) {
                return redirect()->route('orangtua.anak.rapot.index')
                               ->with('error', 'Anak tidak ditemukan');
            }
            
            // For now, allow access (we can add proper parent verification later)
            // TODO: Add proper parent-child relationship verification
            
            // Get all reports for this child
            $reports = StudentReport::with([
                'template',
                'classroom'
            ])
            ->where('student_id', $childId)
            ->orderBy('created_at', 'desc')
            ->get();

            return view('Orangtua.rapot.child', compact('child', 'reports'));
            
        } catch (\Exception $e) {
            Log::error('Error loading child reports: ' . $e->getMessage());
            return redirect()->route('orangtua.anak.rapot.index')->with('error', 'Gagal memuat rapor anak');
        }
    }

    /**
     * Show detailed view of a specific report
     */
    public function showReport($childId, $reportId)
    {
        try {
            $user = Auth::user();
            
            // Find the child
            $child = Student::where('id', $childId)->first();
            
            if (!$child) {
                return redirect()->route('orangtua.anak.rapot.index')
                               ->with('error', 'Anak tidak ditemukan');
            }

            // Get the specific report with all related data
            $report = StudentReport::with([
                'template.themes.subThemes',
                'scores.subTheme.theme',
                'classroom'
            ])
            ->where('id', $reportId)
            ->where('student_id', $childId)
            ->first();
            
            if (!$report) {
                return redirect()->route('orangtua.anak.rapot.child', $childId)
                               ->with('error', 'Rapor tidak ditemukan');
            }

            return view('Orangtua.rapot.view', compact('child', 'report'));
            
        } catch (\Exception $e) {
            Log::error('Error loading report detail: ' . $e->getMessage());
            return redirect()->route('orangtua.anak.rapot.index')->with('error', 'Gagal memuat detail rapor');
        }
    }

    /**
     * Generate PDF for printing
     */
    public function printReport($childId, $reportId)
    {
        try {
            $user = Auth::user();
            
            // Find the child
            $child = Student::where('id', $childId)->first();
            
            if (!$child) {
                return redirect()->route('orangtua.anak.rapot.index')
                               ->with('error', 'Anak tidak ditemukan');
            }

            // Get the specific report with all related data
            $report = StudentReport::with([
                'template.themes.subThemes',
                'scores.subTheme.theme',
                'classroom'
            ])
            ->where('id', $reportId)
            ->where('student_id', $childId)
            ->first();
            
            if (!$report) {
                return redirect()->route('orangtua.anak.rapot.child', $childId)
                               ->with('error', 'Rapor tidak ditemukan');
            }

            return view('Orangtua.rapot.print', compact('child', 'report'));
            
        } catch (\Exception $e) {
            Log::error('Error generating print view: ' . $e->getMessage());
            return redirect()->route('orangtua.anak.rapot.index')->with('error', 'Gagal membuat halaman cetak');
        }
    }
}