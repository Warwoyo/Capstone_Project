
@props(['mode' , 'scheduleList' , 'class'])

<div x-data="{ mode: @entangle('mode') }" class="flex-1 w-full">
<!-- View Data -->
<div x-show="mode === 'view'"class="flex-1 w-full">
<div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 items-start">
    @foreach ($scheduleList as $schedule)
        <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl">
          <div class="flex flex-col gap-1 overflow-hidden">
            <h2 class="text-base font-bold text-sky-800 truncate">
              {{ $schedule['title'] }}
            </h2>
            <p class="text-sm text-gray-500 truncate">
              {{ $schedule['date'] }}
            </p>
          </div>

          <button
            type="button"
            class="flex items-center gap-1 mt-4 text-xs font-medium text-sky-800 hover:opacity-80 transition-opacity"
            onclick="toggleDetail('detail-{{ $schedule['id'] }}', this)"
            aria-label="Lihat Detail {{ $class['title'] }}"
          >
            <!-- Mata tertutup default -->
            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path class="eye-path" stroke-linecap="round" stroke-linejoin="round" d="M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z" />
            </svg>
            <span class="toggle-text">Lihat Detail</span>
          </button>

          <!-- Div detail yang akan ditampilkan dengan transisi -->
          <div id="detail-{{ $schedule['id'] }}" class="hidden mt-4 p-4 border-t border-gray-300 transition-all duration-500 ease-in-out">
            <p class="text-sm text-gray-700">
              Detail Jadwal: {{ $schedule['description'] ?? 'Tidak ada deskripsi.' }}
            </p>
            <div class="flex justify-end gap-4">
                    <button @click="mode = 'add'" class="flex gap-1 items-center text-xs font-medium text-sky-800">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.25 1.5H6.75C3 1.5 1.5 3 1.5 6.75V11.25C1.5 15 3 16.5 6.75 16.5H11.25C15 16.5 16.5 15 16.5 11.25V9.75" stroke="#065986" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.0299 2.26495L6.11991 8.17495C5.89491 8.39995 5.66991 8.84245 5.62491 9.16495L5.30241 11.4224C5.18241 12.2399 5.75991 12.8099 6.57741 12.6974L8.83491 12.3749C9.14991 12.3299 9.59241 12.1049 9.82491 11.8799L15.7349 5.96995C16.7549 4.94995 17.2349 3.76495 15.7349 2.26495C14.2349 0.764945 13.0499 1.24495 12.0299 2.26495Z" stroke="#065986" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.1826 3.11255C11.6851 4.90505 13.0876 6.30755 14.8876 6.81755" stroke="#065986" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Edit
                    </button>
                    <!-- ini untuk tombol delete, pakai komponent -->
                    <x-button.delete-button label="Jadwal" />
                </div>
          </div>

        </article>
      @endforeach
  </div>
</div>
</div>

<!-- Add Data -->

<div x-show="mode === 'add'" class="flex-1 w-full"> 
    <!-- Parent Flex Container -->
    <div class="flex flex-wrap gap-1 md:gap-6 w-full"> <!-- <-- ini flex container -->
        
        <!-- Kolom Kiri -->
        <div class="flex-1 min-w-[300px]">
            <form class="flex flex-col gap-3.5">
                <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                    <!-- Form Input Judul -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs text-slate-600 max-sm:text-sm">
                            <span>Judul</span><span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            placeholder="Judul Jadwal"
                            class="px-4 py-0 h-10 text-sm font-medium text-gray-400 bg-gray-50 rounded-3xl border border-sky-600 w-full"
                        />
                    </div>

                    <!-- Form Tanggal Gambar -->
                    <div class="flex flex-col gap-1.5">
    <label class="text-xs text-slate-600 max-sm:text-sm">
        Tanggal <span class="text-red-500">*</span>
    </label>
    <input
    type="text"
    id="tanggal"
    placeholder="01 - Januari - 2025"
    class="px-4 py-0 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full max-sm:text-sm"
/>


</div>

                </div>
            </form>
        </div>

        <!-- Kolom Kanan -->
        <div class="flex-1 min-w-[300px]">
            <!-- Deskripsi -->
            <div class="flex flex-col gap-1.5">
                <label class="text-xs text-slate-600 max-sm:text-sm">
                    <span>Deskripsi</span><span class="text-red-500">*</span>
                </label>
                <RichTextEditor />
            </div>

            <!-- Toolbar dan Textarea -->
            <div class="flex flex-col px-4 py-0 bg-gray-50 rounded-3xl border-sky-600 border-solid border-[1.5px]">
                <div class="flex gap-3 items-center px-0 py-2.5 border-b border-gray-200">
                    <!-- Tombol Toolbar -->
                    <button class="text-lg text-black font-bold">B</button>
                    <button class="text-lg text-black underline">U</button>
                    <!-- Icon lainnya -->
                </div>

                <textarea class="p-2.5 text-xs font-medium text-gray-500 h-[80px] bg-transparent resize-none focus:outline-none" placeholder="Masukkan deskripsi...."></textarea>
            
    </div> <!-- Tutup flex container -->
</div>
</div>
<div>
            <x-button.submit-button />
        </div>