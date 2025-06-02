@props([
    'label' => null,  // Default jika tidak ada label yang diteruskan
    'class' => null,   // Default jika tidak ada kelas yang diteruskan
    'tab' => null,     // Default jika tidak ada tab yang diteruskan
    'mode' => null     // Menambahkan mode untuk entangling
])


    <!-- Tombol selalu muncul, hanya mode yang akan mempengaruhi tampilan di tempat lain -->
    <button x-data="{ mode: @entangle('mode') }"
        class="w-1/3 sm:w-auto flex justify-center items-center gap-2 bg-sky-600 rounded-3xl border border-sky-600 h-auto sm:h-[36px] px-6 whitespace-nowrap"
        @click="mode = 'add'"> <!-- Ketika tombol diklik, ubah mode ke 'add' -->
        <svg class="hidden sm:block" width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
            <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
        </svg>

        <span class="leading-tight text-sm sm:text-base">
            <span class="text-white block sm:inline">Tambah</span>
            <span class="text-white block sm:inline">{{ $label }}</span>
        </span>
    </button>

