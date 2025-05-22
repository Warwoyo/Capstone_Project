
{{-- resources/views/components/menu/announcement-menu.blade.php --}}
@props(['announcementList', 'class'])

<div class="flex-1 w-full space-y-6">
    <!-- ═══ FORM PENGUMUMAN (langsung submit) ═══ -->
    <form method="POST"
          action="{{ route('classrooms.announcements.store', $class) }}"
          enctype="multipart/form-data"
          class="flex flex-col gap-3.5">
        @csrf
        <input type="hidden" name="classroom_id" value="{{ $class->id }}">

        <div class="flex flex-col md:flex-row gap-1 md:gap-6">
            <!-- KIRI: Judul + Gambar -->
            <div class="w-full md:w-1/2 flex flex-col gap-3.5">

                <!-- Judul -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-slate-600">Judul<span class="text-red-500">*</span></label>
                    <input name="title" required
                           type="text"
                           placeholder="Judul Pengumuman"
                           class="px-4 h-10 text-sm font-medium text-gray-700 bg-gray-50
                                  rounded-3xl border border-sky-600 w-full">
                </div>

                <!-- Upload Gambar (opsional) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-slate-600">Gambar (opsional)</label>
                    <label class="cursor-pointer">
                        <div class="flex items-center px-2 h-10 text-xs font-medium text-gray-400
                                    bg-white rounded-3xl border border-sky-600 border-dashed w-full">
                            <svg width="24" height="24" viewBox="0 0 25 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <path d="M8.5 12h8M9.5 8H6.5a4 4 0 0 0 0 8h3M15.5 8h3a4 4 0 0 1 0 8h-3"
                                      stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Klik / seret gambar</span>
                        </div>
                        <input type="file" name="photo" class="hidden">
                    </label>
                </div>
            </div>

            <!-- KANAN: Deskripsi -->
            <div class="w-full md:w-1/2 flex flex-col gap-1.5">
                <label class="text-xs text-slate-600">Deskripsi<span class="text-red-500">*</span></label>
                <textarea name="description" required rows="6"
                          placeholder="Masukkan deskripsi..."
                          class="p-2.5 text-xs font-medium text-gray-700 bg-gray-50
                                 rounded-3xl border border-sky-600 resize-none focus:outline-none"></textarea>
            </div>
        </div>

        <!-- Tombol Submit custom -->
        <div class="self-end">
            <button type="submit"
                    class="px-6 py-2.5 rounded-full bg-sky-600 text-white text-sm font-semibold
                           hover:bg-sky-700 active:bg-sky-800 transition">
                Simpan
            </button>
        </div>
    </form>

    <!-- DAFTAR PENGUMUMAN -->
    <x-card.announcement-card :announcementList="$announcementList"/>
</div>