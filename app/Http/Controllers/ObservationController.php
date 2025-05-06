<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ObservationController extends Controller
{
    public function getObservationList(){
        return [
            [
                'id' => 0,
                'title' => 'Tema 1 : Hewan Berkaki 4',
                'description' => 'Mempelajari berbagai hewan berkaki empat, habitat, dan ciri-cirinya.',
                'date' => '3 Maret - 7 Maret 2025',
            ],
            [
                'id' => 1,
                'title' => 'Tema 2 : Tumbuhan Hijau',
                'description' => 'Mengenal beragam tumbuhan hijau, proses fotosintesis, dan perannya dalam kehidupan.',
                'date' => '10 Maret - 15 Maret 2025',
            ],
            [
                'id' => 2,
                'title' => 'Tema 3 : Serangga di Sekitar Kita',
                'description' => 'Mengamati berbagai jenis serangga dan manfaatnya bagi lingkungan.',
                'date' => '16 Maret - 23 Maret 2025',
            ],
            [
                'id' => 3,
                'title' => 'Tema 4 : Buah dan Sayuran Sehat',
                'description' => 'Belajar tentang jenis-jenis buah dan sayur serta manfaat kesehatannya.',
                'date' => '10 April - 23 April 2025',
            ],
            [
                'id' => 4,
                'title' => 'Tema 1 : Lingkungan Bersih',
                'description' => 'Menanamkan kesadaran menjaga kebersihan lingkungan sejak dini.',
                'date' => '24 April - 1 Mei 2025',
            ],
            [
                'id' => 5,
                'title' => 'Tema 2 : Petualangan di Laut',
                'description' => 'Mengenal kehidupan bawah laut dan pentingnya menjaga ekosistem laut.',
                'date' => '2 Mei - 12 Mei 2025',
            ],
        ];
    }

    public function fetchObservationList()
{
    return $this->getObservationList();
}
}
