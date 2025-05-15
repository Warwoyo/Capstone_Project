
@props(['mode' => 'view', 'scheduleList' , 'class'])
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
        <h1 class="text-[24px] md:text-2xl  font-bold text-sky-600">PAUD Kartika Pradana</h1>
        <p class=" text-[8px] text-sky-800">
            Taman Penitipan Anak, Kelompok Bermain, dan Taman Kanak-Kanak
        </p>
    </div>
</header>
<x-header.parent-breadcrump-header
    label="Jadwal">
</x-header.parent-breadcrump-header>
<div class="flex-1 w-full md:px-10 pt-2"> <!-- Padding horizontal disini -->
  
  <!-- List Container -->
  <div class="overflow-y-auto hide-scrollbar max-h-[73vh] md:max-h-[80vh]">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 items-start">
      @foreach ($scheduleList as $schedule)
        <!-- Card Start -->
        <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl">
          <div class="flex flex-col gap-1 overflow-hidden">
            <h2 class="text-base font-bold text-sky-800 truncate">
              {{ $schedule['title'] }}
            </h2>
            <p class="text-sm text-gray-500 truncate">
              {{ $schedule['date'] }}
            </p>
          </div>

          <button
            type="button"
            class="flex items-center gap-1 mt-2 text-xs font-medium text-sky-800 hover:opacity-80 transition-opacity"
            onclick="toggleDetail('detail-{{ $schedule['id'] }}', this)"
          >
            <!-- Icon Mata -->
            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path class="eye-path" stroke-linecap="round" stroke-linejoin="round" d="M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z" />
            </svg>
            <span class="toggle-text">Lihat Detail</span>
          </button>

          <!-- Detail -->
          <div id="detail-{{ $schedule['id'] }}" class="hidden mt-4 p-4 border-t border-gray-300 transition-all duration-500 ease-in-out">
            <p class="text-sm text-gray-700">
              {{ $schedule['description'] ?? 'Tidak ada deskripsi.' }}
            </p>
          </div>
        </article>
        <!-- Card End -->
      @endforeach
    </div>
  </div>
</div>

    <!-- Header Icons -->
    <x-header.icon-header />
    

</main>
@endsection
