

@extends('layouts.dashboard')

@section('content')

<main class="flex mx-auto w-full max-w-full min-h-screen bg-white">
<div class="container mx-auto">
    <!-- Main Content -->
    <section class="pt-6 px-8 max-md:p-4 max-sm:p-2.5">

     <header class="flex flex-col gap-2">
  <nav class="text-sm text-slate-600 flex flex-wrap items-center gap-1 mt-4 md:mt-3 justify-center md:justify-start w-full">
    <span class="text-sky-600">Manajemen Kelas</span>
    <span class="text-slate-400">></span>
    <span class="text-slate-600 font-medium truncate max-w-[150px] sm:max-w-[200px] md:max-w-none">
      {{ $class->name }}
    </span>
  </nav>
</header>


        <div class="max-w-screen-xl mx-auto pt-3">
            <!-- Container Scrollable -->
            <section class="flex flex-col relative px-6 py-3 mb-2 w-full bg-sky-100 rounded-2xl h-auto max-md:flex-col max-md:items-start max-md:px-5 max-md:py-4 max-sm:flex-col max-sm:items-start max-sm:px-4 max-sm:py-4 overflow-hidden">

                <!-- Bagian Konten Card -->
                <div class="flex-1">
                    <h2 class="text-base font-bold text-sky-800 max-sm:text-sm">
                        Kelas {{$class->name}} — {{$class->owner->name ?? 'Tidak diketahui'}}
                    </h2>
                    <p class="text-sm text-gray-500 max-sm:text-sm">
                        {{$class->description}}
                    </p>
                </div>

                <!-- Tombol Edit dan Hapus di pojok kanan bawah -->
                <div class="flex justify-end gap-4">
                    <button class="flex gap-1 items-center text-xs font-medium text-sky-800">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.25 1.5H6.75C3 1.5 1.5 3 1.5 6.75V11.25C1.5 15 3 16.5 6.75 16.5H11.25C15 16.5 16.5 15 16.5 11.25V9.75" stroke="#065986" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.0299 2.26495L6.11991 8.17495C5.89491 8.39995 5.66991 8.84245 5.62491 9.16495L5.30241 11.4224C5.18241 12.2399 5.75991 12.8099 6.57741 12.6974L8.83491 12.3749C9.14991 12.3299 9.59241 12.1049 9.82491 11.8799L15.7349 5.96995C16.7549 4.94995 17.2349 3.76495 15.7349 2.26495C14.2349 0.764945 13.0499 1.24495 12.0299 2.26495Z" stroke="#065986" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.1826 3.11255C11.6851 4.90505 13.0876 6.30755 14.8876 6.81755" stroke="#065986" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Edit
                    </button>
                    <!-- ini button untuk delete , spya ada alertnya , pakai komponen ya -->
                      <x-button.delete-button label="Kelas" />
                </div>

            </section>
            
<div x-data="{ mode: 'view' }">    
    <x-menu.tabs-menu
        :class="$class"
        :tab="$tab"
        :schedule="$scheduleList"
        :student="$studentList"
        :announcementList="$announcementList" 
        x-bind:mode="mode"
        :observation="$observationList"
        :active-date="$activeDate"
        :classroom-id="$class->id" 
        :syllabusList="$syllabusList"
    />
</div>
</main>

