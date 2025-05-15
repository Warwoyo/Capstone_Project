<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/classroom.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  @vite('resources/css/app.css')
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
    <svg class="text-blue-800" width="24" height="24" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M13.875 9.25L23.125 18.5L13.875 27.75" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

  <!-- Layout wrapper -->
  <div class="flex md:flex-row flex-col min-h-screen">

    <!-- Sidebar -->
    <nav id="sidebar" class="fixed md:relative top-0 left-0 bottom-0 z-20 bg-sky-100 w-[90px] 
        px-4 py-14 transform -translate-x-full md:translate-x-0 transition-transform duration-300
        flex-col items-start gap-4 hidden md:flex relative">

      <div class="w-full flex justify-center">
        <img 
          src="https://cdn.builder.io/api/v1/image/assets/TEMP/473ef97a35661bd7c1db4be64ed1e88e41692186?placeholderIfAbsent=true"
          alt="Logo" 
          class="w-20 h-auto max-h-[60px] object-contain" 
        />
      </div>

      <div class="flex flex-col gap-5 items-center justify-center flex-1">
        <!-- Dashboard Icon -->
        <a href="{{ route('dashboard.index') }}">
          <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.75 8.75H26.25M3.75 15H26.25M3.75 21.25H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
        </a>

        <!-- Category Icon -->
        <button aria-label="Category">
          <svg width="24" height="25" fill="none" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 10.5H7C9 10.5 10 9.5 10 7.5V5.5C10 3.5 9 2.5 7 2.5H5C3 2.5 2 3.5 2 5.5V7.5C2 9.5 3 10.5 5 10.5Z"/>
            <path d="M17 10.5H19C21 10.5 22 9.5 22 7.5V5.5C22 3.5 21 2.5 19 2.5H17C15 2.5 14 3.5 14 5.5V7.5C14 9.5 15 10.5 17 10.5Z"/>
            <path d="M17 22.5H19C21 22.5 22 21.5 22 19.5V17.5C22 15.5 21 14.5 19 14.5H17C15 14.5 14 15.5 14 17.5V19.5C14 21.5 15 22.5 17 22.5Z"/>
            <path d="M5 22.5H7C9 22.5 10 21.5 10 19.5V17.5C10 15.5 9 14.5 7 14.5H5C3 14.5 2 15.5 2 17.5V19.5C2 21.5 3 22.5 5 22.5Z"/>
          </svg>
        </button>

        <!-- Classroom -->
        <a href="{{ route('classrooms.index') }}">
          <svg width="24" height="25" fill="none" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 17.24V5.17C22 3.97 21.02 3.08 19.83 3.18..."/>
            <path d="M12 5.99V20.99"/>
            <path d="M7.75 8.99H5.5"/>
            <path d="M8.5 11.99H5.5"/>
          </svg>
        </a>

        <!-- Admin -->
        <a href="{{ route('Admin.index') }}">
          <svg width="24" height="25" fill="none" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12.16 11.37C..."/>
            <path d="M7.16 15.06C..."/>
          </svg>
        </a>
      </div>

      <!-- Tombol Logout -->
      <form method="POST" action="{{ route('logout') }}" class="mt-auto w-full flex justify-center">
        @csrf
        <button type="submit" class="flex flex-col items-center gap-1 text-[#0086C9] hover:text-red-500 transition-all duration-200">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" 
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          <span class="text-xs font-medium">Logout</span>
        </button>
      </form>

    </nav>

    <!-- Main Content -->
    <main class="flex-1">
      @yield('content')
    </main>
    
<x-alert.confirmation-alert
    title="Konfirmasi Aksi"
    label="data ini"
    action="melanjutkan aksi ini"
    confirmText="Ya"
    cancelText="Tidak"
/>

  </div>

  <!-- Sidebar toggle script -->
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
    flatpickr("#tanggal", {
      dateFormat: "d - F - Y",
      locale: "id"
    });
  </script>
</body>
</html>
