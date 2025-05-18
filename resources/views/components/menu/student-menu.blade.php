{{--  resources/views/components/menu/student-menu.blade.php  --}}
@props(['studentList','class'])

<div
  x-data="{
      mode     : 'view',    // view | add | edit
      editData : {},        // objek siswa yg sedang diedit
      modeOrtu : 'ortu'     // toggle ortu / wali di dalam edit
  }"
  class="flex-1 w-full"
>

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
            @forelse ($studentList as $stu)
                @php
                    // 1. mulai dari array siswa biasa
                    $flat = $stu->toArray();

                    // 2. ambil role parent
                    $father   = $stu->parents->firstWhere('relation','father');
                    $mother   = $stu->parents->firstWhere('relation','mother');
                    $guardian = $stu->parents->firstWhere('relation','guardian');

                    // 3. isi field yang dipakai form
                    if($father){
                        $flat['father_name']       = $father->name;
                        $flat['father_nik']        = $father->nik;
                        $flat['father_occupation'] = $father->occupation;
                        $flat['father_phone']      = $father->phone;
                        $flat['father_email']      = $father->email;
                        $flat['father_address']    = $father->address;
                    }
                    if($mother){
                        $flat['mother_name']       = $mother->name;
                        $flat['mother_nik']        = $mother->nik;
                        $flat['mother_occupation'] = $mother->occupation;
                        $flat['mother_phone']      = $mother->phone;
                        $flat['mother_email']      = $mother->email;
                        $flat['mother_address']    = $mother->address;
                    }
                    if($guardian){
                        $flat['guardian_name']       = $guardian->name;
                        $flat['guardian_relation']   = $guardian->relation ?? '';
                        $flat['guardian_phone']      = $guardian->phone;
                        $flat['guardian_email']      = $guardian->email;
                        $flat['guardian_address']    = $guardian->address;
                    }

                    // 4. tentukan tipe data utk radio
                    $flat['tipe_data'] = $guardian ? 'wali' : 'ortu';

                    // 5. URL foto lama (jika ada)
                    $flat['photo_url'] = $stu->photo ? asset('storage/'.$stu->photo) : null;
                @endphp

                <div class="flex items-center px-3 py-1 border border-gray-200">
                    <div class="flex-1 text-sm text-center">{{ $stu->name }}</div>
                    <div class="flex-1 text-sm text-center">{{ $class->name ?? $stu->classroom_id }}</div>
                    <div class="flex-1 text-sm text-center">
                        {{ $stu->registrationTokens->pluck('token')->implode('/') ?: '-' }}
                    </div>
                    <div class="flex-1 text-center">
                        <button
                            class="w-20 text-xs border border-sky-300 rounded-lg"
                            @click="
                                editData = {{ json_encode($flat,JSON_UNESCAPED_UNICODE) }};
                                mode     = 'edit';
                                modeOrtu = editData.tipe_data;
                            "
                        >Edit</button>
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

