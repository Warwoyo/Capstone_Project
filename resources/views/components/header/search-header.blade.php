<header class="flex flex-col gap-2">
        <nav class="text-sm text-slate-600 flex flex-wrap sm:flex-row mt-12 md:mt-3 sm:mt-0">
    <span class="text-sky-600">Manajemen Kelas &gt;</span>
    <span class="text-slate-600">Daftar Kelas</span>
  </nav>
            <h1 class="text-lg font-bold text-sky-800">Daftar Kelas</h1>
            <div class="flex gap-5 items-center flex-nowrap w-full">
  <!-- Search Bar -->
  <div class="flex items-center px-4 py-0 bg-white rounded-3xl border border-sky-600 h-[36px] flex-grow">
    <div>
      <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.66668 14.4999C11.1645 14.4999 14 11.6644 14 8.16659C14 4.66878 11.1645 1.83325 7.66668 1.83325C4.16887 1.83325 1.33334 4.66878 1.33334 8.16659C1.33334 11.6644 4.16887 14.4999 7.66668 14.4999Z" stroke="#0086C9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.6667 15.1666L13.3333 13.8333" stroke="#0086C9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
    </div>
    <input type="text" placeholder="Cari kelas...." class="text-base text-sky-600 bg-transparent border-none outline-none ml-2 w-full">
  </div>

  <!-- Add Class Button -->
  <button @click="mode = 'add'" class="w-1/4 sm:w-auto flex justify-center items-center gap-2 bg-sky-600 rounded-3xl border border-sky-600 h-auto sm:h-[36px] px-6 whitespace-nowrap">
    <!-- Icon Plus -->
    <svg class="hidden sm:block" width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
      <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
    </svg>

    <!-- Teks: dipecah jadi 2 baris saat mobile -->
    <span class="leading-tight text-sm sm:text-base">
        <span class="text-white block sm:inline">Tambah</span>
        <span class="text-white block sm:inline">Kelas</span>
    </span>
</button>

        </header>