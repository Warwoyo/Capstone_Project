@props(['mode', 'studentList', 'student' => null])

<div x-data="{ mode: @entangle('mode') }" class="flex-1 w-full">
  <!-- ========================= VIEW MODE ========================= -->
  <div x-show="mode === 'view'" class="flex-1 w-full">
    @if($studentList->isEmpty())
      <div class="py-8 text-center text-slate-500">kelas masih kosong</div>
    @else
      <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
        <!-- Header -->
        <div class="pl-2 flex items-center bg-sky-200 h-[43px] rounded-t-lg">
          @foreach(['Nama Lengkap', 'Kelompok', 'Token', 'Pilihan'] as $header)
            <h3 class="flex-1 text-sm font-medium text-center text-slate-600">{{ $header }}</h3>
          @endforeach
        </div>

        <!-- Rows -->
        @foreach ($studentList as $child)
          <div class="flex items-center px-3 py-1 border border-gray-200">
            <div class="flex-1 text-sm text-center">{{ $child->name }}</div>
            <div class="flex-1 text-sm text-center">{{ $child->group ?? '-' }}</div>
            <div class="flex-1 text-sm text-center">{{ $child->id }}</div>
            <div class="flex-1 text-sm text-center">
              <div class="flex flex-col gap-1 items-center">
                <button @click="$wire.call('editStudent', {{ $child->id }})" class="w-20 text-xs border border-sky-300 text-slate-600 rounded-lg h-[25px]">Edit</button>
                <form method="POST" action="{{ route('students.destroy', $child) }}" onsubmit="return confirm('Hapus data?')">
                  @csrf @method('DELETE')
                  <button class="w-20 text-xs bg-red-500 text-white rounded-lg h-[25px]">Hapus</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <!-- ========================= ADD MODE ========================= -->
  <div x-show="mode === 'add'" class="flex-1 w-full">
    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
      @csrf
      <article class="grid lg:grid-cols-3 md:grid-cols-2 gap-6 p-6 bg-white border rounded-lg">
        <!-- Anak -->
        <div class="flex flex-col gap-2">
          <h2 class="font-semibold text-slate-700 mb-1">Data Anak</h2>
          <x-field.profile-field label="Nama Lengkap"    name="name"          value="{{ old('name') }}" editable="true" />
          <x-field.profile-field label="NIK"             name="nik"           value="{{ old('nik') }}"  editable="true" />
          <x-field.profile-field label="Tanggal Lahir"  name="birth_date"    type="date" value="{{ old('birth_date') }}" editable="true" />
          <x-field.profile-field label="Jenis Kelamin"  name="gender"        value="{{ old('gender') }}" editable="true" />
          <x-field.profile-field label="Alamat"         name="address"       value="{{ old('address') }}" editable="true" />
          <x-field.profile-field label="Riwayat Kesehatan" name="medical_history" value="{{ old('medical_history') }}" editable="true" />
          <x-field.profile-field label="Foto"           name="photo"         type="file" editable="true" />
          <x-field.profile-field label="Kelompok"       name="group"         value="{{ old('group') }}" editable="true" />
        </div>

        <!-- Ibu -->
        <div class="flex flex-col gap-2">
          <h2 class="font-semibold text-slate-700 mb-1">Data Ibu</h2>
          <x-field.profile-field label="Nama Ibu"       name="mother_name"   value="{{ old('mother_name') }}" editable="true" />
          <x-field.profile-field label="NIK Ibu"        name="mother_nik"    value="{{ old('mother_nik') }}" editable="true" />
          <x-field.profile-field label="Telp Ibu"       name="mother_phone"  value="{{ old('mother_phone') }}" editable="true" />
          <x-field.profile-field label="Email Ibu"      name="mother_email"  value="{{ old('mother_email') }}" editable="true" />
          <x-field.profile-field label="Alamat Ibu"     name="mother_address" value="{{ old('mother_address') }}" editable="true" />
          <x-field.profile-field label="Pekerjaan Ibu"  name="mother_job"    value="{{ old('mother_job') }}" editable="true" />
        </div>

        <!-- Ayah -->
        <div class="flex flex-col gap-2">
          <h2 class="font-semibold text-slate-700 mb-1">Data Ayah</h2>
          <x-field.profile-field label="Nama Ayah"       name="father_name"   value="{{ old('father_name') }}" editable="true" />
          <x-field.profile-field label="NIK Ayah"        name="father_nik"    value="{{ old('father_nik') }}" editable="true" />
          <x-field.profile-field label="Telp Ayah"       name="father_phone"  value="{{ old('father_phone') }}" editable="true" />
          <x-field.profile-field label="Email Ayah"      name="father_email"  value="{{ old('father_email') }}" editable="true" />
          <x-field.profile-field label="Alamat Ayah"     name="father_address" value="{{ old('father_address') }}" editable="true" />
          <x-field.profile-field label="Pekerjaan Ayah"  name="father_job"    value="{{ old('father_job') }}" editable="true" />
        </div>
      </article>
      <div class="mt-4"><x-button.submit-button /></div>
    </form>
  </div>

  <!-- ========================= EDIT MODE ========================= -->
  <div x-show="mode === 'edit'" class="flex-1 w-full">
    <form method="POST" action="{{ route('students.update', $student?->id) }}" enctype="multipart/form-data">
      @csrf @method('PUT')
      <article class="grid lg:grid-cols-3 md:grid-cols-2 gap-6 p-6 bg-white border rounded-lg">
        <!-- Anak -->
        <div class="flex flex-col gap-2">
          <h2 class="font-semibold text-slate-700 mb-1">Data Anak</h2>
          <x-field.profile-field label="Nama Lengkap"    name="name"          value="{{ old('name', $student?->name) }}" editable="true" />
          <x-field.profile-field label="NIK"             name="nik"           value="{{ old('nik', $student?->nik) }}" editable="true" />
          <x-field.profile-field label="Tanggal Lahir"  name="birth_date"    type="date" value="{{ old('birth_date', $student?->birth_date) }}" editable="true" />
          <x-field.profile-field label="Jenis Kelamin"  name="gender"        value="{{ old('gender', $student?->gender) }}" editable="true" />
          <x-field.profile-field label="Alamat"         name="address"       value="{{ old('address', $student?->address) }}" editable="true" />
          <x-field.profile-field label="Riwayat Kesehatan" name="medical_history" value="{{ old('medical_history', $student?->medical_history) }}" editable="true" />
          <x-field.profile-field label="Foto"           name="photo"         type="file" editable="true" />
          <x-field.profile-field label="Kelompok"       name="group"         value="{{ old('group', $student?->group) }}" editable="true" />
        </div>

        <!-- Ibu -->
        <div class="flex flex-col gap-2">
          <h2 class="font-semibold text-slate-700 mb-1">Data Ibu</h2>
          <x-field.profile-field label="Nama Ibu"       name="mother_name"   value="{{ old('mother_name', $student?->mother?->name) }}" editable="true" />
          <x-field.profile-field label="NIK Ibu"        name="mother_nik"    value="{{ old('mother_nik', $student?->mother?->nik) }}" editable="true" />
          <x-field.profile-field label="Telp Ibu"       name="mother_phone"  value="{{ old('mother_phone', $student?->mother?->phone) }}" editable="true" />
          <x-field.profile-field label="Email Ibu"      name="mother_email"  value="{{ old('mother_email', $student?->mother?->email) }}" editable="true" />
          <x-field.profile-field label="Alamat Ibu"     name="mother_address" value="{{ old('mother_address', $student?->mother?->address) }}" editable="true" />
          <x-field.profile-field label="Pekerjaan Ibu"  name="mother_job"    value="{{ old('mother_job', $student?->mother?->occupation) }}" editable="true" />
        </div>

        <!-- Ayah -->
        <div class="flex flex-col gap-2">
          <h2 class="font-semibold text-slate-700 mb-1">Data Ayah</h2>
          <x-field.profile-field label="Nama Ayah"       name="father_name"   value="{{ old('father_name', $student?->father?->name) }}" editable="true" />
          <x-field.profile-field label="NIK Ayah"        name="father_nik"    value="{{ old('father_nik', $student?->father?->nik) }}" editable="true" />
          <x-field.profile-field label="Telp Ayah"       name="father_phone"  value="{{ old('father_phone', $student?->father?->phone) }}" editable="true" />
          <x-field.profile-field label="Email Ayah"      name="father_email"  value="{{ old('father_email', $student?->father?->email) }}" editable="true" />
          <x-field.profile-field label="Alamat Ayah"     name="father_address" value="{{ old('father_address', $student?->father?->address) }}" editable="true" />
          <x-field.profile-field label="Pekerjaan Ayah"  name="father_job"    value="{{ old('father_job', $student?->father?->occupation) }}" editable="true" />
        </div>
      </article>
      <div class="mt-4"><x-button.submit-button /></div>
    </form>
  </div>
</div>