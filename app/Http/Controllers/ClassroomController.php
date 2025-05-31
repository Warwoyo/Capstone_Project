<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Classroom,
    Report,
    User,
    Schedule,
    StudentReport
};

class ClassroomController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:50',
            'description' => 'required|string',
            'owner_id'    => 'required|exists:users,id',
            'photo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('classrooms', 'public');
        }

        $classroom = Classroom::create($data);

        return redirect()->route('classroom.tab', [$classroom->id, 'pengumuman'])
               ->with('success', 'Kelas berhasil ditambahkan');
    }

    /* ========== LIST KELAS ========== */
    public function index()
    {
        $classroom = Classroom::with('owner:id,name')
                     ->withCount('students')
                     ->orderBy('name')
                     ->get();

        $teachers  = User::where('role', 'teacher')->get(['id','name']);

        return view('Classroom.index', compact('classroom','teachers'));
    }

    /* ========== DETAIL KELAS ========== */
public function showClassroomDetail(Request $r, Classroom $classroom, string $tab)
{
    /* default data */
    $data = [
        'class'               => $classroom,
        'tab'                 => strtolower($tab),

        'announcementList'    => collect(),
        'scheduleList'        => collect(),
        'attendanceList'      => collect(),
        'observationList'     => collect(),
        'reportList'          => collect(),
        'syllabusList'        => collect(),
        'studentList'         => collect(),
        'students'            => collect(),
        'schedules'           => collect(),

        'activeDate'          => null,
        'selectedSchedule'    => null,
        'selectedDescription' => null,
    ];

    switch ($data['tab']) {
        /* ── PENGUMUMAN ── */
        case 'pengumuman':
            $data['announcementList'] = $classroom->announcements()->latest()->get();
            break;

        /* ── JADWAL ── */
        case 'jadwal':
            $data['scheduleList'] = $classroom->schedules() 
                                    ->with('scheduleDetails') // Changed from 'details' to 'scheduleDetails'
                                    ->orderBy('created_at')
                                    ->get();
            break;

        /* ── PRESENSI ── */
        case 'presensi':
            $activeDate = $r->query('date', now()->toDateString());
            $attDay     = $classroom->attendances()
                           ->whereDate('attendance_date', $activeDate)
                           ->get();

            $attendanceMap         = $attDay->pluck('status','student_id');
            $data['selectedSchedule']    = $attDay->first()->schedule_id ?? null;
            $data['selectedDescription'] = $attDay->first()->description  ?? null;
            $data['activeDate']          = $activeDate;

            $totalSessions = $classroom->attendances()
                             ->distinct('attendance_date')
                             ->count('attendance_date');

            $data['studentList'] = $classroom->students()
                ->withCount([
                    'attendances as total_present' => fn($q) =>
                        $q->where('classroom_id', $classroom->id)
                          ->where('status','hadir'),
                ])
                ->orderBy('name')
                ->get()
                ->map(fn($s) => [
                    'id'           => $s->id,
                    'name'         => $s->name,
                    'totalPresent' => $s->total_present,
                    'percentage'   => $totalSessions
                                       ? round($s->total_present / max($totalSessions,1) * 100).' %'
                                       : '0 %',
                    'statusToday'  => $attendanceMap[$s->id] ?? null,
                ]);

            $data['scheduleList'] = $classroom->Schedule()
                                   ->orderBy('title')
                                   ->get(['id','title']);
            break;

        /* ── OBSERVASI ── */
                case 'observasi':
                    try {
                        $rawSchedules = $classroom->schedules()
                                    ->with('scheduleDetails')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                    
                        $data['scheduleList'] = $rawSchedules->map(function ($schedule) {
                            return [
                                'id' => $schedule->id,
                                'title' => $schedule->title,
                                'description' => $schedule->description,
                                'date' => $schedule->created_at->format('d M Y'),
                                'sub_themes' => $schedule->scheduleDetails->map(function($detail) {
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
                        
                    } catch (\Exception $e) {
                        \Log::error('Error loading observasi data: ' . $e->getMessage());
                        $data['schedule'] = collect();
                    }
                    break;

        /* ── RAPOR ── */
            case 'rapor':
                $data['reportList'] = StudentReport::whereIn(
                                          'student_id',
                                          $classroom->students->pluck('id')
                                      )
                                      ->with(['student', 'template'])
                                      ->latest()
                                      ->get();
                break;

        /* ── PESERTA ── */
        case 'peserta':
            $data['studentList'] = $classroom->students()
                                     ->with(['parents','registrationTokens'])
                                     ->orderBy('name')
                                     ->get();
            break;

        /* ── SILABUS (opsional) ── */
        case 'silabus':
            $data['syllabusList'] = $classroom->syllabuses()->get();
            break;
    }

    return view('Classroom.classroom-detail', $data);
}

/* ========== GET ATTENDANCE SUMMARY ========== */
    public function getAttendanceSummary(Classroom $classroom)
    {
        try {
            $students = $classroom->students;
            $attendanceData = [];
            
            foreach ($students as $student) {
                $attendanceStats = $classroom->attendances()
                    ->where('student_id', $student->id)
                    ->selectRaw('
                        student_id,
                        COUNT(CASE WHEN status = "hadir" THEN 1 END) as present_count,
                        COUNT(CASE WHEN status = "sakit" THEN 1 END) as sick_count,
                        COUNT(CASE WHEN status = "ijin" THEN 1 END) as permission_count,
                        COUNT(CASE WHEN status = "alpha" THEN 1 END) as absent_count,
                        COUNT(*) as total_sessions
                    ')
                    ->groupBy('student_id')
                    ->first();
                
                if ($attendanceStats) {
                    $attendanceData[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'present_count' => $attendanceStats->present_count ?? 0,
                        'sick_count' => $attendanceStats->sick_count ?? 0,
                        'permission_count' => $attendanceStats->permission_count ?? 0,
                        'absent_count' => $attendanceStats->absent_count ?? 0,
                        'total_sessions' => $attendanceStats->total_sessions ?? 0,
                    ];
                } else {
                    // Student with no attendance records
                    $attendanceData[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'present_count' => 0,
                        'sick_count' => 0,
                        'permission_count' => 0,
                        'absent_count' => 0,
                        'total_sessions' => 0,
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $attendanceData,
                'classroom_id' => $classroom->id,
                'classroom_name' => $classroom->name,
                'total_students' => count($attendanceData)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting attendance summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attendance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
