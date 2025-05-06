<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Aplikasi')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  
{{-- Tailwind CSS via Vite --}}
  @vite('resources/css/app.css')
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">

  {{-- Main Content --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- Optional: Footer --}}
  <footer class="py-4 text-center text-sm text-gray-500">
    &copy; {{ date('Y') }} Aplikasi by Radiance
  </footer>

  {{-- JS via Vite (kalau perlu interaktivitas) --}}
  @vite('resources/js/app.js')
</body>
</html>
