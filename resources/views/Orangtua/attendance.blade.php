
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
    class="flex-1 p-5 max-md:p-2.5 max-sm:p-2.5 overflow-y-auto hide-scrollbar max-h-[100vh] md:max-h-[100vh]"
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
<div class="mb-4">
    <a href="{{ url('/dashboard') }}" 
       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali ke Dashboard
    </a>
</div>
   <!-- Container Utama -->
<div class="flex-1 w-full md:px-10 pt-2">
    @if($selectedStudent)
        <!-- Info Siswa -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <h3 class="text-base sm:text-lg font-semibold text-sky-700">
                Nama: {{ $selectedStudent['name'] }}
            </h3>
            <h3 class="text-base sm:text-lg font-semibold text-sky-700">
                Kelas: {{ $selectedStudent['class'] }}
            </h3>
        </div>

        @if($attendanceData && count($attendanceData) > 0)
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
                                    <th class="text-center px-4 py-2">Status</th>
                                    <th class="text-center px-4 py-2">Deskripsi</th>
                                </tr>
                            </thead>

                            <!-- Body -->
                            <tbody>
                                @foreach ($attendanceData as $record)
                                    <tr class="border-t border-gray-200">
                                        <td class="text-center px-4 py-2 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($record['date'])->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-normal break-words">
                                            {{ $record['theme'] ?? '-' }}
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($record['status'] == 'hadir') bg-green-100 text-green-800
                                                @elseif($record['status'] == 'sakit') bg-yellow-100 text-yellow-800
                                                @elseif($record['status'] == 'izin') bg-blue-100 text-blue-800
                                                @elseif($record['status'] == 'alpha') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($record['status']) }}
                                            </span>
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-normal break-words">
                                            {{ $record['description'] ?? $record['keterangan'] ?? $record['deskripsi'] ?? $record['catatan'] ?? $record['notes'] ?? 'No description available' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Data Presensi</h3>
                <p class="text-gray-500">Tidak ada data presensi yang tersedia untuk anak ini.</p>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak Ada Data Anak</h3>
            <p class="text-gray-500">Tidak ada data anak yang terdaftar untuk akun ini.</p>
        </div>
    @endif
</div>

    <!-- Icon Header -->
    <x-header.icon-header />

</div>

</main>
@endsection




