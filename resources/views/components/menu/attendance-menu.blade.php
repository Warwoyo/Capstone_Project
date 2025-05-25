{{-- resources/views/components/menu/attendance-menu.blade.php --}}
@props([
    'class','studentList','scheduleList',
    'activeDate' => null,
    'selectedSchedule' => null,
    'selectedDescription' => null,
    'classroomId'
])




<div>
    <h1 class="text-xl font-bold mb-4">
        Presensi – {{ $class->name }}
    </h1>

    <form method="POST" action="{{ route('attendance.store', $class) }}">
        @csrf
        {{-- lempar daftar & jadwal ke kartu --}}
    <x-card.attendance-card
        :students="$studentList"
        :schedules="$scheduleList"
        :activeDate="$activeDate"
        :selectedSchedule="$selectedSchedule"       
        :selectedDescription="$selectedDescription"  
        :classroomId="$classroomId" 
    />
    </form>
</div>
