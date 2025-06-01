@extends('layouts.dashboard')

@section('content')
<!-- ini dashboard orang tua -->
<main class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
<div class="flex-1 p-5 max-md:p-2.5 max-sm:p-2.5">

    {{-- Header --}}
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
<x-header.parent-breadcrump-header
    label="Silabus">
</x-header.parent-breadcrump-header>

<div class="text-lg font-semibold text-sky-700 mb-2">Silabus Pembelajaran</div>

        @if($syllabusList && count($syllabusList) > 0)
            <!-- Scroll horizontal dan vertikal -->
            <div class="w-full overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-y-auto max-h-[500px] md:max-h-[400px] rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full table-auto text-sm text-slate-600">
                            <!-- Header -->
                            <thead class="bg-sky-200 text-sky-800 font-medium">
                                <tr>
                                    <th class="text-center px-4 py-2">Tanggal</th>
                                    <th class="text-center px-4 py-2">Judul Silabus</th>
                                    <th class="text-center px-4 py-2">Nama File</th>
                                    <th class="text-center px-4 py-2">Aksi</th>
                                </tr>
                            </thead>

                            <!-- Body -->
                            <tbody>
                                @foreach ($syllabusList as $syllabus)
                                    <tr class="border-t border-gray-200">
                                        <td class="text-center px-4 py-2 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($syllabus['created_at'])->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-normal break-words">
                                            {{ $syllabus['title'] }}
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-normal break-words">
                                            {{ $syllabus['file_name'] }}
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-nowrap">
                                            <a href="{{ route('syllabus.view', $syllabus['id']) }}" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-1 bg-sky-600 text-white text-xs font-medium rounded-md hover:bg-sky-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat PDF
                                            </a>
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
                <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Silabus</h3>
                <p class="text-gray-500">Tidak ada silabus yang tersedia untuk kelas anak Anda.</p>
            </div>
        @endif
    </div>

    <!-- Header Icons -->
    <x-header.icon-header />

</div>

</main>
@endsection
