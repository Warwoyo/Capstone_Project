<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;


class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(): View
    {
        $schedules = Schedule::with(['scheduleDetails', 'classroom'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('schedules.index', [
            'schedules' => $schedules,
            'mode' => 'view'
        ]);
    }

    /**
     * Display schedules for parents
     */
    public function parentSchedule()
    {
        try {
            // Get children data from authenticated parent user
            $user = auth()->user();
            $children = $user->children()->with('classrooms')->get();
            
            // Debug: Log children data
            \Log::info('Children data:', ['children' => $children->toArray()]);
            
            // Get schedules for the children's classrooms using the pivot relationship
            $classroomIds = collect();
            foreach ($children as $child) {
                $childClassroomIds = $child->classrooms->pluck('id');
                $classroomIds = $classroomIds->merge($childClassroomIds);
            }
            $classroomIds = $classroomIds->unique()->filter();
            
            // Debug: Log classroom IDs
            \Log::info('Classroom IDs:', ['classroom_ids' => $classroomIds->toArray()]);
            
            if ($classroomIds->isEmpty()) {
                return view('Orangtua.schedule', [
                    'children' => $children,
                    'schedules' => collect()
                ]);
            }
            
            $schedules = Schedule::with(['scheduleDetails', 'classroom'])
                ->whereIn('classroom_id', $classroomIds)
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Debug: Log schedules data
            \Log::info('Schedules data:', ['schedules' => $schedules->toArray()]);

            return view('Orangtua.schedule', [
                'children' => $children,
                'schedules' => $schedules
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in parentSchedule:', ['error' => $e->getMessage()]);
            
            return view('Orangtua.schedule', [
                'children' => collect(),
                'schedules' => collect()
            ]);
        }
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'classroom_id' => 'required|exists:classrooms,id',
                'sub_themes' => 'required|array',
                'sub_themes.*.title' => 'required|string|max:255',
                'sub_themes.*.start_date' => 'required|date',
                'sub_themes.*.end_date' => 'required|date',
                'sub_themes.*.week' => 'nullable|integer|min:1|max:52',
            ]);

            // Create schedule
            $schedule = Schedule::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'classroom_id' => $validated['classroom_id']
            ]);

            // Create sub themes
            foreach ($validated['sub_themes'] as $subTheme) {
                ScheduleDetail::create([
                    'schedule_id' => $schedule->id,
                    'classroom_id' => $validated['classroom_id'], // Add classroom_id here
                    'title' => $subTheme['title'],
                    'start_date' => $subTheme['start_date'],
                    'end_date' => $subTheme['end_date'],
                    'week' => $subTheme['week'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil disimpan'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
        public function update(Request $request, Schedule $schedule)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'sub_themes' => 'array|min:1',
                'sub_themes.*.title' => 'required|string|max:255',
                'sub_themes.*.start_date' => 'required|date',
                'sub_themes.*.end_date' => 'required|date|after_or_equal:sub_themes.*.start_date',
                'sub_themes.*.week' => 'nullable|integer|min:1',
            ]);

            DB::beginTransaction();

            // Update main schedule
            $schedule->update([
                'title' => $validated['title'],
                'description' => $validated['description']
            ]);

            // Delete existing sub-themes
            $schedule->scheduleDetails()->delete();

            // Create new sub-themes
            foreach ($validated['sub_themes'] as $subTheme) {
                $schedule->scheduleDetails()->create([
                    'classroom_id' => $schedule->classroom_id,
                    'title' => $subTheme['title'],
                    'start_date' => $subTheme['start_date'],
                    'end_date' => $subTheme['end_date'],
                    'week' => $subTheme['week'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubThemes($scheduleId)
    {
        try {
            $schedule = Schedule::with('scheduleDetails')->findOrFail($scheduleId);
            
            return response()->json([
                'success' => true,
                'sub_themes' => $schedule->scheduleDetails->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'title' => $detail->title,
                        'description' => $detail->description ?? '',
                        'start_date' => $detail->start_date->format('Y-m-d'),
                        'end_date' => $detail->end_date->format('Y-m-d'),
                        'week' => $detail->week
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat sub tema: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Schedule $schedule)
{
    try {
        $schedule->load('scheduleDetails');
        
        return response()->json([
            'success' => true,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'sub_themes' => $schedule->scheduleDetails->map(function($detail) {
                return [
                    'title' => $detail->title,
                    'start_date' => $detail->start_date,
                    'end_date' => $detail->end_date,
                    'week' => $detail->week
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data jadwal: ' . $e->getMessage()
        ], 500);
    }
}

public function destroy(Schedule $schedule)
{
    try {
        $schedule->delete(); // This will trigger the boot method to delete related details
        
        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
        ], 500);
    }
}

    public function getStudents($scheduleId)
    {
        try {
            $schedule = Schedule::with('classroom.students')->findOrFail($scheduleId);
            
            return response()->json([
                'success' => true,
                'students' => $schedule->classroom->students->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'student_id' => $student->student_number
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data siswa: ' . $e->getMessage()
            ], 500);
        }
    }

}