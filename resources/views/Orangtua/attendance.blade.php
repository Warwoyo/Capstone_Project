
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
                                    <th class="text-center px-4 py-2">Keterangan</th>
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




