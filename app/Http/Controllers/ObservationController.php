<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
/* ————————————————   TAMBAHKAN BARIS INI ———————————————— */
use Illuminate\Support\Facades\Log;           // ← WAJIB
/* ——————————————————————————————————————————————— */

use App\Models\{
    Observation,
    Classroom,
    ScheduleDetail,
    Student
};


class ObservationController extends Controller
{
    public function getSchedulesForObservation($classroomId)
{
    try {
        $classroom = Classroom::findOrFail($classroomId);
        
        $schedules = $classroom->schedules()
                    ->with('scheduleDetails') // This should load sub_themes
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($schedule) {
                        return [
                            'id' => $schedule->id,
                            'title' => $schedule->title,
                            'description' => $schedule->description,
                            'created_at' => $schedule->created_at->toISOString(),
                            'details' => $schedule->scheduleDetails->map(function($detail) {
                                return [
                                    'id' => $detail->id, // This should be the sub_themes.id
                                    'title' => $detail->title,
                                    'start_date' => $detail->start_date->toDateString(),
                                    'end_date' => $detail->end_date->toDateString(),
                                    'week' => $detail->week,
                                ];
                            })
                        ];
                    });

        return response()->json([
            'success' => true,
            'schedules' => $schedules
        ]);
    } catch (\Exception $e) {
        \Log::error('Error loading schedules for observation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat jadwal: ' . $e->getMessage()
        ], 500);
    }
}

    public function getStudentsForObservation($classroomId)
    {
        try {
            $classroom = Classroom::findOrFail($classroomId);
            
            $students = $classroom->students()
                       ->select('id', 'name', 'student_number')
                       ->orderBy('name')
                       ->get();

            return response()->json([
                'success' => true,
                'students' => $students
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading students for observation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat siswa: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getScores(Request $request)
    {
        try {
            $scores = ObservationScore::where('schedule_detail_id', $request->schedule_detail_id)
                ->get();

            return response()->json([
                'success' => true,
                'scores' => $scores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat nilai'
            ], 500);
        }
    }

    public function storeScores(Request $request)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.schedule_detail_id' => 'required|exists:schedule_details,id',
            'scores.*.score' => 'required|integer|min:1|max:4',
            'scores.*.note' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->scores as $scoreData) {
                ObservationScore::updateOrCreate(
                    [
                        'student_id' => $scoreData['student_id'],
                        'schedule_detail_id' => $scoreData['schedule_detail_id']
                    ],
                    [
                        'score' => $scoreData['score'],
                        'note' => $scoreData['note']
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan nilai: ' . $e->getMessage()
            ], 500);
        }
    }

public function store(Request $request)
{
    try {
        // Debug logging
        \Log::info('Raw request data:', $request->all());
        
        // Basic validation first
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'schedule_detail_id' => 'required|exists:schedule_details,id',
            'observations' => 'required|array',
            'observations.*.student_id' => 'required|exists:students,id',
            'observations.*.score' => 'required|integer|min:1|max:4',
            'observations.*.observation_text' => 'nullable|string'
        ]);

        $scheduleDetailId = $request->schedule_detail_id;
        
        // Since your foreign key references sub_themes but data is in schedule_details,
        // we need to ensure the record exists in both tables
        $scheduleDetail = \DB::table('schedule_details')->where('id', $scheduleDetailId)->first();
        
        if (!$scheduleDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule detail tidak ditemukan',
                'errors' => ['schedule_detail_id' => ['The selected schedule detail id is invalid.']]
            ], 422);
        }
        
        // Check if the record exists in sub_themes (required by foreign key)
        $subThemeExists = \DB::table('sub_themes')->where('id', $scheduleDetailId)->exists();
        \Log::info('Sub theme exists check:', ['id' => $scheduleDetailId, 'exists' => $subThemeExists]);
        
        if (!$subThemeExists) {
            // First, let's check what columns exist in sub_themes table
            $subThemeColumns = \Schema::getColumnListing('sub_themes');
            \Log::info('Sub_themes table columns:', ['columns' => $subThemeColumns]);
            
            // Create the insert data based on available columns
            $insertData = [
                'id' => $scheduleDetail->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Add columns only if they exist in the table
            if (in_array('title', $subThemeColumns)) {
                $insertData['title'] = $scheduleDetail->title ?? 'Sub Theme';
            }
            if (in_array('week', $subThemeColumns)) {
                $insertData['week'] = $scheduleDetail->week ?? 1;
            }
            if (in_array('start_date', $subThemeColumns)) {
                $insertData['start_date'] = $scheduleDetail->start_date ?? now();
            }
            if (in_array('end_date', $subThemeColumns)) {
                $insertData['end_date'] = $scheduleDetail->end_date ?? now();
            }
            if (in_array('schedule_id', $subThemeColumns)) {
                $insertData['schedule_id'] = $scheduleDetail->schedule_id;
            }
            
            // Create the missing record in sub_themes to satisfy the foreign key constraint
            \DB::table('sub_themes')->insert($insertData);
            
            \Log::info('Created missing sub_theme record:', ['id' => $scheduleDetailId, 'data' => $insertData]);
        }

        \Log::info('Validation passed, storing observations');
        
        $scheduleId = $request->schedule_id;
        $observations = $request->observations;
        
        foreach ($observations as $obs) {
            Observation::updateOrCreate(
                [
                    'schedule_id' => $scheduleId,
                    'schedule_detail_id' => $scheduleDetailId,
                    'student_id' => $obs['student_id']
                ],
                [
                    'score' => $obs['score'],
                    'observation_text' => $obs['observation_text'],
                    'description' => $obs['observation_text'] ?? '',
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Observasi berhasil disimpan',
            'count' => count($observations)
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation failed:', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'message' => 'Data tidak valid',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error storing observations: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan observasi: ' . $e->getMessage()
        ], 500);
    }
}

public function getObservations($scheduleId, $detailId)
{
    try {
        $observations = Observation::where('schedule_id', $scheduleId)
            ->where('schedule_detail_id', $detailId)
            ->get(['student_id', 'score', 'observation_text']);

        return response()->json([
            'success'      => true,
            'observations' => $observations,
        ]);
    } catch (\Throwable $e) {
        \Log::error('Error fetching observations: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat observasi: ' . $e->getMessage(),
        ], 500);
    }
}
}