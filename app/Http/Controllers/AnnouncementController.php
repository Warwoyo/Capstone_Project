<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    private function getAnnouncementList()
    {
        return [
            [
                'id' => 0,
                'title' => 'Membawa Baju Batik',
                'description' => 'Digunakan Untuk perayaan hari pangan nasional.Digunakan Untuk perayaan hari pangan nasional.Digunakan Untuk perayaan hari pangan nasional.Digunakan Untuk perayaan hari pangan nasional.Digunakan Untuk perayaan hari pangan nasional',
                'date' => '7 Maret 2025',
                'timestamp' => '7 Maret 2025',
            ],
            [
                'id' => 1,
                'title' => 'PAUD Libur',
                'description' => 'Sehubungan dengan hari buruh , kegiatan PAUD diliburkan',
                'date' => '15 Maret 2025',
                'timestamp' => '7 Maret 2025',
            ],
            [
                'id' => 2,
                'title' => 'Menggambar',
                'description' => 'Anak anak PAUD diharapkan membawa pensil warna',
                'date' => '23 Maret 2025',
                'timestamp' => '7 Maret 2025',
            ],
            [
                'id' => 3,
                'title' => 'Menyelam',
                'description' => 'Anak anak PAUD membawa baju renang',
                'date' => '23 April 2025',
                'timestamp' => '7 Maret 2025',
            ],
            [
                'id' => 4,
                'title' => 'Taman Indah ',
                'description' => 'Anak anak PAUD bawa Bunga ',
                'date' => '1 Mei 2025',
                'timestamp' => '7 Maret 2025',
            ],
            [
                'id' => 5,
                'title' => 'Petualangan di Laut',
                'description' => 'Anak anak PAUD membawa baju selam',
                'date' => '12 Mei 2025',
                'timestamp' => '7 Maret 2025',
            ],
        ];
    }

    public function fetchAnnouncementList()
{
    return $this->getAnnouncementList();
}
 
   
}
