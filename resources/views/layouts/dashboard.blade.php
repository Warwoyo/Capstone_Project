<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Responsive Sidebar</title>
  @vite('resources/css/app.css') <!-- Link to Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">

  <!-- Toggle Button (Mobile Only) -->
  <button id="toggleSidebarBtn" class="md:hidden fixed top-4 left-4 z-30 p-2 text-white bg-sky-600 rounded-md shadow-lg">
    ☰
  </button>

  <!-- Layout wrapper -->
  <div class="flex md:flex-row flex-col min-h-screen">

    <!-- Sidebar -->
    <nav id="sidebar" class="fixed md:relative top-0 left-0 bottom-0 z-20 bg-sky-100 w-[108px] 
        px-4 py-24 transform -translate-x-full md:translate-x-0 transition-transform duration-300 
        flex-col items-center gap-10 hidden md:flex">

      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/473ef97a35661bd7c1db4be64ed1e88e41692186?placeholderIfAbsent=true"
        alt="Logo" class="w-16 h-[46px]" />

        <div class="flex flex-col gap-5 items-center">
                <!-- Menu Icon -->
                <button class="cursor-pointer" aria-label="Menu">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.75 8.75H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"></path>
                        <path d="M3.75 15H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"></path>
                        <path d="M3.75 21.25H26.25" stroke="#0086C9" stroke-width="2.5" stroke-linecap="round"></path>
                    </svg>
                </button>

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
                <button class="cursor-pointer" aria-label="Book">
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 17.2399V5.16994C22 3.96994 21.02 3.07994 19.83 3.17994H19.77C17.67 3.35994 14.48 4.42994 12.7 5.54994L12.53 5.65994C12.24 5.83994 11.76 5.83994 11.47 5.65994L11.22 5.50994C9.44 4.39994 6.26 3.33994 4.16 3.16994C2.97 3.06994 2 3.96994 2 5.15994V17.2399C2 18.1999 2.78 19.0999 3.74 19.2199L4.03 19.2599C6.2 19.5499 9.55 20.6499 11.47 21.6999L11.51 21.7199C11.78 21.8699 12.21 21.8699 12.47 21.7199C14.39 20.6599 17.75 19.5499 19.93 19.2599L20.26 19.2199C21.22 19.0999 22 18.1999 22 17.2399Z" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 5.98999V20.99" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.75 8.98999H5.5" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M8.5 11.99H5.5" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 p-2">
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

</body>
</html>
