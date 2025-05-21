<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Classroom,
    Report,
    User
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
                                        ->with('details')
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

                $data['scheduleList'] = $classroom->schedules()
                                       ->orderBy('title')
                                       ->get(['id','title']);
                break;

            /* ── OBSERVASI ── */
            case 'observasi':
                $data['observationList'] = $classroom->observations()
                                            ->with(['student','scheduleDetail'])
                                            ->latest()
                                            ->get();
                break;

            /* ── RAPOR ── */
            case 'rapor':
                $data['reportList'] = Report::whereIn(
                                          'student_id',
                                          $classroom->students->pluck('id')
                                      )
                                      ->with('student')
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
}
