
@props(['announcementList' , 'class'])
<div class="flex-1 w-full"> 
    <!-- Parent Flex Container -->
    <div class="flex flex-col md:flex-row gap-1 md:gap-6 w-full">
    <!-- Kolom Kiri -->
    <div class="w-full md:w-1/2">
        <form class="flex flex-col gap-3.5">
            <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                <!-- Form Input Judul -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-slate-600 max-sm:text-sm">
                        <span>Judul</span><span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        placeholder="Judul Pengumuman"
                        class="px-4 py-0 h-10 text-sm font-medium text-gray-400 bg-gray-50 rounded-3xl border border-sky-600 w-full"
                    />
                </div>

                <!-- Form Upload Gambar -->
                <div class="flex flex-col gap-1.5 max-md:w-full max-sm:w-full">
  <label class="text-xs text-slate-600 max-sm:text-sm">Gambar</label>

  <label class="cursor-pointer">
    <div class="flex items-center px-2 py-0 text-xs font-medium text-gray-400 bg-white rounded-3xl border border-sky-600 border-dashed h-[40px] w-full">
      <svg width="24" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2.5">
        <path d="M8.4491 12H16.4491M9.4491 8H6.4491C5.38823 8 4.37082 8.42143 3.62067 9.17157C2.87052 9.92172 2.4491 10.9391 2.4491 12C2.4491 13.0609 2.87052 14.0783 3.62067 14.8284C4.37082 15.5786 5.38823 16 6.4491 16H9.4491M15.4491 8H18.4491C19.51 8 20.5274 8.42143 21.2775 9.17157C22.0277 9.92172 22.4491 10.9391 22.4491 12C22.4491 13.0609 22.0277 14.0783 21.2775 14.8284C20.5274 15.5786 19.51 16 18.4491 16H15.4491" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span>Klik atau seret gambar ke area ini untuk upload</span>
    </div>
    <input type="file" name="photo" class="hidden" />
  </label>
</div>

            </div>
        </form>
    </div>

    <!-- Kolom Kanan -->
    <div class="w-full md:w-1/2 flex flex-col">
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
        </div>

        <!-- Tombol Submit -->
        
    </div>
    
</div>
<div>
            <x-button.submit-button />
        </div>

</div>

