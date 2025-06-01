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
  <style>
    [x-cloak] { display: none !important; }
</style>
</head>

<body class="bg-white">

  <!-- Toggle Button (Mobile Only) -->
   <div class="md:hidden fixed top-4 left-4 z-30 p-2 text-white bg-blue-200 rounded-full shadow-lg flex ml-auto">
  <button id="toggleSidebarBtn" >
    <svg class="text-blue-800" width="24" height="24" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M13.875 9.25L23.125 18.5L13.875 27.75" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>
</div>

  <!-- Layout wrapper -->
  <div class="flex md:flex-row flex-col min-h-screen">

  <nav id="sidebar" 
     class="bg-sky-100 w-[90px] px-4 py-14 transform transition-transform duration-300
            flex flex-col justify-between h-screen
            md:fixed md:top-0 md:left-0 md:bottom-0
            fixed md:relative hidden md:flex z-10"
>
<div>
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
        <a href="{{ route('classrooms.index') }}" class="cursor-pointer" aria-label="Book">
                  <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 17.2399V5.16994C22 3.96994 21.02 3.07994 19.83 3.17994H19.77C17.67 3.35994 14.48 4.42994 12.7 5.54994L12.53 5.65994C12.24 5.83994 11.76 5.83994 11.47 5.65994L11.22 5.50994C9.44 4.39994 6.26 3.33994 4.16 3.16994C2.97 3.06994 2 3.96994 2 5.15994V17.2399C2 18.1999 2.78 19.0999 3.74 19.2199L4.03 19.2599C6.2 19.5499 9.55 20.6499 11.47 21.6999L11.51 21.7199C11.78 21.8699 12.21 21.8699 12.47 21.7199C14.39 20.6599 17.75 19.5499 19.93 19.2599L20.26 19.2199C21.22 19.0999 22 18.1999 22 17.2399Z" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M12 5.98999V20.99" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M7.75 8.98999H5.5" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M8.5 11.99H5.5" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </a>

        <!-- Admin -->
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
</div>
<div class="mt-auto w-full flex justify-center">
      <!-- Tombol Logout -->
      <form method="POST" action="{{ route('logout') }}">
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
</div>
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
  if(window.innerWidth < 768){ // hanya di mobile
    sidebar.classList.toggle("hidden");
  }
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
