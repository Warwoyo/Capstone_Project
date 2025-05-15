<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistrationToken; 

class ClassroomController extends Controller
{

    // Private method untuk mendapatkan daftar kelas
    private function getClassroomList()
    {
        return [
            [
                'id' => 0,
                'title' => 'Kelas Pelangi Ceria',
                'description' => 'Meningkatkan kreativitas melalui seni, musik, dan permainan seru yang mengenalkan warna dan ekspresi diri',
            ],
            [
                'id' => 1,
                'title' => 'Kelas Bintang Pintar',
                'description' => 'Mengasah keterampilan membaca, menulis, dan berhitung dasar dengan cara yang menyenangkan dan interaktif',
            ],
            [
                'id' => 2,
                'title' => 'Kelas Hutan Ceria',
                'description' => 'Mengenalkan anak pada keajaiban alam, tumbuhan, dan binatang melalui eksplorasi serta cerita menarik',
            ],
            [
                'id' => 3,
                'title' => 'Kelas Matahari Bersinar',
                'description' => 'Melatih rasa percaya diri dan kemandirian anak melalui kegiatan kelompok yang penuh semangat',
            ],
            [
                'id' => 4,
                'title' => 'Kelas Pelangi Ceria',
                'description' => 'Meningkatkan kreativitas melalui seni, musik, dan permainan seru yang mengenalkan warna dan ekspresi diri',
            ],
            [
                'id' => 5,
                'title' => 'Kelas Bintang Pintar',
                'description' => 'Mengasah keterampilan membaca, menulis, dan berhitung dasar dengan cara yang menyenangkan dan interaktif',
            ],
            [
                'id' => 6,
                'title' => 'Kelas Hutan Ceria',
                'description' => 'Mengenalkan anak pada keajaiban alam, tumbuhan, dan binatang melalui eksplorasi serta cerita menarik',
            ],
        ];
    }

    // Function index untuk menampilkan semua kelas
    public function index()
    {
        $classroom = $this->getClassroomList();
        $tab = 'pengumuman'; // default
        return view('Classroom.index', compact('classroom', 'tab'));
    }
    

    // Function detail per kelas + tab
    public function showClassroomDetail($classId, $tab)
{
    
    
    $classroomList = $this->getClassroomList();
    $class = collect($classroomList)->firstWhere('id', $classId);
    
    // dd($tab, $classId);
    if (!$class) {
        abort(404, 'Kelas tidak ditemukan.');
    }

    // Tentukan tabs yang akan digunakan
    $tabs = ['Pengumuman', 'Presensi', 'Jadwal', 'Observasi', 'Rapor', 'Peserta', 'Silabus'];

    // Tentukan data untuk tab tertentu
    $data = match ($tab) {
        'pengumuman' => ['Pengumuman A', 'Pengumuman B'],
        'presensi' => ['Presensi A', 'Presensi B'],
        'observasi' => ['Observasi A', 'Observasi B'],
        'rapor' => ['Rapor A', 'Rapor B'],
        default => [],
    };

    $scheduleList = [];
    $announcementList= [];
    $studentList= [];


         $scheduleController = new ScheduleController();
         $scheduleList = $scheduleController->fetchScheduleList();

         $AnnouncementController = new AnnouncementController();
         $announcementList = $AnnouncementController->fetchAnnouncementList();

         $StudentController = new StudentController();
         $studentList = $StudentController->fetchStudentList();

         $ObservationController = new ObservationController();
         $observationList = $ObservationController->fetchObservationList();

         $ReportController = new ReportController();
         $semesterList = $ReportController->fetchSemesterList();



    
     //dd(compact('class', 'tab', 'data', 'classId', 'tabs','scheduleList','announcementList','studentList','observationList','semesterList'));

    return view('Classroom.classroom-detail', compact('class', 'tab', 'data', 'classId', 'tabs','scheduleList','announcementList','studentList','observationList','semesterList'));
}
}
