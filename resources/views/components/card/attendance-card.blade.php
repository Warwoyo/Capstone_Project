<div 
    x-data="{
        selectedDate: '',
        tema: '',
        keterangan: ''
    }" 
    x-init="
        flatpickr('#tanggal', {
            dateFormat: 'Y-m-d', // Format penyimpanan
            altFormat: 'd M Y', // Format yang ditampilkan di input (dengan nama bulan)
            locale: 'id', // Set locale ke Indonesia untuk format bulan dalam bahasa Indonesia
            onChange: function(selectedDates, dateStr, instance) {
                selectedDate = dateStr; // Sinkronisasi dengan Alpine.js
            }
        });
    " 
    class="w-full"
>

    <!-- Input Tanggal -->
    <div class="mb-4">
        <label for="tanggal"
         class="block text-sm font-small font-bold text-sky-800">Tanggal Observasi</label>
        <input 
            id="tanggal" 
            x-model="selectedDate" 
            class="mt-2 px-4 py-0 h-10 text-sm font-medium bg-gray-50 rounded-3xl border border-sky-600"
            placeholder="Klik Untuk Pilih Tanggal"
        />
    </div>

    <!-- Jika tanggal sudah dipilih, tampilkan tema dan keterangan -->
    <div x-show="selectedDate" class="grid grid-cols-1 sm:grid-cols-2 gap-4 ">
        <div class="flex flex-col gap-1.5">
            <label class="text-xs text-slate-600 max-sm:text-sm">
                <span>Tema</span><span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                x-model="tema"
                placeholder="Masukkan Tema"
                class="px-4 py-0 h-10 text-sm font-medium text-gray-400 bg-gray-50 rounded-3xl border border-sky-600 w-full"
            />
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs text-slate-600 max-sm:text-sm">
                <span>Keterangan</span><span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                x-model="keterangan"
                placeholder="Masukkan Keterangan"
                class="px-4 py-0 h-10 text-sm font-medium text-gray-400 bg-gray-50 rounded-3xl border border-sky-600 w-full"
            />
        </div>
    </div>

    <!-- Tabel siswa muncul jika tanggal sudah dipilih -->
    <div x-show="selectedDate" class="w-full overflow-x-auto transition-all">
        <section class="min-w-[846px] mx-auto my-4 rounded-lg border border-gray-200 bg-white">
            <!-- Header -->
            <header class="flex font-medium text-sky-800 bg-sky-200 text-sm">
                <h3 class="flex-1 p-2.5 text-center">Nama Lengkap</h3>
                <h3 class="flex-1 p-2.5 text-center" x-text="selectedDate ? new Date(selectedDate).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : 'Tanggal'"></h3>
                <h3 class="flex-1 p-2.5 text-center">Total Hadir</h3>
                <h3 class="flex-1 p-2.5 text-center">Persentase</h3>
            </header>

            @foreach ($students as $index => $student)
                <article class="flex border-b border-gray-200 text-sm text-slate-600">
                    <p class="flex-1 px-2.5 py-1.5 text-center">{{ $student['name'] }}</p>

                    <!-- Dropdown Presensi -->
                    <div class="flex-1 flex justify-center items-center">
                        <select name="attendance[{{ $index }}][status]" class="text-xs border rounded-md p-1 bg-white text-gray-700">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>

                    <p class="flex-1 px-2.5 py-1.5 text-center">{{ $student['totalPresent'] ?? '-' }}</p>
                    <p class="flex-1 px-2.5 py-1.5 text-center">{{ $student['percentage'] ?? '-' }}</p>
                </article>
            @endforeach
        </section>
    </div>
</div>
