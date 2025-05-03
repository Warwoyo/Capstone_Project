

@extends('layouts.dashboard')

@section('content')

<main x-data="{ mode: 'view' }" class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
    <section class="flex-1 pt-8 px-8 max-md:p-4 max-sm:p-6">
    <!-- ini bagian pencarian dan tambah kelas -->
    
    <header class="flex gap-3 items-center flex-wrap mt-11 md:mt-0">
    <img 
        src="https://cdn.builder.io/api/v1/image/assets/TEMP/7c611c0665bddb8f69e3b35c80f5477a6f0b559e?placeholderIfAbsent=true" 
        alt="PAUD Logo" 
        class="h-12 w-auto max-w-[60px]"
    />
    <div class="flex flex-col">
        <h1 class="text-[21px] md:text-2xl  font-bold text-sky-600">PAUD Kartika Pradana</h1>
        <p class=" text-[7px] text-sky-800 md:text-[8px]">
            Taman Penitipan Anak, Kelompok Bermain, dan Taman Kanak-Kanak
        </p>
    </div>
</header>

<!-- View Data -->
<div x-show="mode === 'view'" class="flex-1 w-full pt-2">
<div class="p-1 md:p-5">
    <x-header.search-parent-header />
</div>
  <div class="overflow-y-auto hide-scrollbar max-h-[70vh] md:max-h-[70vh]">

    <!-- Header -->
    <div class="pl-2 flex items-center bg-sky-200 h-[43px] rounded-t-lg">
      @foreach (['Nama Lengkap', 'Token', 'Pilihan'] as $header)
        <h3 class="flex-1 text-sm font-medium text-center text-slate-600 max-md:text-sm max-sm:text-xs">
          {{ $header }}
        </h3>
      @endforeach
    </div>

    <!-- Data Rows -->
    @foreach ($parents as $parent)
      <div class="flex items-center px-3 py-1 border border-gray-200">
        <div class="flex-1 text-sm text-center text-slate-600">{{ $parent['nama'] }}</div>
        <div class="flex-1 text-sm text-center text-slate-600">{{ $parent['token'] ?? '-' }}</div>
        <div class="flex-1 text-sm text-center text-slate-600">
        <div class="flex flex-col md:flex-row gap-2 justify-center items-center">

            <button
              class="w-20 text-xs font-medium bg-transparent rounded-lg border border-sky-300 text-slate-600 h-[25px]"
              @click="mode = 'edit'"
            >
              Edit
            </button>
            <button
              class="w-20 text-xs font-medium text-white bg-red-500 rounded-lg border border-red-300 h-[25px]"
              @click="viewparent({{ $parent['id'] }})"
            >
              Hapus
            </button>
          </div>
        </div>
      </div>
    @endforeach

  </div>
</div>


<!-- Add Data -->

<div x-show="mode === 'add'" class="flex-1 w-full pt-1">
  <article class="grid grid-cols-1 gap-6 mx-auto w-full bg-white rounded-lg max-w-[1241px] max-md:grid-cols-1">
    
   
  {{-- Form --}}
    <form method="POST" action="" class="w-full mx-auto space-y-2 max-w-4xl p-4">
      
      @csrf

      {{-- Nomor Induk Guru --}}
      {{-- Nama Lengkap --}}
      <div>
        <label for="fullName" class="block text-sm text-gray-600">Nama Lengkap</label>
        <input
          type="text"
          name="fullName"
          id="fullName"
          value="{{ old('fullName') }}"
          placeholder="Contoh Nama"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
          required
        >
      </div>

      {{-- Nomor Handphone --}}
      <div>
        <label for="phone" class="block text-sm text-gray-600">Nomor Handphone</label>
        <input
          type="tel"
          name="phone"
          id="phone"
          value="{{ old('phone') }}"
          placeholder="08xxxx"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
          required
        >
      </div>

      {{-- Email --}}
      <div>
        <label for="email" class="block text-sm text-gray-600">Email</label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          placeholder="contoh@gmail.com"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
          required
        >
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="block text-sm text-gray-600">Kata Sandi</label>
        <input
          type="password"
          name="password"
          id="password"
          placeholder="Masukkan Kata Sandi"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200 "
          required
        >
      </div>
      {{-- Password --}}
<div>
  <label for="password" class="block text-sm text-gray-600">Kata Sandi</label>
  <input type="password" name="password" id="password" placeholder="Masukkan Kata Sandi"
    class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-300" required>
</div>

{{-- Tombol Daftar --}}
<div class="text-center">
  <button type="submit"
  class="w-1/2 mx-auto px-4 md:px-2 py-2 mt-2 bg-sky-600 text-white font-bold rounded-full hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
  Daftar
  </button>
</div>
  </article>
</div>


<div x-show="mode === 'edit'" class="flex-1 w-full pt-2">
  <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white p-6 rounded-lg border border-gray-200 max-w-[1241px] max-md:grid-cols-1">
    
    <div class="flex flex-col gap-2">
      <x-field.profile-field label="Nama Lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $parent['nama'] ?? '') }}" editable="true" />
      <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" editable="true" type="date" id="tanggal"/>
      <x-field.profile-field label="Nomor Telepon" name="telepon_orang_tua" value="{{ old('telepon_orang_tua') }}" editable="true" />
      </div>

    <div class="flex flex-col gap-2">
    <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ old('jenis_kelamin') }}" editable="true" />
    <x-field.profile-field label="Alamat" name="alamat" value="{{ old('alamat') }}" editable="true" />
    <x-field.profile-field label="Pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" editable="true" />
    </div>

  </article>

  <div class="mt-3 md:mt-0">
    <x-button.submit-button />
  </div>

        </div>
    <!-- Header Icons -->
    <x-header.icon-header />

</main>
@endsection
