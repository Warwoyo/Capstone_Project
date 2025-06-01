<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Alumni;
use App\Models\Announcement;
use App\Models\ParentProfile;
use App\Models\Observation;
use App\Models\Syllabus;
use Illuminate\Support\Facades\DB;

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
        $studentIds = DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
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
        $studentIds = DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        $children = Student::whereIn('id', $studentIds)
            ->with([
                'classrooms',
                'parentProfiles' => function($query) {
                    $query->orderBy('relation');
                }
            ])
            ->get();
        
        return view('Orangtua.children', compact('children'));
    }

    public function scheduleParent()
    {
        $user = auth()->user();
        
        // Get children of the authenticated parent
        $studentIds = DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
        $children = Student::whereIn('id', $studentIds)->with([
            'classrooms.schedules' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'classrooms.schedules.classroom'
        ])->get();
        
        // Flatten all schedules from all children's classrooms
        $allSchedules = collect();
        foreach ($children as $child) {
            foreach ($child->classrooms as $classroom) {
                foreach ($classroom->schedules as $schedule) {
                    $schedule->student_name = $child->name;
                    $schedule->classroom_name = $classroom->name;
                    $allSchedules->push($schedule);
                }
            }
        }
        
        // Sort by creation date
        $schedules = $allSchedules->sortByDesc('created_at');
        
        return view('Orangtua.schedule', compact('children', 'schedules'));
    }

    public function attendanceParent()
    {
        try {
            $user = auth()->user();
            
            // Ambil students melalui tabel student_user
            $studentIds = DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
            $students = Student::whereIn('id', $studentIds)->with(['attendances.schedule', 'classrooms'])->get();
            
            if ($students->isEmpty()) {
                return view('Orangtua.attendance', [
                    'selectedStudent' => null,
                    'attendanceData' => []
                ]);
            }

            // Ambil siswa pertama
            $firstStudent = $students->first();
            $primaryClassroom = $firstStudent->classrooms->first();
            
            // Format data presensi
            $attendanceData = $firstStudent->attendances->map(function($record) {
                return [
                    'date' => $record->attendance_date->format('Y-m-d'),
                    'theme' => $record->schedule->title ?? $record->description ?? 'Tidak ada tema',
                    'status' => ucfirst($record->status)
                ];
            })->toArray();
            
            $selectedStudent = [
                'name' => $firstStudent->name,
                'class' => $primaryClassroom->name ?? 'Tidak ada kelas'
            ];
            
            return view('Orangtua.attendance', [
                'selectedStudent' => $selectedStudent,
                'attendanceData' => $attendanceData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in attendanceParent:', ['error' => $e->getMessage()]);
            
            return view('Orangtua.attendance', [
                'selectedStudent' => null,
                'attendanceData' => []
            ]);
        }
    }

    public function syllabusParent()
    {
        try {
            $user = auth()->user();
            
            // Ambil students melalui tabel student_user
            $studentIds = DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
            $students = Student::whereIn('id', $studentIds)->with(['classrooms.syllabuses'])->get();
            
            if ($students->isEmpty()) {
                return view('Orangtua.syllabus', [
                    'syllabusList' => []
                ]);
            }

            $syllabusList = [];
            foreach ($students as $student) {
                foreach ($student->classrooms as $classroom) {
                    if ($classroom->syllabuses && $classroom->syllabuses->isNotEmpty()) {
                        foreach ($classroom->syllabuses as $syllabus) {
                            $syllabusList[] = [
                                'id' => $syllabus->id,
                                'title' => $syllabus->title,
                                'file_name' => $syllabus->file_name,
                                'file_path' => $syllabus->file_path,
                                'created_at' => $syllabus->created_at->format('Y-m-d H:i:s'),
                                'classroom_name' => $classroom->name
                            ];
                        }
                    }
                }
            }
            
            // Sort by created_at descending
            usort($syllabusList, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return view('Orangtua.syllabus', [
                'syllabusList' => $syllabusList
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in syllabusParent:', ['error' => $e->getMessage()]);
            
            return view('Orangtua.syllabus', [
                'syllabusList' => []
            ]);
        }
    }

    public function observationParent()
    {
        try {
            $user = auth()->user();
            
            // Ambil students melalui tabel student_user
            $studentIds = DB::table('student_user')->where('user_id', $user->id)->pluck('student_id');
            $students = Student::whereIn('id', $studentIds)->with(['classrooms'])->get();
            
            if ($students->isEmpty()) {
                return view('Orangtua.observation', [
                    'observationList' => []
                ]);
            }

            // Ambil semua classroom IDs dari anak-anak user
            $classroomIds = $students
                ->flatMap(function($student) {
                    return $student->classrooms->pluck('id');
                })
                ->unique()
                ->filter();

            if ($classroomIds->isEmpty()) {
                return view('Orangtua.observation', [
                    'observationList' => []
                ]);
            }

            // Ambil observations dari classroom yang relevan
            $observations = \App\Models\Observation::whereIn('schedule_id', function($query) use ($classroomIds) {
                    $query->select('id')
                          ->from('schedules')
                          ->whereIn('classroom_id', $classroomIds);
                })
                ->whereIn('student_id', $studentIds)
                ->with(['schedule', 'student', 'scheduleDetail'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format data untuk view
            $observationList = $observations->map(function($observation) {
                return [
                    'id' => $observation->id,
                    'title' => $observation->schedule->title ?? 'Observasi',
                    'date' => $observation->created_at->format('d/m/Y'),
                    'description' => $observation->observation_text ?? $observation->description ?? 'Tidak ada deskripsi observasi',
                    'score_text' => $this->getScoreText($observation->score ?? 0),
                    'student_name' => $observation->student->name ?? ''
                ];
            })->toArray();

            return view('Orangtua.observation', [
                'observationList' => $observationList
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in observationParent:', ['error' => $e->getMessage()]);
            
            return view('Orangtua.observation', [
                'observationList' => []
            ]);
        }
    }

    /**
     * Helper method untuk mengkonversi skor ke teks penilaian
     */
    private function getScoreText($score)
    {
        if ($score >= 4) {
            return 'Sangat Baik (A)';
        } elseif ($score >= 3) {
            return 'Baik (B)';
        } elseif ($score >= 2) {
            return 'Cukup (C)';
        } elseif ($score >= 1) {
            return 'Kurang (D)';
        } else {
            return 'Belum dinilai';
        }
    }
}