{{--  resources/views/components/menu/student-menu.blade.php  --}}
@props(['student','class'])

<div x-data="{ mode:'view', editId:null }" class="flex-1 w-full">

    {{-- ========== VIEW LIST ========== --}}
    <div x-show="mode==='view'" class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[60vh]">
            {{-- Header --}}
            <div class="pl-2 flex items-center bg-sky-200 h-10 rounded-t-lg">
                @foreach (['Nama','Kelas','Token','Aksi'] as $h)
                    <h3 class="flex-1 text-xs text-center font-semibold text-slate-700">{{ $h }}</h3>
                @endforeach
            </div>

            {{-- Rows --}}
            @forelse ($student as $stu)
                <div class="flex items-center px-3 py-1 border border-gray-200">
                    <div class="flex-1 text-sm text-center">{{ $stu->name }}</div>
                    <div class="flex-1 text-sm text-center">{{ $class->name ?? $stu->classroom_id }}</div>
                    <div class="flex-1 text-sm text-center">
                        {{ $stu->registrationTokens->pluck('token')->implode('/') ?: '-' }}
                    </div>
                    <div class="flex-1 text-center">
                        <button class="w-20 text-xs border border-sky-300 rounded-lg"
                                @click="mode='edit'; editId={{ $stu->id }}">Edit</button>
                        <form method="POST" action="{{ route('students.destroy',[$class->id,$stu->id]) }}"
                              class="inline" onsubmit="return confirm('Hapus siswa?')">
                            @csrf @method('DELETE')
                            <button class="w-20 text-xs bg-red-500 text-white rounded-lg mt-1">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-gray-400">Belum ada data siswa.</div>
            @endforelse
        </div>

        {{-- tombol tambah --}}
        <div class="mt-4 text-center">
            <button @click="mode='add'"
                    class="bg-sky-600 text-white px-6 py-2 rounded-full">
                + Tambah Siswa
            </button>
        </div>
    </div>

    {{-- ========== ADD FORM (BARU) ========== --}}
    <div x-show="mode==='add'" class="flex-1" x-data="{ modeOrtu:'ortu' }">
        <form method="POST"
              action="{{ route('students.store',['class'=>$class->id]) }}"
              enctype="multipart/form-data"
              class="p-4 bg-white rounded-lg border space-y-6">
            @csrf

            {{-- ===== DATA SISWA ===== --}}
            <h3 class="font-semibold text-slate-700">Data Siswa</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Nomor Induk</label>
                    <input name="student_number" required
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Nama Lengkap</label>
                    <input name="name" required
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Tanggal Lahir</label>
                    <input type="date" name="birth_date" required
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Jenis Kelamin</label>
                    <select name="gender" required
                            class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-600">Foto (opsional)</label>
                    <input type="file" name="photo"
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-600">Riwayat Medis</label>
                    <textarea name="medical_history" rows="2"
                              class="w-full border rounded px-2 py-1 focus:ring-sky-500"></textarea>
                </div>
            </div>

            {{-- ===== TOGGLE TIPE KELUARGA ===== --}}
            <h3 class="font-semibold text-slate-700">Tipe Data Keluarga</h3>
            <div class="flex gap-6 text-sm text-gray-700">
                <label class="inline-flex items-center">
                    <input type="radio" value="ortu" x-model="modeOrtu" name="tipe_data"
                           class="form-radio text-sky-600" checked>
                    <span class="ml-2">Kedua Orang Tua</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" value="wali" x-model="modeOrtu" name="tipe_data"
                           class="form-radio text-sky-600">
                    <span class="ml-2">Wali Saja</span>
                </label>
            </div>

            {{-- ===== DATA ORANG TUA ===== --}}
            <div x-show="modeOrtu==='ortu'" class="grid md:grid-cols-2 gap-4">
                @foreach (['father'=>'Ayah','mother'=>'Ibu'] as $pfx=>$lbl)
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Nama {{ $lbl }}</label>
                        <input :disabled="modeOrtu!=='ortu'" :required="modeOrtu==='ortu'"
                               name="{{ $pfx }}_name"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">NIK {{ $lbl }}</label>
                        <input :disabled="modeOrtu!=='ortu'" name="{{ $pfx }}_nik"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Pekerjaan {{ $lbl }}</label>
                        <input :disabled="modeOrtu!=='ortu'" name="{{ $pfx }}_occupation"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Telp {{ $lbl }}</label>
                        <input :disabled="modeOrtu!=='ortu'" name="{{ $pfx }}_phone"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1 text-gray-600">Email {{ $lbl }}</label>
                        <input type="email" :disabled="modeOrtu!=='ortu'" name="{{ $pfx }}_email"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1 text-gray-600">Alamat {{ $lbl }}</label>
                        <textarea :disabled="modeOrtu!=='ortu'" name="{{ $pfx }}_address" rows="2"
                                  class="w-full border rounded px-2 py-1 focus:ring-sky-500"></textarea>
                    </div>
                @endforeach
            </div>

            {{-- ===== DATA WALI ===== --}}
            <div x-show="modeOrtu==='wali'" class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Nama Wali</label>
                    <input :disabled="modeOrtu!=='wali'" :required="modeOrtu==='wali'"
                           name="guardian_name"
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">NIK Wali</label>
                    <input :disabled="modeOrtu!=='wali'" name="guardian_nik"
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Pekerjaan Wali</label>
                    <input :disabled="modeOrtu!=='wali'" name="guardian_occupation"
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Telp Wali</label>
                    <input :disabled="modeOrtu!=='wali'" name="guardian_phone"
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-sm mb-1 text-gray-600">Email Wali</label>
                    <input type="email" :disabled="modeOrtu!=='wali'" name="guardian_email"
                           class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-600">Alamat Wali</label>
                    <textarea :disabled="modeOrtu!=='wali'" name="guardian_address" rows="2"
                              class="w-full border rounded px-2 py-1 focus:ring-sky-500"></textarea>
                </div>
            </div>

            {{-- ===== AKSI ===== --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="px-6 py-2 bg-sky-600 text-white rounded-full hover:bg-sky-700">
                    Simpan
                </button>
                <button type="button" @click="mode='view'"
                        class="px-6 py-2 bg-gray-200 rounded-full">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- ========== EDIT FORM (tetap) ========== --}}
    <div x-show="mode==='edit'" x-cloak class="flex-1">
        {{-- …form edit yang sudah Anda miliki… --}}
    </div>

</div>
