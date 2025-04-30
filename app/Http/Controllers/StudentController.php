<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function getStudentList()
    { return [
        [
            'id' => 1,
            'nama' => 'Anita Silalahi',
            'tanggal_lahir' => '2013-05-29',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Kota Malang',
            'nama_orang_tua' => 'Jansen Silalahi',
            'nomor_hp_orang_tua' => '08123456789',
            'alergi' => 'Alergi Kacang',
            'foto' => 'anita.jpg',
        ],
        [
            'id' => 2,
            'nama' => 'Raka Permana',
            'tanggal_lahir' => '2012-02-12',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Surabaya',
            'nama_orang_tua' => 'Dian Permana',
            'nomor_hp_orang_tua' => '082198765432',
            'alergi' => 'Tidak ada',
            'foto' => 'raka.jpg',
        ],
        [
            'id' => 3,
            'nama' => 'Siti Nurhaliza',
            'tanggal_lahir' => '2013-08-08',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Bandung',
            'nama_orang_tua' => 'Asep Nurhalim',
            'nomor_hp_orang_tua' => '081277788899',
            'alergi' => 'Debu',
            'foto' => 'siti.jpg',
        ],
        [
            'id' => 4,
            'nama' => 'Bima Pradipta',
            'tanggal_lahir' => '2014-03-17',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jakarta Selatan',
            'nama_orang_tua' => 'Rina Pradipta',
            'nomor_hp_orang_tua' => '083112223333',
            'alergi' => 'Laktosa',
            'foto' => 'bima.jpg',
        ],
        [
            'id' => 5,
            'nama' => 'Lestari Ayuningtyas',
            'tanggal_lahir' => '2013-01-05',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Yogyakarta',
            'nama_orang_tua' => 'Suryo Ayuningtyas',
            'nomor_hp_orang_tua' => '081355566677',
            'alergi' => 'Tidak ada',
            'foto' => 'lestari.jpg',
        ],
        [
            'id' => 6,
            'nama' => 'Fikri Ramadhan',
            'tanggal_lahir' => '2012-04-20',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Bekasi',
            'nama_orang_tua' => 'Wahyudi Ramadhan',
            'nomor_hp_orang_tua' => '082122233344',
            'alergi' => 'Alergi Udang',
            'foto' => 'fikri.jpg',
        ],
        [
            'id' => 7,
            'nama' => 'Tania Salsabila',
            'tanggal_lahir' => '2013-11-02',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Depok',
            'nama_orang_tua' => 'Rizky Salsabila',
            'nomor_hp_orang_tua' => '081234567812',
            'alergi' => 'Alergi Telur',
            'foto' => 'tania.jpg',
        ],
        [
            'id' => 8,
            'nama' => 'Andra Putra',
            'tanggal_lahir' => '2014-06-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Tangerang',
            'nama_orang_tua' => 'Putri Andayani',
            'nomor_hp_orang_tua' => '082122111444',
            'alergi' => 'Tidak ada',
            'foto' => 'andra.jpg',
        ],
        [
            'id' => 9,
            'nama' => 'Clarissa Dwi',
            'tanggal_lahir' => '2012-09-30',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Bogor',
            'nama_orang_tua' => 'Dwi Kartika',
            'nomor_hp_orang_tua' => '081266655544',
            'alergi' => 'Debu dan serbuk sari',
            'foto' => 'clarissa.jpg',
        ],
        [
            'id' => 10,
            'nama' => 'Zaki Ahmad',
            'tanggal_lahir' => '2013-03-10',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Cimahi',
            'nama_orang_tua' => 'Ahmad Yusuf',
            'nomor_hp_orang_tua' => '083344455566',
            'alergi' => 'Alergi gluten',
            'foto' => 'zaki.jpg',
        ],
    ];
}

public function fetchStudentList()
{
    return $this->getStudentList();
}

}
