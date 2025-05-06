@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/classroom.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  @vite('resources/css/app.css') <!-- Link to Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
 
</head>

<body class="bg-white">

  <!-- Toggle Button (Mobile Only) -->
  <button id="toggleSidebarBtn" class="md:hidden fixed top-4 left-4 z-30 p-2 text-white bg-blue-200 rounded-full shadow-lg flex justify-center items-center">
    <!-- Chevron Icon (SVG) -->
    <svg class="text-blue-800" width="24" height="24" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13.875 9.25L23.125 18.5L13.875 27.75" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

    
    @php
        $role = auth()->user()->role;
        $isStaff = in_array($role, ['admin', 'teacher']);
    @endphp

    {{-- ====================== ADMIN & GURU ====================== --}}
    @if ($isStaff)
        <!-- Layout wrapper -->
        <div class="flex md:flex-row flex-col min-h-screen">

        <!-- Sidebar -->
        <nav id="sidebar" class="fixed md:relative top-0 left-0 bottom-0 z-20 bg-sky-100 w-[90px] 
            px-4 py-14 transform -translate-x-full md:translate-x-0 transition-transform duration-300 
            flex-col items-start gap-4 hidden md:flex">

            <div class="w-full flex justify-center">

        </div>

            <div class="flex flex-col gap-5 items-center justify-center">

            <img 
            src="https://cdn.builder.io/api/v1/image/assets/TEMP/473ef97a35661bd7c1db4be64ed1e88e41692186?placeholderIfAbsent=true"
            alt="Logo" 
            class="w-20 h-auto max-h-[60px] object-contain" 
        />
                    <!-- Menu Icon -->
                    <a href="{{ route('dashboard.index') }}" class="cursor-pointer" aria-label="Menu">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.75 8.75H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"></path>
                            <path d="M3.75 15H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"></path>
                            <path d="M3.75 21.25H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"></path>
                        </svg>
        </a>

                    <!-- Category Icon -->
                    <button class="cursor-pointer" aria-label="Category">
                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 10.5H7C9 10.5 10 9.5 10 7.5V5.5C10 3.5 9 2.5 7 2.5H5C3 2.5 2 3.5 2 5.5V7.5C2 9.5 3 10.5 5 10.5Z" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M17 10.5H19C21 10.5 22 9.5 22 7.5V5.5C22 3.5 21 2.5 19 2.5H17C15 2.5 14 3.5 14 5.5V7.5C14 9.5 15 10.5 17 10.5Z" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M17 22.5H19C21 22.5 22 21.5 22 19.5V17.5C22 15.5 21 14.5 19 14.5H17C15 14.5 14 15.5 14 17.5V19.5C14 21.5 15 22.5 17 22.5Z" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M5 22.5H7C9 22.5 10 21.5 10 19.5V17.5C10 15.5 9 14.5 7 14.5H5C3 14.5 2 15.5 2 17.5V19.5C2 21.5 3 22.5 5 22.5Z" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    
                    <!-- Book Icon -->
                    <a href="{{ route('Classroom.index') }}" class="cursor-pointer" aria-label="Book">
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 17.2399V5.16994C22 3.96994 21.02 3.07994 19.83 3.17994H19.77C17.67 3.35994 14.48 4.42994 12.7 5.54994L12.53 5.65994C12.24 5.83994 11.76 5.83994 11.47 5.65994L11.22 5.50994C9.44 4.39994 6.26 3.33994 4.16 3.16994C2.97 3.06994 2 3.96994 2 5.15994V17.2399C2 18.1999 2.78 19.0999 3.74 19.2199L4.03 19.2599C6.2 19.5499 9.55 20.6499 11.47 21.6999L11.51 21.7199C11.78 21.8699 12.21 21.8699 12.47 21.7199C14.39 20.6599 17.75 19.5499 19.93 19.2599L20.26 19.2199C21.22 19.0999 22 18.1999 22 17.2399Z" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 5.98999V20.99" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.75 8.98999H5.5" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M8.5 11.99H5.5" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    </a>

                    <!-- admin Icon -->
                    <a href="{{ route('Admin.index') }}" class="cursor-pointer" aria-label="Category">
                        <svg
        width="24"
        height="25"
        viewBox="0 0 24 25"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="profile-icon"
        >
        <path
        d="M12.16 11.37C12.06 11.36 11.94 11.36 11.83 11.37C9.45 11.29 7.56 9.34 7.56 6.94C7.56 4.49 9.54 2.5 12 2.5C14.45 2.5 16.44 4.49 16.44 6.94C16.43 9.34 14.54 11.29 12.16 11.37Z"
        stroke="#0086C9"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
        <path
        d="M7.16 15.06C4.74 16.68 4.74 19.32 7.16 20.93C9.91 22.77 14.42 22.77 17.17 20.93C19.59 19.31 19.59 16.67 17.17 15.06C14.43 13.23 9.92 13.23 7.16 15.06Z"
        stroke="#0086C9"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        />
        </svg>
        </a>


                </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1">
                <!-- Content will be injected here -->
                @yield('content')
        </main>
        </div>

        <!-- JavaScript Toggle -->
        <script>
        document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggleSidebarBtn");
        const sidebar = document.getElementById("sidebar");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("hidden");
            sidebar.classList.toggle("flex");
            sidebar.classList.toggle("-translate-x-full");
            sidebar.classList.toggle("translate-x-0");
        });
        });
        </script>
        <script>
        function toggleDetail(id, button) {
        const detailCard = document.getElementById(id);

        // Check if the detail card is visible
        if (detailCard) {
        // Toggle the visibility of the card
        detailCard.classList.toggle('hidden');
        
        // Toggle eye icon and text
        const eyeIcon = button.querySelector('.eye-icon');
        const toggleText = button.querySelector('.toggle-text');
        const eyePath = eyeIcon.querySelector('.eye-path');
        
        if (detailCard.classList.contains('hidden')) {
            // If detail is hidden, change to "Lihat Detail" and close eye
            toggleText.textContent = 'Lihat Detail';
            
            // Mata tertutup
            eyePath.setAttribute('d', 'M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z'); // Mata terbuka
        }
        else {
            // If detail is visible, change to "Tutup Detail" and open eye
            toggleText.textContent = 'Tutup Detail';
            
            // Mata terbuka
            eyePath.setAttribute('d', 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88'); // Mata tertutup
        } 
        }
        }
        </script>
        <script>
        document.addEventListener('alpine:init', () => {
        Alpine.store('app', {
            mode: 'view' // default pertama saat halaman dimuat
        });
        });
        </script>

        <script>
        flatpickr("#tanggal", {
        dateFormat: "d - F - Y", // langsung format user-friendly
        locale: "id"
        });


        </script>


    {{-- ====================== ORANG TUA ====================== --}}
    @elseif($role === 'parent')
        <hr class="my-8">
        <h2 class="text-xl font-semibold mb-4 text-center">Data Anak Anda</h2>

        @php($children = auth()->user()->students)

        @if($children->isEmpty())
            <p class="text-center text-gray-500">Belum ada data anak yang terhubung. Silakan hubungi wali kelas.</p>
        @else
            <div class="space-y-4">
                @foreach($children as $child)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-semibold text-lg">{{ $child->name }}</p>
                        <p class="text-sm">Tgl Lahir&nbsp;: {{ \Carbon\Carbon::parse($child->birth_date)->format('d-m-Y') }}</p>
                        <p class="text-sm">Jenis Kelamin&nbsp;: {{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
        {{-- Tombol logout --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-8 text-center">
        @csrf
        <button class="px-6 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none">
            Logout
        </button>
    </form>
    @endif
    {{-- ====================== END ROLE SECTION ====================== --}}


</body>
@endsection
</html>