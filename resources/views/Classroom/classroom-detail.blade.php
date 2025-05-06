

@extends('layouts.dashboard')

@section('content')

<main class="flex mx-auto w-full max-w-full min-h-screen bg-white">
<div class="container mx-auto">
    <!-- Main Content -->
    <section class="pt-6 px-8 max-md:p-4 max-sm:p-2.5">

        <x-header.search-header />

        <div class="max-w-screen-xl mx-auto pt-3">
            <!-- Container Scrollable -->
            <section class="flex flex-col relative px-6 py-3 mb-2 w-full bg-sky-100 rounded-2xl h-auto max-md:flex-col max-md:items-start max-md:px-5 max-md:py-4 max-sm:flex-col max-sm:items-start max-sm:px-4 max-sm:py-4 overflow-hidden">

                <!-- Bagian Konten Card -->
                <div class="flex-1">
                    <h2 class="text-base font-bold text-sky-800 max-sm:text-sm">
                        Kelas Pelangi Ceria
                    </h2>
                    <p class="text-sm text-gray-500 max-sm:text-sm">
                        Meningkatkan kreativitas melalui seni, musik, dan permainan seru yang mengenalkan warna dan ekspresi diri
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
                    <button class="flex gap-1 items-center text-xs font-medium text-red-500">
                        <svg width="18" height="18" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.0417 2.875H3.95833C3.08388 2.875 2.375 3.58388 2.375 4.45833V15.5417C2.375 16.4161 3.08388 17.125 3.95833 17.125H15.0417C15.9161 17.125 16.625 16.4161 16.625 15.5417V4.45833C16.625 3.58388 15.9161 2.875 15.0417 2.875Z" stroke="#F04438" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.125 7.625L11.875 12.375M11.875 7.625L7.125 12.375" stroke="#F04438" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Hapus
                    </button>
                </div>

            </section>
            
<div x-data="{ mode: 'view' }">    
    <x-menu.tabs-menu 
        :class="$class" 
        :tab="$tab"
        :schedule="$scheduleList" 
        :student="$studentList"
        :announcement="$announcementList"
        x-bind:mode="mode"
        :observation="$observationList"

    />

</div>


    </section>

    <!-- Header Icons -->
    <x-header.icon-header />
</div>
</main>
@endsection
