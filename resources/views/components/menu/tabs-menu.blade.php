{{-- resources/views/components/menu/tabs-menu.blade.php --}}
@props([
    'classroom',
    'tab',
    'studentList'   => [],   {{-- koleksi siswa --}}
    'schedule'      => [],
    'announcement'  => [],
    'observation'   => [],
    'mode'          => 'view',
])

<!-- Kontainer global Alpine -->
<div x-data="{ mode: @entangle('mode') }" class="space-y-2"></div>

    {{-- Navigasi tab --}}
    <x-tabs
        :tabs="['Presensi','Pengumuman','Jadwal','Observasi','Rapor','Peserta','Silabus']"
        :active="ucfirst($tab)"
        :class-id="$classroom['id']"
    />

    {{-- Header / Search --}}
    <div class="flex items-center justify-between gap-5 p-1">
        @if (in_array(strtolower($tab), ['jadwal','peserta']))
            <x-search.search-search />
        @else
            <h2 class="text-lg font-medium text-sky-800 max-sm:text-sm">
                {{ ucfirst($tab) }}
            </h2>
        @endif
    </div>

    {{-- Konten tiap tab --}}
    <div class="flex flex-col md:flex-row md:gap-6 gap-4 mt-0 md:mt-2">
        @switch(strtolower($tab))
            @case('jadwal')
                <x-menu.schedule-menu
                    :scheduleList="$schedule"
                    :class="$classroom"
                    :label="ucfirst($tab)"
                    x-bind:mode="mode"
                />
                @break

            @case('pengumuman')
                <x-menu.announcement-menu
                    :announcementList="$announcement"
                    :class="$classroom"
                    :label="ucfirst($tab)"
                />
                @break

            {{-- ✔︎ FIX: pass koleksi siswa langsung, TANPA foreach  --}}
            @case('peserta')
                <x-menu.student-menu
                    :student="$studentList"
                    :class="$classroom"
                    :label="ucfirst($tab)"
                />
                @break

            @case('observasi')
                <x-menu.observation-menu
                    :studentList="$studentList"
                    :class="$classroom"
                    :observationList="$observation"
                    :label="ucfirst($tab)"
                />
                @break

            @default
                {{-- tab lain (rapor, silabus, dll) --}}
        @endswitch
    </div>
</div>
