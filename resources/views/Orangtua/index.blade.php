
@extends('layouts.dashboard')

@section('content')
<main class="flex mx-auto w-full max-w-full h-screen bg-white flex-1 p-5 max-md:p-2.5 max-sm:p-2.5 overflow-y-auto hide-scrollbar max-h-[100vh] md:max-h-[100vh]">
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
 
        {{-- Stat Cards --}}
        <div class="flex flex-col pb-1 md:pt-6 md:pl-8 md:pr-8">
            <div class="text-lg font-semibold text-sky-700 mb-2">Menu Anak</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <x-card.parent-stat-card label="Data Anak" route="{{ route('orangtua.children') }}" />
                <x-card.parent-stat-card label="Jadwal" route="{{ route('orangtua.schedule') }}" />
                <x-card.parent-stat-card label="Presensi" route="{{ route('orangtua.attendance') }}" />
                <x-card.parent-stat-card label="Observasi" route="{{ route('orangtua.observation') }}" />
                <x-card.parent-stat-card label="Silabus" route="{{ route('orangtua.syllabus') }}" />
                <x-card.parent-stat-card label="Rapot" route="{{ route('orangtua.rapor') }}" />
            </div>
        </div>

        {{-- Announcement Card --}}
        <div class="mt-2">
            @if(!empty($announcementList) && count($announcementList) > 0)
                <x-card.announcement-card 
                    :announcementList="$announcementList" 
                
                />
            @else
                <div class="bg-gray-100 rounded-lg p-4 text-center">
                    <p class="text-gray-500">Belum ada pengumuman untuk anak Anda</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Header Icons -->
    <x-header.icon-header />
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to meta if not present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }
    
    // Handle all orangtua navigation with session preservation
    const orangtuaLinks = document.querySelectorAll('a[href*="orangtua"]');
    
    orangtuaLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading state
            this.style.opacity = '0.7';
            this.style.pointerEvents = 'none';
            
            // Get the target route
            const targetRoute = this.getAttribute('href');
            
            // Perform navigation with session check
            fetch('/orangtua/session-check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.authenticated && (data.user_role === 'orangtua' || data.user_role === 'parent')) {
                    // Session is valid, proceed with navigation
                    window.location.href = targetRoute;
                } else {
                    // Session invalid, redirect to login
                    window.location.href = '{{ route("login") }}';
                }
            })
            .catch(error => {
                console.error('Navigation error:', error);
                // Fallback to direct navigation
                window.location.href = targetRoute;
            })
            .finally(() => {
                this.style.opacity = '1';
                this.style.pointerEvents = 'auto';
            });
        });
    });
});
</script>
@endpush
@endsection