
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
    label="Pengumuman">
</x-header.parent-breadcrump-header>
<div class="flex-1 w-full md:px-10 pt-2"> <!-- Padding horizontal disini -->
  
  <!-- List Container -->
  <div class="overflow-y-auto hide-scrollbar">
   {{-- Announcement Card --}}
<x-card.announcement-card :announcementList="$announcementList" maxHeight="max-h-[80vh] md:max-h-[70vh]" />

   
</div>

    <!-- Header Icons -->
    <x-header.icon-header />
    

</main>
@endsection
