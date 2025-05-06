

@extends('layouts.dashboard')

@section('content')
<!-- ini dashboard orang tua -->
<main class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
<div class="flex-1 p-5 max-md:p-2.5 max-sm:p-2.5">

    {{-- Header --}}
    <header class="flex gap-3 items-center flex-wrap mt-11 md:mt-0">
    <img 
        src="https://cdn.builder.io/api/v1/image/assets/TEMP/7c611c0665bddb8f69e3b35c80f5477a6f0b559e?placeholderIfAbsent=true" 
        alt="PAUD Logo" 
        class="h-12 w-auto max-w-[60px]"
    />
    <div class="flex flex-col">
        <h1 class="text-[24px] md:text-2xl  font-bold text-sky-600">PAUD Kartika Pradana</h1>
        <p class=" text-[8px] text-sky-800">
            Taman Penitipan Anak, Kelompok Bermain, dan Taman Kanak-Kanak
        </p>
    </div>
</header>
<x-header.parent-breadcrump-header
    label="Data Anak">
</x-header.parent-breadcrump-header>

<div class="text-lg font-semibold text-sky-700 mb-2">Data Anak</div>

<div class="flex-1 w-full">
  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white md:p-6 p-2 rounded-lg max-w-[1241px] max-md:grid-cols-1">
    
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

</div>

    <!-- Header Icons -->
    <x-header.icon-header />
    

</main>
@endsection
