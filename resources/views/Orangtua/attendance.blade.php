
@php
$selectedStudent = [
    'name' => 'Ahmad Fauzi',
    'class' => 'Kelas Pelangi Ceria',
    'attendance' => [
        ['date' => '2025-05-23', 'theme' => 'Makan Nasi Padang Enak Sekali', 'status' => 'Hadir'],
        ['date' => '2025-05-24', 'theme' => 'Berkebun', 'status' => 'Hadir'],
        ['date' => '2025-05-25', 'theme' => 'Belajar Huruf', 'status' => 'Izin'],
        ['date' => '2025-05-26', 'theme' => 'Belajar Angka', 'status' => 'Hadir'],
        ['date' => '2025-05-27', 'theme' => 'Mewarnai Gambar', 'status' => 'Hadir'],
        ['date' => '2025-05-28', 'theme' => 'Bermain di Taman', 'status' => 'Sakit'],
        ['date' => '2025-05-29', 'theme' => 'Membuat Origami', 'status' => 'Hadir'],
        ['date' => '2025-05-30', 'theme' => 'Mengenal Binatang', 'status' => 'Hadir'],
        ['date' => '2025-06-01', 'theme' => 'Menonton Film Edukasi', 'status' => 'Alpha'],
        ['date' => '2025-06-02', 'theme' => 'Menari Tradisional', 'status' => 'Hadir'],
        ['date' => '2025-06-03', 'theme' => 'Menyanyi Lagu Anak', 'status' => 'Hadir'],
        ['date' => '2025-06-04', 'theme' => 'Prakarya Sederhana', 'status' => 'Izin'],
        ['date' => '2025-06-05', 'theme' => 'Mengenal Warna', 'status' => 'Hadir'],
        ['date' => '2025-06-06', 'theme' => 'Berolahraga', 'status' => 'Hadir'],
        ['date' => '2025-06-07', 'theme' => 'Membaca Cerita Anak', 'status' => 'Hadir'],
        ['date' => '2025-06-08', 'theme' => 'Bermain Puzzle', 'status' => 'Hadir'],
        ['date' => '2025-06-09', 'theme' => 'Mengenal Profesi', 'status' => 'Sakit'],
        ['date' => '2025-06-10', 'theme' => 'Belajar Menyikat Gigi', 'status' => 'Hadir'],
        ['date' => '2025-06-11', 'theme' => 'Simulasi Lalu Lintas', 'status' => 'Alpha'],
        ['date' => '2025-06-12', 'theme' => 'Membuat Kolase', 'status' => 'Hadir'],
        ['date' => '2025-06-13', 'theme' => 'Kegiatan Bebas Bermain Air', 'status' => 'Hadir'],
    ],
];
@endphp

@extends('layouts.dashboard')

@section('content')
<!-- ini dashboard orang tua -->
<main class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
<div
    x-data="{
        selectedDate: '',
        tema: '',
        keterangan: ''
    }" 
    x-init="
        flatpickr('#tanggal', {
            dateFormat: 'Y-m-d',
            altFormat: 'd M Y',
            locale: 'id',
            onChange: function(selectedDates, dateStr) {
                selectedDate = dateStr;
            }
        });
    "
    class="flex-1 p-5 max-md:p-2.5 max-sm:p-2.5"
>

    {{-- Header Logo --}}
    <header class="flex gap-3 items-center flex-wrap mt-11 md:mt-0">
        <img 
            src="https://cdn.builder.io/api/v1/image/assets/TEMP/7c611c0665bddb8f69e3b35c80f5477a6f0b559e?placeholderIfAbsent=true" 
            alt="PAUD Logo" 
            class="h-12 w-auto max-w-[60px]"
        />
        <div class="flex flex-col">
            <h1 class="text-[24px] md:text-2xl font-bold text-sky-600">PAUD Kartika Pradana</h1>
            <p class="text-[8px] text-sky-800">
                Taman Penitipan Anak, Kelompok Bermain, dan Taman Kanak-Kanak
            </p>
        </div>
    </header>

    <x-header.parent-breadcrump-header label="Presensi" />

   <!-- Container Utama -->
<div class="flex-1 w-full md:px-10 pt-2">
    <!-- Info Siswa -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
        <h3 class="text-base sm:text-lg font-semibold text-sky-700">
            Nama: {{ $selectedStudent['name'] }}
        </h3>
        <h3 class="text-base sm:text-lg font-semibold text-sky-700">
            Kelas: {{ $selectedStudent['class'] }}
        </h3>
    </div>

    <!-- Scroll horizontal dan vertikal -->
    <div class="w-full overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-y-auto max-h-[500px] md:max-h-[400px] rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full table-auto text-sm text-slate-600">
                    <!-- Header -->
                    <thead class="bg-sky-200 text-sky-800 font-medium">
                        <tr>
                            <th class="text-center px-4 py-2">Tanggal</th>
                            <th class="text-center px-4 py-2">Tema</th>
                            <th class="text-center px-4 py-2">Keterangan</th>
                        </tr>
                    </thead>

                    <!-- Body -->
                    <tbody>
                        @foreach ($selectedStudent['attendance'] as $record)
                            <tr class="border-t border-gray-200">
                                <td class="text-center px-4 py-2 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($record['date'])->translatedFormat('d F Y') }}
                                </td>
                                <td class="text-center px-4 py-2 whitespace-normal break-words">
                                    {{ $record['theme'] }}
                                </td>
                                <td class="text-center px-4 py-2 whitespace-nowrap">
                                    {{ $record['status'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




    <!-- Icon Header -->
    <x-header.icon-header />

</div>


    <!-- Header Icons -->
    <x-header.icon-header />
    

</main>
@endsection
