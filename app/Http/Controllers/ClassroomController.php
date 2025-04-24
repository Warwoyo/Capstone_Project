<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classroom = [
            [
                'title' => 'Kelas Pelangi Ceria',
                'description' => 'Meningkatkan kreativitas melalui seni, musik, dan permainan seru yang mengenalkan warna dan ekspresi diri',
            ],
            [
                'title' => 'Kelas Bintang Pintar',
                'description' => 'Mengasah keterampilan membaca, menulis, dan berhitung dasar dengan cara yang menyenangkan dan interaktif',
            ],
            [
                'title' => 'Kelas Hutan Ceria',
                'description' => 'Mengenalkan anak pada keajaiban alam, tumbuhan, dan binatang melalui eksplorasi serta cerita menarik',
            ],
            [
                'title' => 'Kelas Matahari Bersinar',
                'description' => 'Melatih rasa percaya diri dan kemandirian anak melalui kegiatan kelompok yang penuh semangat',
            ],
            [
                'title' => 'Kelas Permata Hati',
                'description' => 'Membangun karakter dan keterampilan sosial anak dalam lingkungan penuh kasih',
            ],
            [
                'title' => 'Kelas Petualang Kecil',
                'description' => 'Mendorong rasa ingin tahu dan eksplorasi melalui eksperimen sederhana dan kegiatan imajinatif',
            ]
        ];

        return view('Classroom.index', compact('classroom'));
    }
}
