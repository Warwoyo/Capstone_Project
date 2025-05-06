<?php

namespace App\Http\Controllers;

use App\Models\Classroom;   // <--  tambahkan baris ini
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classroom = Classroom::withCount('students')
                     ->orderBy('name')
                     ->get();

        return view('Classroom.index', compact('classroom'));
    }
    public function showClassroomDetail($class, $tab)
    {
        $classroom = Classroom::withCount('students')->findOrFail($class);

        // siapkan semua variabel supaya view tidak undefined
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
                                    ->orderBy('start_date')->get();
                break;

            case 'presensi':
                // presensi hari ini; ubah kalau mau rentang
                $today = now()->toDateString();
                $data['attendanceList'] = $classroom->attendances()
                                        ->whereDate('attendance_date', $today)
                                        ->with('student')
                                        ->get();
                break;

            case 'observasi':
                $data['observationList'] = $classroom->observations()
                                        ->latest()->with('student')->get();
                break;

            case 'rapor':
                $data['reportList'] = $classroom->reports()
                                    ->latest('semester')->with('student')->get();
                break;

            case 'peserta':
                $data['studentList'] = $classroom->students()
                                    ->with('mother','father')->get();
                break;

            case 'silabus':
                $data['syllabusList'] = $classroom->syllabuses()->get();
                break;
        }

        return view('Classroom.classroom-detail', $data);
    }


}
