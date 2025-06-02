{{-- resources/views/components/menu/announcement-menu.blade.php --}}
@props(['announcementList', 'class'])

<div x-data="{ mode: 'view' }" class="flex-1 w-full space-y-6">

    <!-- DAFTAR PENGUMUMAN (Default View) -->
    <div x-show="mode === 'view'">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-base font-semibold text-sky-700">Daftar Pengumuman</h2>
            <button @click="mode = 'add'"
                class="px-4 py-2 rounded-full bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition">
                + Tambah
            </button>
        </div>
        <x-card.announcement-card :announcementList="$announcementList" maxHeight="max-h-[62vh] md:max-h-[54vh]" isHidden="hidden"/>
    </div>

    <!-- FORM TAMBAH PENGUMUMAN -->
    <div x-show="mode === 'add'" x-cloak>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-base font-semibold text-sky-700">Tambah Pengumuman</h2>
            <button @click="mode = 'view'"
                class="px-4 py-2 rounded-full bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                Batal
            </button>
        </div>
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
                        <div x-data="{ 
                            previewUrl: null,
                            fileName: '',
                            dragOver: false,
                            handleFileSelect(event) {
                                const file = event.target.files[0];
                                this.handleFile(file);
                            },
                            handleFile(file) {
                                if (file && file.type.startsWith('image/')) {
                                    this.fileName = file.name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.previewUrl = e.target.result;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            },
                            removeImage() {
                                this.previewUrl = null;
                                this.fileName = '';
                                $refs.fileInput.value = '';
                            }
                        }">
                            <!-- Preview Image -->
                            <div x-show="previewUrl" x-cloak class="relative mb-2">
                                <img :src="previewUrl" alt="Preview" class="w-full h-32 object-cover rounded-lg border border-sky-600">
                                <button type="button" @click="removeImage()" 
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                    ×
                                </button>
                                <p class="text-xs text-gray-600 mt-1" x-text="fileName"></p>
                            </div>

                            <!-- Upload Area -->
                            <label class="cursor-pointer" x-show="!previewUrl">
                                <div class="flex items-center px-2 h-10 text-xs font-medium text-gray-400
                                            bg-white rounded-3xl border border-sky-600 border-dashed w-full
                                            hover:border-sky-700 transition-colors"
                                    :class="{ 'border-sky-700 bg-sky-50': dragOver }"
                                    @dragover.prevent="dragOver = true"
                                    @dragleave.prevent="dragOver = false"
                                    @drop.prevent="dragOver = false; handleFile($event.dataTransfer.files[0])">
                                    <svg width="24" height="24" viewBox="0 0 25 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                        <path d="M21.5 12v7a2 2 0 0 1-2 2H5.5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <line x1="16.5" y1="5" x2="22.5" y2="5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="19.5" y1="2" x2="19.5" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span>Klik atau seret gambar disini</span>
                                </div>
                                <input type="file" name="photo" accept="image/*" class="hidden" 
                                    x-ref="fileInput" @change="handleFileSelect($event)">
                            </label>
                        </div>
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
            <div class="self-end flex gap-2">
                <button type="button" @click="mode = 'view'"
                    class="px-6 py-2.5 rounded-full bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-full bg-sky-600 text-white text-sm font-semibold
                        hover:bg-sky-700 active:bg-sky-800 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>