{{-- ========== FORM EDIT ========== --}}
<div x-show="mode==='edit'" x-cloak class="flex-1">

    <form
        method="POST"
        :action="`{{ url('/classroom/'.$class->id.'/students') }}/${editData.id}`"
        enctype="multipart/form-data"
        class="p-4 bg-white rounded-lg border space-y-6"
    >
        @csrf @method('PUT')

        {{-- ===== DATA SISWA ===== --}}
        <h3 class="font-semibold text-slate-700">Data Siswa</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1 text-gray-600">Nomor Induk</label>
                <input name="student_number" x-model="editData.student_number" required
                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm mb-1 text-gray-600">Nama Lengkap</label>
                <input name="name" x-model="editData.name" required
                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm mb-1 text-gray-600">Tanggal Lahir</label>
                <input type="date" name="birth_date" x-model="editData.birth_date" required
                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm mb-1 text-gray-600">Jenis Kelamin</label>
                <select name="gender" x-model="editData.gender"
                        class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    <option value="male">Laki-laki</option>
                    <option value="female">Perempuan</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600">Riwayat Medis</label>
                <textarea name="medical_history" x-model="editData.medical_history" rows="2"
                          class="w-full border rounded px-2 py-1 focus:ring-sky-500"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600">Foto (opsional)</label>
                <input type="file" name="photo"
                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
            </div>
        </div>

        {{-- ===== PILIH TIPE KELUARGA ===== --}}
        <h3 class="font-semibold text-slate-700 mt-6">Data Keluarga</h3>
        <label class="inline-flex items-center mr-6">
            <input type="radio" value="ortu" x-model="editData.tipe_data" name="tipe_data"
                   class="text-sky-600 focus:ring-sky-500">
            <span class="ml-2">Ayah &amp; Ibu</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" value="wali" x-model="editData.tipe_data" name="tipe_data"
                   class="text-sky-600 focus:ring-sky-500">
            <span class="ml-2">Wali</span>
        </label>

        {{-- ===== DATA ORANG TUA ===== --}}
        <template x-if="editData.tipe_data==='ortu'">
            <div class="mt-4 border-t pt-4 space-y-8">
                @foreach(['father'=>'Ayah','mother'=>'Ibu'] as $pfx=>$lbl)
                    <div>
                        <h4 class="font-semibold text-slate-700 mb-2">{{ $lbl }}</h4>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1 text-gray-600">Nama</label>
                                <input name="{{ $pfx }}_name" :disabled="editData.tipe_data!=='ortu'"
                                       x-model="editData.{{ $pfx }}_name"
                                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block text-sm mb-1 text-gray-600">NIK</label>
                                <input name="{{ $pfx }}_nik" :disabled="editData.tipe_data!=='ortu'"
                                       x-model="editData.{{ $pfx }}_nik"
                                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block text-sm mb-1 text-gray-600">Pekerjaan</label>
                                <input name="{{ $pfx }}_occupation" :disabled="editData.tipe_data!=='ortu'"
                                       x-model="editData.{{ $pfx }}_occupation"
                                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block text-sm mb-1 text-gray-600">Telp</label>
                                <input name="{{ $pfx }}_phone" :disabled="editData.tipe_data!=='ortu'"
                                       x-model="editData.{{ $pfx }}_phone"
                                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm mb-1 text-gray-600">Email</label>
                                <input name="{{ $pfx }}_email" :disabled="editData.tipe_data!=='ortu'"
                                       x-model="editData.{{ $pfx }}_email"
                                       class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm mb-1 text-gray-600">Alamat</label>
                                <textarea name="{{ $pfx }}_address" rows="2"
                                          :disabled="editData.tipe_data!=='ortu'"
                                          x-model="editData.{{ $pfx }}_address"
                                          class="w-full border rounded px-2 py-1 focus:ring-sky-500"></textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </template>

        {{-- ===== DATA WALI ===== --}}
        <template x-if="editData.tipe_data==='wali'">
            <div class="mt-4 border-t pt-4">
                <h4 class="font-semibold text-slate-700 mb-3">Data Wali</h4>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Nama Wali</label>
                        <input name="guardian_name" :disabled="editData.tipe_data!=='wali'"
                               x-model="editData.guardian_name"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Hubungan</label>
                        <input name="guardian_relation" :disabled="editData.tipe_data!=='wali'"
                               x-model="editData.guardian_relation"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Telp</label>
                        <input name="guardian_phone" :disabled="editData.tipe_data!=='wali'"
                               x-model="editData.guardian_phone"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-600">Email</label>
                        <input name="guardian_email" :disabled="editData.tipe_data!=='wali'"
                               x-model="editData.guardian_email"
                               class="w-full border rounded px-2 py-1 focus:ring-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1 text-gray-600">Alamat</label>
                        <textarea name="guardian_address" rows="2"
                                  :disabled="editData.tipe_data!=='wali'"
                                  x-model="editData.guardian_address"
                                  class="w-full border rounded px-2 py-1 focus:ring-sky-500"></textarea>
                    </div>
                </div>
            </div>
        </template>

        {{-- ===== AKSI ===== --}}
        <div class="mt-8 flex gap-3">
            <button type="submit"
                    class="px-6 py-2 bg-sky-600 text-white rounded-full hover:bg-sky-700">
                Simpan Perubahan
            </button>
            <button type="button" @click="mode='view'"
                    class="px-6 py-2 bg-gray-200 rounded-full">
                Batal
            </button>
        </div>
    </form>
</div>


</div>
