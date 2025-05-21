<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, Classroom, Student};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /** ── Form presensi ───────────────────────────── */
    public function index(Classroom $classroom, Request $request)
    {
        $date = $request->query('date');

        // Ambil semua jadwal (tema)
        $schedules = DB::table('schedules')->get();

        // Ambil data presensi utama (header) untuk tanggal tsb (ambil salah satu, misal milik siswa pertama)
        $attendance = null;
        if ($date) {
            $attendance = Attendance::where('classroom_id', $classroom->id)
                ->where('attendance_date', $date)
                ->first();
        }

        // Ambil data siswa & status presensi pada tanggal tsb
                $students = $classroom->students()
            ->select('students.id','students.name')
            ->withCount([
                'attendances as total_present' => fn($q) => $q
                    ->where('classroom_id', $classroom->id)
                    ->where('status', 'hadir')
            ])
            ->get()
            ->map(function ($s) use ($date, $classroom) {
                $attendance = Attendance::where('classroom_id', $classroom->id)
                    ->where('student_id', $s->id)
                    ->where('attendance_date', $date)
                    ->first();
                $s->statusToday     = $attendance?->status;
                $s->totalPresent    = $s->total_present ?? 0;
                $s->percentage      = $s->total_present > 0
                    ? ($s->total_present * 100).'%' : '0%';
                return $s;
            })
            ->values();   // <-- reindex numeric keys

        return view('components.card.attendance-card', [
            'students'            => $students,
            // …
        ]);

        return view('components.card.attendance-card', [
            'students' => $students,
            'schedules' => $schedules,
            'activeDate' => $date,
            'selectedSchedule' => $attendance?->schedule_id,
            'selectedDescription' => $attendance?->description,
        ]);
    }

    /** ── Simpan presensi ─────────────────────────── */
    public function store(Classroom $classroom, Request $r)
    {
        /* 1. Validasi input */
        $r->validate([
            'attendance_date'            => 'required|date',
            'schedule_id'                => 'required|exists:schedules,id',
            'description'                => 'nullable|string|max:200',

            /* array “attendance” = list siswa */
            'attendance'                 => 'required|array|min:1',
            'attendance.*.student_id'    => 'required|exists:students,id',
            'attendance.*.status'        => 'required|in:hadir,ijin,sakit,alpha',
        ]);

        /* 2. Simpan dalam transaksi */
        DB::transaction(function () use ($r, $classroom) {
            foreach ($r->attendance as $row) {

                Attendance::updateOrCreate(
                    [   // kunci unik
                        'attendance_date' => $r->attendance_date,
                        'classroom_id'    => $classroom->id,
                        'student_id'      => $row['student_id'],
                    ],
                    [   // nilai yg di-update jika ada
                        'status'      => $row['status'],
                        'schedule_id' => $r->schedule_id,
                        'description' => $r->description,
                    ]
                );
            }
        });

        /* 3. Redirect balik dgn notifikasi */
        return back()->with('success', 'Presensi berhasil disimpan!');
    }
    public function getAttendanceData(Request $request, $classroomId)
    {
        $date = $request->query('date');
        $attendance = Attendance::where('classroom_id', $classroomId)
            ->where('attendance_date', $date)
            ->first();

        $students = Student::where('classroom_id', $classroomId)->get();

        return response()->json([
            'students' => $students,
            'selectedSchedule' => $attendance->schedule_id ?? null,
            'selectedDescription' => $attendance->description ?? '',
        ]);
    }
    public function ajax(Classroom $classroom, Request $request)
    {
        $date = $request->query('date');
        $attendance = $date ? Attendance::where('classroom_id', $classroom->id)
            ->where('attendance_date', $date)
            ->first() : null;

        $totalDays = Attendance::where('classroom_id', $classroom->id)
            ->distinct('attendance_date')
            ->count('attendance_date');

        $students = $date ? $classroom->students()
            ->select('students.id', 'students.name')
            ->withCount([
                'attendances as total_present' => fn($q) => $q
                    ->where('classroom_id', $classroom->id)
                    ->where('status', 'hadir')
            ])
            ->get()
            ->map(function ($s) use ($date, $classroom, $totalDays) {
                $att = Attendance::where('classroom_id', $classroom->id)
                    ->where('student_id', $s->id)
                    ->where('attendance_date', $date)
                    ->first();
                $s->statusToday = $att?->status;
                $s->totalPresent = $s->total_present ?? 0;
                $s->percentage = $totalDays > 0
                    ? round(($s->total_present / $totalDays) * 100, 2) . '%'
                    : '0%';
                return $s;
            })
            ->toArray() : [];

        $highlightedDates = Attendance::where('classroom_id', $classroom->id)
            ->distinct('attendance_date')
            ->pluck('attendance_date')
            ->toArray();

        return response()->json([
            'students' => $students,
            'selectedSchedule' => $attendance?->schedule_id,
            'selectedDescription' => $attendance?->description,
            'highlightedDates' => $highlightedDates,
        ]);
    }
}
