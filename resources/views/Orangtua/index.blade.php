

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
 
    {{-- Stat Cards --}}


<div class="flex flex-col pb-1 md:pt-6 md:pl-8 md:pr-8">
  <div class="text-lg font-semibold text-sky-700 mb-2">Anak</div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
 <x-card.parent-stat-card 
    label="Data Anak" 
   >
    </x-card.parent-stat-card>

  <x-card.parent-stat-card 
             label="Jadwal">
        </x-card.parent-stat-card>

  <x-card.parent-stat-card
            label="Presensi">
          </x-card.parent-stat-card>

  <x-card.parent-stat-card 
             label="Observasi">
        </x-card.parent-stat-card>

  <x-card.parent-stat-card
            label="Silabus">
          </x-card.parent-stat-card>

  <x-card.parent-stat-card 
             label="Riwayat Pengumuman">
        </x-card.parent-stat-card>
</div>
{{-- Announcement Card --}}
    <x-card.announcement-card 
    :announcementList="$announcementList"
    />
</div>
    <!-- Header Icons -->
    <x-header.icon-header />
    

</main>
@endsection
