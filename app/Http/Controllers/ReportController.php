<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getSemesterList()
    {
        return [
            [
                'id' => 0,
                'semester' => 'Ganjil',
                'year'=>'2024/2025',
                'timeline'=>'29 Januari 2025 - 23 Juni 2025',
            ],
            [
                'id' => 1,
                'semester' => 'Genap',
                'year'=>'2024/2025',
                'timeline'=>'29 Agustus 2025 - 23 Desember 2025',
            ],

            [
                'id' => 2,
                'semester' => 'Ganjil',
                'year'=>'2025/2026',
                'timeline'=>'29 Januari 2026 - 23 Juni 2026',
            ],
            [
                'id' => 3,
                'semester' => 'Genap',
                'year'=>'2025/2026',
                'timeline'=>'29 Agustus 2026 - 23 Desember 2026',
            ],
            [
                'id' => 4,
                'semester' => 'Ganjil',
                'year'=>'2026/2027',
                'timeline'=>'29 Januari 2026 - 23 Juni 2027',
            ],
            [
                'id' => 5,
                'semester' => 'Genap',
                'year'=>'2026/2027',
                'timeline'=>'29 Agustus 2028 - 23 Desember 2028',
            ],

            [
                'id' => 6,
                'semester' => 'Ganjil',
                'year'=>'2027/2028',
                'timeline'=>'29 Januari 2028 - 23 Juni 2028',
            ],
            [
                'id' => 7,
                'semester' => 'Genap',
                'year'=>'2027/2028',
                'timeline'=>'29 Agustus 2028 - 23 Desember 2028',
            ],
        ];
    }

      public function fetchSemesterList()
{
    return $this->getSemesterList();
}
}
