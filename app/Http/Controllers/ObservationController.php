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
                        ->with('scheduleDetails')
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
                                        'id' => $detail->id,
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
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'schedule_detail_id' => 'required|exists:schedule_details,id', // This should reference schedule_details
            'observations' => 'required|array',
            'observations.*.student_id' => 'required|exists:students,id',
            'observations.*.score' => 'required|integer|min:1|max:4',
            'observations.*.observation_text' => 'nullable|string'
        ]);

        \Log::info('Storing observations:', $request->all());
        
        $scheduleId = $request->schedule_id;
        $scheduleDetailId = $request->schedule_detail_id;
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
                    'description' => $obs['observation_text'] ?? '', // Ensure description is provided
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Observasi berhasil disimpan',
            'count' => count($observations)
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
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