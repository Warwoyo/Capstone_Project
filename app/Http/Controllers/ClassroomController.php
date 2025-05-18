<?php

namespace App\Http\Controllers;
use App\Models\Announcement;
use App\Models\Classroom;   // <--  tambahkan baris ini
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classroom = Classroom::with(['owner:id,name'])
                    ->withCount('students')
                    ->orderBy('name')
                    ->get();

        $teachers = \App\Models\User::where('role', 'teacher')->get(['id', 'name']);

        return view('Classroom.index', compact('classroom', 'teachers'));
    }
    public function showClassroomDetail($class, $tab)
    {
        $classroom = Classroom::with('owner:id,name')->findOrFail($class);
        
        $data = [
            'class'            => $classroom,
            'tab'              => strtolower($tab),
            'announcementList' => collect(),
            'scheduleList'     => collect(),
            'attendanceList'   => collect(),
            'observationList'  => collect(),
            'reportList'       => collect(),
            'syllabusList'     => collect(), 
            'studentList'      => collect(),
        ];

        switch ($data['tab']) {
            case 'pengumuman':
                $data['announcementList'] = $classroom->announcements()->latest()->get();
                break;

            case 'jadwal':
                $data['scheduleList'] = $classroom->schedules()
                                        ->with('details')
                                        ->orderBy('created_at')
                                        ->get();
                break;

            case 'presensi':
                $today = now()->toDateString();
                $data['attendanceList'] = $classroom->attendances()
                                        ->whereDate('attendance_date', $today)
                                        ->with('student')
                                        ->get();
                break;

            case 'observasi':
                $data['observationList'] = $classroom->observations()
                                        ->with(['student', 'scheduleDetail'])
                                        ->latest()
                                        ->get();
                break;

            case 'rapor':
                $data['reportList'] = \App\Models\Report::whereIn('student_id', $classroom->students->pluck('id'))
                                        ->with('student')
                                        ->latest()
                                        ->get();
                break;

            case 'peserta':
                $data['studentList'] = $classroom->students()
                                         ->with(['parents', 'registrationTokens'])
                                         ->orderBy('name')
                                         ->get();
                break;
                
            
                
        }

        return view('Classroom.classroom-detail', $data);
    }
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('Classrooms.index', compact('teachers'));
    }
    public function store(Request $r)
    {
        $r->validate([
            'name'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'owner_id'    => 'required|exists:users,id',
        ]);
    
        $classroom = \App\Models\Classroom::create([
            'name'        => $r->name,
            'description' => $r->description,
            'owner_id'    => $r->owner_id,
        ]);
    
        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dibuat!');
    }
    
    public function studentsTab(Classroom $class)
    {
        // ambil token terakhir per siswa
        $class->load([
            'students.registrationTokens' => fn($q) => $q->latest()->limit(1)
        ]);
        
        return view('Classroom.components.tab-students', [
            'class'        => $class,
            'studentList'  => $class->students  // collection Student model
        ]);
    }

}
