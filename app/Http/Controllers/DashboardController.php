<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Alumni;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
            case 'teacher':
                return view('Dashboard.index', [
                    'activeStudentCount' => Student::count(),
                    'alumniCount' => Alumni::count(),
                    'announcementList' => Announcement::latest()->get(),
                ]);
            case 'parent':
                // Load announcement list dari kelas anak untuk dashboard orang tua
                $announcementList = $this->getAnnouncementsFromChildren($user);
                return view('Orangtua.index', compact('user', 'announcementList'));
            default:
                abort(403);
        }
    }

    /**
     * Helper method untuk mendapatkan pengumuman dari kelas anak-anak
     */
        private function getAnnouncementsFromChildren($user)
    {
        // Debug: Cek detail students dengan relasi yang benar
        \Log::info('User ID: ' . $user->id);
        
        // Ambil students melalui tabel student_user
        $studentIds = \DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        \Log::info('Student IDs from student_user: ' . $studentIds->toJson());
        
        if ($studentIds->isEmpty()) {
            \Log::info('No students found for user');
            return [];
        }

        // Ambil data students dengan classrooms (many-to-many)
        $students = Student::whereIn('id', $studentIds)->with('classrooms')->get();
        \Log::info('Students count: ' . $students->count());
        
        // Debug: Cek detail setiap student
        foreach ($students as $index => $student) {
            \Log::info("Student {$index}: ID={$student->id}, Name={$student->name}, Classrooms count={$student->classrooms->count()}");
        }

        // Ambil semua classroom IDs dari anak-anak user
        $classroomIds = $students
            ->flatMap(function($student) {
                return $student->classrooms->pluck('id');
            })
            ->unique()
            ->filter();

        \Log::info('Classroom IDs: ' . $classroomIds->toJson());
        if ($classroomIds->isEmpty()) {
            \Log::info('No valid classrooms found');
            
            // Fallback: tampilkan pengumuman umum jika ada
            $announcements = Announcement::whereNull('classroom_id')
                ->with(['classroom'])
                ->latest()
                ->take(5)
                ->get();
                
            \Log::info('Showing global announcements: ' . $announcements->count());
            return $announcements;
        }

        // Ambil announcements dari classroom yang relevan
        $announcements = Announcement::whereIn('classroom_id', $classroomIds)
            ->with(['classroom'])
            ->latest()
            ->take(10)
            ->get();

        \Log::info('Announcements found: ' . $announcements->count());
        return $announcements;
    }

    public function childrenParent()
    {
        $user = auth()->user();
        
        // Ambil data anak melalui tabel student_user
        $studentIds = \DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        $children = Student::whereIn('id', $studentIds)->with(['classrooms'])->get();
        
        return view('Orangtua.children', compact('children'));
    }

    public function scheduleParent()
    {
        $user = auth()->user();
        
        // Ambil students melalui tabel student_user
        $studentIds = \DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        $students = Student::whereIn('id', $studentIds)->with(['classrooms.schedules.scheduleDetails'])->get();
        
        $scheduleList = [];
        foreach ($students as $student) {
            foreach ($student->classrooms as $classroom) {
                if ($classroom->schedules->isNotEmpty()) {
                    $scheduleList = array_merge($scheduleList, $classroom->schedules->toArray());
                }
            }
        }
        
        return view('Orangtua.schedule', compact('scheduleList'));
    }

    public function attendanceParent()
    {
        $user = auth()->user();
        
        // Ambil students melalui tabel student_user
        $studentIds = \DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        $students = Student::whereIn('id', $studentIds)->with(['attendances.schedule', 'classrooms'])->get();
        
        $attendanceData = [];
        foreach ($students as $student) {
            if ($student->attendances->isNotEmpty()) {
                $attendanceData[] = [
                    'student' => $student,
                    'attendance' => $student->attendances
                ];
            }
        }
        
        // Untuk sementara, ambil data siswa pertama jika ada
        $selectedStudent = null;
        if (!empty($attendanceData)) {
            $firstStudent = $attendanceData[0]['student'];
            $primaryClassroom = $firstStudent->classrooms->first();
            $selectedStudent = [
                'name' => $firstStudent->name,
                'class' => $primaryClassroom->name ?? 'Tidak ada kelas',
                'attendance' => $attendanceData[0]['attendance']->map(function($record) {
                    return [
                        'date' => $record->attendance_date,
                        'theme' => $record->schedule->title ?? 'Tidak ada tema',
                        'status' => $record->status
                    ];
                })->toArray()
            ];
        }
        
        return view('Orangtua.attendance', compact('selectedStudent'));
    }

    public function syllabusParent()
    {
        $user = auth()->user();
        
        // Ambil students melalui tabel student_user
        $studentIds = \DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        $students = Student::whereIn('id', $studentIds)->with(['classrooms.syllabuses'])->get();
        
        $syllabusList = [];
        foreach ($students as $student) {
            foreach ($student->classrooms as $classroom) {
                if ($classroom->syllabuses && $classroom->syllabuses->isNotEmpty()) {
                    $syllabusList = array_merge($syllabusList, $classroom->syllabuses->toArray());
                }
            }
        }
        
        return view('Orangtua.syllabus', compact('syllabusList'));
    }
}