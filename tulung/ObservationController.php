<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\ObservationScore;
use Illuminate\Support\Facades\DB;

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
}