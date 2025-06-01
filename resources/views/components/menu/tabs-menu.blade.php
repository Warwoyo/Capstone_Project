{{-- resources/views/components/menu/tabs-menu.blade.php --}}
@props([
    'class' => null,
    'tab' => null,
    'schedule' => null,
    'mode' => null,
    'announcementList' => null,
    'student' => null,
    'observation' => null,
    'activeDate'         => null,
   'selectedSchedule'   => null,
   'selectedDescription'=> null,
   'classroomId'=> null,
   'syllabusList' => null,
])



<!-- Kontainer global Alpine -->
<div x-data="{ mode: @entangle('mode') }" class="space-y-2"></div>

    {{-- Navigasi tab --}}
    <x-tabs
        :tabs="['Presensi','Pengumuman','Jadwal','Observasi','Rapor','Peserta','Silabus']"
        :active="ucfirst($tab)"
        :class-id="$class->id"  
    />

    {{-- Header / Search --}}
    <div class="flex items-center justify-between gap-5 p-1">
    @if (in_array(strtolower($tab), ['jadwal', 'peserta']))
        <x-search.search-search />
    @else
        <h2 class="text-lg font-medium text-sky-800 max-sm:text-sm">
            {{ ucfirst($tab) }}
        </h2>
    @endif

    @php
        $tabActions = ['observasi', 'jadwal', 'peserta', 'rapor'];
    @endphp

    @if (in_array($tab, $tabActions))
        <x-button.add-button 
            :label="ucfirst($tab)" 
            :scheduleList="$schedule" 
            :studentList="$student"
            :class="$class"
            @click="mode = 'add'" 
        />
    @endif
</div>


    <div class="flex flex-col md:flex-row md:gap-6 gap-4 mt-0 md:mt-2">
        @php
            $tabMapping = [
                'pengumuman' => 'announcement',
                'presensi' => 'attendance',
                'jadwal' => 'schedule',
                'observasi' => 'observation',
                'rapor' => 'report',
                'peserta' => 'student',
                'silabus' => 'syllabus',
            ];

            $englishTab = $tabMapping[strtolower($tab)] ?? null;
        @endphp

        @if ($englishTab)
    @if (strtolower($tab) === 'jadwal')
        <x-dynamic-component 
            :component="'menu.' . $englishTab . '-menu'" 
            :scheduleList="$schedule" 
            :class="$class" 
            :label="ucfirst($tab)" 
            x-bind:mode="mode" 
        />
    @elseif (strtolower($tab) === 'pengumuman')
        <x-dynamic-component 
            :component="'menu.' . $englishTab . '-menu'" 
            :announcementList="$announcementList" 
            :class="$class" 
            :label="ucfirst($tab)" 
           
        />
        @elseif (strtolower($tab) === 'peserta')
        <x-dynamic-component 
            :component="'menu.' . $englishTab . '-menu'" 
            :studentList="$student" 
            :class="$class" 
            :label="ucfirst($tab)" 
            x-bind:mode="mode" 
           
        />
        @elseif (strtolower($tab) === 'observasi')
        <x-dynamic-component 
            :component="'menu.' . $englishTab . '-menu'" 
            :mode="$mode"
            :scheduleList="$schedule"
            :class="$class"
           
        />
         @elseif (strtolower($tab) === 'rapor')
        <x-dynamic-component 
            :component="'menu.' . $englishTab . '-menu'" 
            :studentList="$student" 
            :class="$class" 
            :label="ucfirst($tab)" 
           
        />
        @elseif (strtolower($tab) === 'presensi')
    <x-dynamic-component
        :component="'menu.attendance-menu'"
        :studentList="$student"
        :scheduleList="$schedule"
        :class="$class"
        :activeDate="$activeDate"
        :selectedSchedule="$selectedSchedule"        
        :selectedDescription="$selectedDescription"
        :classroomId="$classroomId"   
    />

     @elseif (strtolower($tab) === 'silabus')
        <x-dynamic-component 
            :component="'menu.' . $englishTab . '-menu'" 
            :classroom="$class" 
            :syllabusList="$syllabusList"
        />

        
    @endif
@endif
    </div>
</div>
