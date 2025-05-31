<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentReport;
use App\Models\ReportScore;
use App\Models\TemplateAssignment;
use App\Models\Student;
use App\Models\TemplateSubTheme;

class StudentReportSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = TemplateAssignment::with(['template.themes.subThemes', 'classroom.students'])->get();
        $scoreOptions = ['BM', 'MM', 'BSH', 'BSB'];
        $sampleNotes = [
            'Anak sudah menunjukkan perkembangan yang baik',
            'Perlu bimbingan lebih lanjut',
            'Sudah mencapai target perkembangan',
            'Menunjukkan antusiasme dalam belajar',
            'Masih memerlukan motivasi',
        ];

        foreach ($assignments as $assignment) {
            $students = $assignment->classroom->students;
            
            foreach ($students->take(3) as $student) { // Hanya buat untuk 3 siswa pertama sebagai sample
                $report = StudentReport::create([
                    'template_id' => $assignment->template_id,
                    'student_id' => $student->id,
                    'classroom_id' => $assignment->classroom_id,
                    'issued_at' => now(),
                    'notes' => 'Rapor semester ini menunjukkan perkembangan yang positif dari ' . $student->name,
                ]);

                // Buat score untuk setiap sub-tema
                $subThemes = $assignment->template->themes->flatMap->subThemes;
                
                foreach ($subThemes as $subTheme) {
                    ReportScore::create([
                        'report_id' => $report->id,
                        'sub_theme_id' => $subTheme->id,
                        'score' => $scoreOptions[array_rand($scoreOptions)],
                        'notes' => $sampleNotes[array_rand($sampleNotes)],
                    ]);
                }
            }
        }
    }
}