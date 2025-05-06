
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
              @click="mode = 'detail'"
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
      <x-field.profile-field label="NIK" name="nik_siswa" value="{{ old('nik_siswa') }}" editable="true" />
      <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" editable="true" type="date" id="tanggal" />
      <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}" editable="true" />
      
    </div>

    <div class="flex flex-col gap-2">
    <x-field.profile-field label="Alamat" name="alamat" value="{{ old('alamat') }}" editable="true" />
      <x-field.profile-field label="Riwayat Kesehatan Anak" name="riwayat_kesehatan" value="{{ old('riwayat_kesehatan') }}" editable="true" />
      <x-field.profile-field label="Photo" name="photo" value="{{ old('photo') }}" editable="true" type="file" />
    </div>

  </article>

  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 mt-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    <!-- Data Ayah -->
    <div class="flex flex-col gap-2">
      <h2 class="text-lg font-semibold mb-2">Data Ayah</h2>
      <x-field.profile-field label="Nama Ayah" name="nama_ayah" value="{{ old('nama_ayah') }}" editable="true" />
      <x-field.profile-field label="NIK Ayah" name="nik_ayah" value="{{ old('nik_ayah') }}" editable="true" />
      <x-field.profile-field label="Email Ayah" name="email_ayah" value="{{ old('email_ayah') }}" editable="true" type="email" />
      <x-field.profile-field label="Nomor Telepon Ayah" name="telepon_ayah" value="{{ old('telepon_ayah') }}" editable="true" />
      <x-field.profile-field label="Alamat Ayah" name="alamat_ayah" value="{{ old('alamat_ayah') }}" editable="true" />
      <x-field.profile-field label="Pekerjaan Ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" editable="true" />
    </div>

    <!-- Data Ibu -->
    <div class="flex flex-col gap-2">
      <h2 class="text-lg font-semibold mb-2">Data Ibu</h2>
      <x-field.profile-field label="Nama Ibu" name="nama_ibu" value="{{ old('nama_ibu') }}" editable="true" />
      <x-field.profile-field label="NIK Ibu" name="nik_ibu" value="{{ old('nik_ibu') }}" editable="true" />
      <x-field.profile-field label="Email Ibu" name="email_ibu" value="{{ old('email_ibu') }}" editable="true" type="email" />
      <x-field.profile-field label="Nomor Telepon Ibu" name="telepon_ibu" value="{{ old('telepon_ibu') }}" editable="true" />
      <x-field.profile-field label="Alamat Ibu" name="alamat_ibu" value="{{ old('alamat_ibu') }}" editable="true" />
      <x-field.profile-field label="Pekerjaan Ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" editable="true" />
    </div>
  </article>
  <div class="mt-4">
    <x-button.submit-button />
  </div>
</div>



