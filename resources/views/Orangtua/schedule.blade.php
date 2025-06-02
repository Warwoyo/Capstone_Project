@extends('layouts.dashboard')

@section('content')
<!-- Dashboard orang tua - Jadwal -->
<main class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
   <div class="flex-1 p-5 max-md:p-2.5 max-sm:p-2.5 overflow-y-auto hide-scrollbar max-h-[100vh] md:max-h-[100vh]">

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

        <x-header.parent-breadcrump-header label="Jadwal Pembelajaran" />

        {{-- Tombol Kembali --}}
        <div class="mb-4">
            <a href="{{ url('/dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <div class="text-lg font-semibold text-sky-700 mb-2">Jadwal Pembelajaran Anak</div>

        {{-- Informasi untuk jadwal --}}
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Informasi:</strong> Berikut adalah jadwal pembelajaran untuk semua anak Anda. Pastikan anak hadir tepat waktu sesuai jadwal yang telah ditentukan.
                    </p>
                </div>
            </div>
        </div>

        {{-- Schedule Content --}}
        <div class="flex-1 w-full md:px-4">
            @if(isset($children) && $children->count() > 0 && isset($schedules) && $schedules->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($schedules as $schedule)
                        <div class="bg-white border border-sky-600 rounded-2xl p-6 shadow-sm">
                            <div class="flex flex-col gap-4">
                                <div class="border-b border-gray-200 pb-3">
                                    <h3 class="text-lg font-bold text-sky-800">
                                        {{ $schedule->title ?? 'Jadwal Pembelajaran' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Kelas: {{ $schedule->classroom->name ?? 'Tidak ada kelas' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Dibuat: {{ $schedule->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                
                                @if($schedule->description)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-700">{{ $schedule->description }}</p>
                                    </div>
                                @endif

                                @if($schedule->scheduleDetails && $schedule->scheduleDetails->count() > 0)
                                    <div class="space-y-3">
                                        <h4 class="font-semibold text-gray-800">Detail Jadwal:</h4>
                                        @foreach($schedule->scheduleDetails as $detail)
                                            <div class="bg-blue-50 p-3 rounded-lg border-l-4 border-blue-400">
                                                <h5 class="font-medium text-blue-800">{{ $detail->title }}</h5>
                                                <div class="text-sm text-blue-600 mt-1">
                                                    <p>Tanggal: {{ $detail->start_date->format('d/m/Y') }} - {{ $detail->end_date->format('d/m/Y') }}</p>
                                                    @if($detail->week)
                                                        <p>Minggu ke: {{ $detail->week }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500">
                                        <p class="text-sm">Belum ada detail jadwal</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Jadwal</h3>
                    <p class="text-gray-500">
                        @if(!isset($children) || $children->count() == 0)
                            Tidak ada data anak yang terdaftar.
                        @elseif(!isset($schedules) || $schedules->count() == 0)
                            Tidak ada jadwal pembelajaran yang tersedia untuk kelas anak Anda.
                        @else
                            Tidak ada jadwal pembelajaran yang tersedia.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Icon Header -->
        <x-header.icon-header />

    </div>

</main>
@endsection
