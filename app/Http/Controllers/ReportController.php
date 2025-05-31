<?php

namespace App\Http\Controllers;

use App\Models\StudentReport;
use App\Models\ReportScore;
use App\Models\ReportTemplate;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Menampilkan laporan siswa
     */
    public function show($classroomId, $studentId, $templateId)
    {
        try {
            $report = StudentReport::with([
                'template.themes.subThemes',
                'scores.subTheme.theme',
                'student',
                'classroom'
            ])
            ->where('classroom_id', $classroomId)
            ->where('student_id', $studentId)
            ->where('template_id', $templateId)
            ->first();

            if (!$report) {
                // Jika belum ada laporan, buat struktur kosong
                $template = ReportTemplate::with('themes.subThemes')->findOrFail($templateId);
                $student = Student::findOrFail($studentId);
                $classroom = Classroom::findOrFail($classroomId);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'report' => null,
                        'template' => $template,
                        'student' => $student,
                        'classroom' => $classroom,
                        'scores' => []
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat laporan'
            ], 500);
        }
    }

    /**
     * Simpan atau update laporan siswa
     */
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'student_id' => 'required|exists:students,id',
            'template_id' => 'required|exists:report_templates,id',
            'scores' => 'required|array',
            'scores.*.sub_theme_id' => 'required|exists:template_sub_themes,id',
            'scores.*.score' => 'required|in:BM,MM,BSH,BSB',
            'scores.*.notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Buat atau update laporan siswa
            $report = StudentReport::updateOrCreate(
                [
                    'classroom_id' => $request->classroom_id,
                    'student_id' => $request->student_id,
                    'template_id' => $request->template_id,
                ],
                [
                    'issued_at' => now(),
                    'notes' => $request->notes
                ]
            );

            // Simpan atau update scores
            foreach ($request->scores as $scoreData) {
                ReportScore::updateOrCreate(
                    [
                        'report_id' => $report->id,
                        'sub_theme_id' => $scoreData['sub_theme_id']
                    ],
                    [
                        'score' => $scoreData['score'],
                        'notes' => $scoreData['notes'] ?? null
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil disimpan',
                'data' => $report->load('scores.subTheme.theme')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan'
            ], 500);
        }
    }

    /**
     * Daftar laporan berdasarkan kelas dan template
     */
    public function index(Request $request)
    {
        try {
            $query = StudentReport::with(['student', 'template', 'classroom']);

            if ($request->classroom_id) {
                $query->where('classroom_id', $request->classroom_id);
            }

            if ($request->template_id) {
                $query->where('template_id', $request->template_id);
            }

            if ($request->student_id) {
                $query->where('student_id', $request->student_id);
            }

            $reports = $query->orderBy('issued_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $reports
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading reports: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar laporan'
            ], 500);
        }
    }

    public function getStudentsForReport($classroomId)
    {
        $classroom = \App\Models\Classroom::with('students')->findOrFail($classroomId);

        return response()->json([
            'success'  => true,
            'students' => $classroom->students->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name,
                // tambahkan fields lain jika perlu
            ]),
        ]);
    }
}