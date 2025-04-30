<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getParentList()
    {
        return [
            [
                'id' => 1,
                'nama' => 'Andi Saputra',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_telepon' => '081234567890',
                'tanggal_lahir' => '1985-06-15',
                'alamat' => 'Jl. Melati No.12, Jakarta',
                'pekerjaan' => 'Pegawai Negeri'
            ],
            [
                'id' => 2,
                'nama' => 'Siti Rahmawati',
                'jenis_kelamin' => 'Perempuan',
                'nomor_telepon' => '082233445566',
                'tanggal_lahir' => '1990-08-21',
                'alamat' => 'Jl. Anggrek No.5, Bandung',
                'pekerjaan' => 'Guru'
            ],
            [
                'id' => 3,
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_telepon' => '081355667788',
                'tanggal_lahir' => '1978-12-03',
                'alamat' => 'Jl. Mawar No.7, Surabaya',
                'pekerjaan' => 'Wiraswasta'
            ],
            [
                'id' => 4,
                'nama' => 'Dewi Kartika',
                'jenis_kelamin' => 'Perempuan',
                'nomor_telepon' => '085755443322',
                'tanggal_lahir' => '1982-04-17',
                'alamat' => 'Jl. Dahlia No.23, Yogyakarta',
                'pekerjaan' => 'Dokter'
            ],
            [
                'id' => 5,
                'nama' => 'Rahmat Hidayat',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_telepon' => '087812345678',
                'tanggal_lahir' => '1987-09-10',
                'alamat' => 'Jl. Kenanga No.9, Medan',
                'pekerjaan' => 'Pengusaha'
            ],
            [
                'id' => 6,
                'nama' => 'Lina Marlina',
                'jenis_kelamin' => 'Perempuan',
                'nomor_telepon' => '089912345670',
                'tanggal_lahir' => '1995-02-25',
                'alamat' => 'Jl. Teratai No.1, Makassar',
                'pekerjaan' => 'Perawat'
            ],
            [
                'id' => 1,
                'nama' => 'Andi Saputra',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_telepon' => '081234567890',
                'tanggal_lahir' => '1985-06-15',
                'alamat' => 'Jl. Melati No.12, Jakarta',
                'pekerjaan' => 'Pegawai Negeri'
            ],
            [
                'id' => 2,
                'nama' => 'Siti Rahmawati',
                'jenis_kelamin' => 'Perempuan',
                'nomor_telepon' => '082233445566',
                'tanggal_lahir' => '1990-08-21',
                'alamat' => 'Jl. Anggrek No.5, Bandung',
                'pekerjaan' => 'Guru'
            ],
            [
                'id' => 3,
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_telepon' => '081355667788',
                'tanggal_lahir' => '1978-12-03',
                'alamat' => 'Jl. Mawar No.7, Surabaya',
                'pekerjaan' => 'Wiraswasta'
            ],
            [
                'id' => 4,
                'nama' => 'Dewi Kartika',
                'jenis_kelamin' => 'Perempuan',
                'nomor_telepon' => '085755443322',
                'tanggal_lahir' => '1982-04-17',
                'alamat' => 'Jl. Dahlia No.23, Yogyakarta',
                'pekerjaan' => 'Dokter'
            ],
            [
                'id' => 5,
                'nama' => 'Rahmat Hidayat',
                'jenis_kelamin' => 'Laki-laki',
                'nomor_telepon' => '087812345678',
                'tanggal_lahir' => '1987-09-10',
                'alamat' => 'Jl. Kenanga No.9, Medan',
                'pekerjaan' => 'Pengusaha'
            ],
            [
                'id' => 6,
                'nama' => 'Lina Marlina',
                'jenis_kelamin' => 'Perempuan',
                'nomor_telepon' => '089912345670',
                'tanggal_lahir' => '1995-02-25',
                'alamat' => 'Jl. Teratai No.1, Makassar',
                'pekerjaan' => 'Perawat'
            ]
        ];
    }

    public function fetchParentList()
    {
        $parents = $this->getParentList();
        return view('Admin.index', compact('parents'));
    }
    
}