<div x-show="mode === 'edit'" class="flex-1 w-full">
  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    
    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" editable="true" />
      <x-field.profile-field label="NIK" name="nik_siswa" value="{{ old('nik_siswa') }}" editable="true" />
      <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" editable="true" type="date" id="tanggal" />
      <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}" editable="true" />
      
    </div>

    <div class="flex flex-col gap-2">
    <x-field.profile-field label="Alamat" name="alamat" value="{{ old('alamat') }}" editable="true" />
      <x-field.profile-field label="Riwayat Kesehatan Anak" name="riwayat_kesehatan" value="{{ old('riwayat_kesehatan') }}" editable="true" />
      <x-field.profile-field label="Photo" name="photo" value="{{ old('photo') }}" editable="true" type="file" />
    </div>

  </article>

  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 mt-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    <!-- Data Ayah -->
    <div class="flex flex-col gap-2">
      <h2 class="text-lg font-semibold mb-2">Data Ayah</h2>
      <x-field.profile-field label="Nama Ayah" name="nama_ayah" value="{{ old('nama_ayah') }}" editable="true" />
      <x-field.profile-field label="NIK Ayah" name="nik_ayah" value="{{ old('nik_ayah') }}" editable="true" />
      <x-field.profile-field label="Email Ayah" name="email_ayah" value="{{ old('email_ayah') }}" editable="true" type="email" />
      <x-field.profile-field label="Nomor Telepon Ayah" name="telepon_ayah" value="{{ old('telepon_ayah') }}" editable="true" />
      <x-field.profile-field label="Alamat Ayah" name="alamat_ayah" value="{{ old('alamat_ayah') }}" editable="true" />
      <x-field.profile-field label="Pekerjaan Ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" editable="true" />
    </div>

    <!-- Data Ibu -->
    <div class="flex flex-col gap-2">
      <h2 class="text-lg font-semibold mb-2">Data Ibu</h2>
      <x-field.profile-field label="Nama Ibu" name="nama_ibu" value="{{ old('nama_ibu') }}" editable="true" />
      <x-field.profile-field label="NIK Ibu" name="nik_ibu" value="{{ old('nik_ibu') }}" editable="true" />
      <x-field.profile-field label="Email Ibu" name="email_ibu" value="{{ old('email_ibu') }}" editable="true" type="email" />
      <x-field.profile-field label="Nomor Telepon Ibu" name="telepon_ibu" value="{{ old('telepon_ibu') }}" editable="true" />
      <x-field.profile-field label="Alamat Ibu" name="alamat_ibu" value="{{ old('alamat_ibu') }}" editable="true" />
      <x-field.profile-field label="Pekerjaan Ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" editable="true" />
    </div>
  </article>
  <div class="mt-4">
    <x-button.submit-button />
  </div>
</div>


<div x-show="mode === 'detail'" class="flex-1 w-full">
  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    
    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" editable="false" />
      <x-field.profile-field label="NIK" name="nik_siswa" value="{{ old('nik_siswa') }}" editable="false" />
      <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" editable="false" type="date" id="tanggal" />
      <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}" editable="false" />
      
    </div>

    <div class="flex flex-col gap-2">
    <x-field.profile-field label="Alamat" name="alamat" value="{{ old('alamat') }}" editable="false" />
      <x-field.profile-field label="Riwayat Kesehatan Anak" name="riwayat_kesehatan" value="{{ old('riwayat_kesehatan') }}" editable="false" />
      <x-field.profile-field label="Photo" name="photo" value="{{ old('photo') }}" editable="false" type="file" />
    </div>

  </article>

  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 mt-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    <!-- Data Ayah -->
    <div class="flex flex-col gap-2">
      <h2 class="text-lg font-semibold mb-2">Data Ayah</h2>
      <x-field.profile-field label="Nama Ayah" name="nama_ayah" value="{{ old('nama_ayah') }}" editable="false" />
      <x-field.profile-field label="NIK Ayah" name="nik_ayah" value="{{ old('nik_ayah') }}" editable="false" />
      <x-field.profile-field label="Email Ayah" name="email_ayah" value="{{ old('email_ayah') }}" editable="false" type="email" />
      <x-field.profile-field label="Nomor Telepon Ayah" name="telepon_ayah" value="{{ old('telepon_ayah') }}" editable="false" />
      <x-field.profile-field label="Alamat Ayah" name="alamat_ayah" value="{{ old('alamat_ayah') }}" editable="false" />
      <x-field.profile-field label="Pekerjaan Ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" editable="false" />
    </div>

    <!-- Data Ibu -->
    <div class="flex flex-col gap-2">
      <h2 class="text-lg font-semibold mb-2">Data Ibu</h2>
      <x-field.profile-field label="Nama Ibu" name="nama_ibu" value="{{ old('nama_ibu') }}" editable="false" />
      <x-field.profile-field label="NIK Ibu" name="nik_ibu" value="{{ old('nik_ibu') }}" editable="false" />
      <x-field.profile-field label="Email Ibu" name="email_ibu" value="{{ old('email_ibu') }}" editable="false" type="email" />
      <x-field.profile-field label="Nomor Telepon Ibu" name="telepon_ibu" value="{{ old('telepon_ibu') }}" editable="false" />
      <x-field.profile-field label="Alamat Ibu" name="alamat_ibu" value="{{ old('alamat_ibu') }}" editable="false" />
      <x-field.profile-field label="Pekerjaan Ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" editable="false" />
    </div>
  </article>


        </div>