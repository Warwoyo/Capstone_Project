
@props(['mode' , 'studentList' , 'class'])

<div x-data="{ mode: @entangle('mode') }" class="flex-1 w-full">
<!-- View Data -->
<div x-show="mode === 'view'" class="flex-1 w-full">
  <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">

    <!-- Header -->
    <div class="pl-2 flex items-center bg-sky-200 h-[43px] rounded-t-lg">
      @foreach (['Nama Lengkap', 'Nama Kelompok', 'Token', 'Pilihan'] as $header)
        <h3 class="flex-1 text-sm font-medium text-center text-slate-600 max-md:text-sm max-sm:text-xs">
          {{ $header }}
        </h3>
      @endforeach
    </div>

    <!-- Data Rows -->
    @foreach ($studentList as $student)
      <div class="flex items-center px-3 py-1 border border-gray-200">
        <div class="flex-1 text-sm text-center text-slate-600">{{ $student['nama'] }}</div>
        <div class="flex-1 text-sm text-center text-slate-600">{{ $student['kelompok'] ?? '-' }}</div>
        <div class="flex-1 text-sm text-center text-slate-600">{{ $student['token'] ?? '-' }}</div>
        <div class="flex-1 text-sm text-center text-slate-600">
          <div class="flex flex-col gap-1 items-center">
            <button
              class="w-20 text-xs font-medium bg-transparent rounded-lg border border-sky-300 text-slate-600 h-[25px]"
              @click="mode = 'edit'"
            >
              Edit
            </button>
            <button
              class="w-20 text-xs font-medium text-white bg-sky-300 rounded-lg border border-sky-300 h-[25px]"
              @click="viewStudent({{ $student['id'] }})"
            >
              Detail
            </button>
          </div>
        </div>
      </div>
    @endforeach

  </div>
</div>


<!-- Add Data -->

<div x-show="mode === 'add'" class="flex-1 w-full">
  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    
    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" editable="true" />
      <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" editable="true" type="date" id="tanggal" />
      <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}" editable="true" />
      <x-field.profile-field label="Alamat" name="alamat" value="{{ old('alamat') }}" editable="true" />
    </div>

    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Orang Tua" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" editable="true" />
      <x-field.profile-field label="Riwayat Kesehatan Anak" name="riwayat_kesehatan" value="{{ old('riwayat_kesehatan') }}" editable="true" />
      <x-field.profile-field label="Nomor Telepon Orang Tua" name="telepon_orang_tua" value="{{ old('telepon_orang_tua') }}" editable="true" />
      <x-field.profile-field label="Photo" name="photo" value="{{ old('photo') }}" editable="true" type="file" />
    </div>

  </article>

  <div class="mt-4">
    <x-button.submit-button />
  </div>
</div>


<div x-show="mode === 'edit'" class="flex-1 w-full">
  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    
    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $student['nama'] ?? '') }}" editable="true" />
      <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" editable="true" type="date" id="tanggal"/>
      <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}" editable="true" />
      <x-field.profile-field label="Alamat" name="alamat" value="{{ old('alamat') }}" editable="true" />
    </div>

    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Orang Tua" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" editable="true" />
      <x-field.profile-field label="Riwayat Kesehatan Anak" name="riwayat_kesehatan" value="{{ old('riwayat_kesehatan') }}" editable="true" />
      <x-field.profile-field label="Nomor Telepon Orang Tua" name="telepon_orang_tua" value="{{ old('telepon_orang_tua') }}" editable="true" />
      <x-field.profile-field label="Photo" name="photo" value="{{ old('photo') }}" editable="true" type="file" />
    </div>

  </article>

  <div class="mt-4">
    <x-button.submit-button />
  </div>
</div>

        </div>