<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateAssignment;
use App\Models\ReportTemplate;
use App\Models\Classroom;

class TemplateAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil template dan kelas yang ada
        $templates = ReportTemplate::all();
        $classrooms = Classroom::all();

        if ($templates->isNotEmpty() && $classrooms->isNotEmpty()) {
            // Assign template pertama ke kelas pertama
            if ($templates->count() >= 1 && $classrooms->count() >= 1) {
                TemplateAssignment::create([
                    'template_id' => $templates->first()->id,
                    'classroom_id' => $classrooms->first()->id,
                    'assigned_at' => now(),
                    'is_current' => true,
                ]);
            }

            // Assign template kedua ke kelas kedua (jika ada)
            if ($templates->count() >= 2 && $classrooms->count() >= 2) {
                TemplateAssignment::create([
                    'template_id' => $templates->skip(1)->first()->id,
                    'classroom_id' => $classrooms->skip(1)->first()->id,
                    'assigned_at' => now(),
                    'is_current' => true,
                ]);
            }

            // Assign template dasar ke semua kelas yang belum memiliki template
            $templateDasar = $templates->where('title', 'Template Dasar Penilaian')->first();
            if ($templateDasar) {
                $assignedClassrooms = TemplateAssignment::pluck('classroom_id')->toArray();
                $unassignedClassrooms = $classrooms->whereNotIn('id', $assignedClassrooms);

                foreach ($unassignedClassrooms as $classroom) {
                    TemplateAssignment::create([
                        'template_id' => $templateDasar->id,
                        'classroom_id' => $classroom->id,
                        'assigned_at' => now(),
                        'is_current' => true,
                    ]);
                }
            }
        }
    }
}