

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

{{-- Tombol Kembali --}}
<div class="mb-4">
    <a href="{{ url('/dashboard') }}" 
       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali ke Dashboard
    </a>
</div>

<div class="text-lg font-semibold text-sky-700 mb-2">Data Anak</div>

{{-- Informasi untuk perubahan data --}}
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-lg">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Informasi:</strong> Jika ada data yang tidak sesuai, silakan hubungi walikelas atau pihak sekolah untuk melakukan pengubahan data.
            </p>
        </div>
    </div>
</div>

@if($children && $children->count() > 0)
    @foreach($children as $child)
    <div class="flex-1 w-full mb-6">
      <div class="bg-gray-50 p-4 rounded-lg mb-4">
        <div class="flex items-center gap-4">
          @if($child->photo)
            <img src="{{ asset('storage/'.$child->photo) }}" alt="{{ $child->name }}" 
                 class="w-16 h-16 rounded-full object-cover">
          @else
            <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
          @endif
          <div>
            <h3 class="text-lg font-semibold text-sky-600 mb-2">{{ $child->name }}</h3>
            <p class="text-sm text-gray-600">
              Nomor Siswa: {{ $child->student_number ?? '-' }} | 
              Kelas: {{ $child->classrooms->pluck('name')->join(', ') ?: 'Belum ada kelas' }}
            </p>
          </div>
        </div>
      </div>
      
      <article class="grid grid-cols-2 gap-6 mx-auto w-full bg-white md:p-6 p-2 rounded-lg max-w-[1241px] max-md:grid-cols-1">
        
        {{-- Data Anak --}}
        <div class="flex flex-col gap-2">
          <h4 class="text-md font-semibold text-sky-700 mb-2">Data Anak</h4>
          <x-field.profile-field label="Nama Lengkap" name="nama_lengkap" value="{{ $child->name }}" editable="false" />
          <x-field.profile-field label="Nomor Siswa" name="student_number" value="{{ $child->student_number ?? '-' }}" editable="false" />
          <x-field.profile-field label="Tanggal Lahir" name="tanggal_lahir" value="{{ $child->birth_date ? \Carbon\Carbon::parse($child->birth_date)->format('d/m/Y') : '-' }}" editable="false" />
          <x-field.profile-field label="Jenis Kelamin" name="jenis_kelamin" value="{{ $child->gender == 'male' ? 'Laki-laki' : ($child->gender == 'female' ? 'Perempuan' : '-') }}" editable="false" />
          <x-field.profile-field label="Riwayat Kesehatan" name="medical_history" value="{{ $child->medical_history ?? '-' }}" editable="false" />
          <x-field.profile-field label="Kelas" name="kelas" value="{{ $child->classrooms->pluck('name')->join(', ') ?: 'Belum ada kelas' }}" editable="false" />
          
          {{-- Foto Anak --}}
          <div class="mt-4">
            <h5 class="text-sm font-medium text-sky-600 mb-3">Foto Anak</h5>
            <div class="w-full max-w-sm mx-auto">
              @if($child->photo)
                <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200">
                  <img src="{{ asset('storage/'.$child->photo) }}" 
                       alt="Foto {{ $child->name }}" 
                       class="w-full h-full object-contain"
                       onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                  {{-- Fallback jika foto tidak dapat dimuat --}}
                  <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 text-gray-500" style="display:none;">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-sm">Foto tidak dapat dimuat</span>
                  </div>
                </div>
              @else
                <div class="w-full h-64 bg-gray-100 rounded-lg flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                  <svg class="w-16 h-16 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  <span class="text-gray-500 text-sm">Foto belum tersedia</span>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Data Orang Tua/Wali --}}
        <div class="flex flex-col gap-2">
          <h4 class="text-md font-semibold text-sky-700 mb-2">Data Orang Tua/Wali</h4>
          
          @if($child->parentProfiles && $child->parentProfiles->count() > 0)
            @php
              $hasGuardian = $child->parentProfiles->where('relation', 'guardian')->first();
              $father = $child->parentProfiles->where('relation', 'father')->first();
              $mother = $child->parentProfiles->where('relation', 'mother')->first();
            @endphp
            
            @if($hasGuardian)
              {{-- Jika ada wali, tampilkan data wali --}}
              <x-field.profile-field label="Nama Wali" name="wali_name" value="{{ $hasGuardian->name }}" editable="false" />
              <x-field.profile-field label="NIK Wali" name="wali_nik" value="{{ $hasGuardian->nik ?? '-' }}" editable="false" />
              <x-field.profile-field label="Nomor Telepon" name="wali_phone" value="{{ $hasGuardian->phone ?? '-' }}" editable="false" />
              <x-field.profile-field label="Email" name="wali_email" value="{{ $hasGuardian->email ?? '-' }}" editable="false" />
              <x-field.profile-field label="Pekerjaan" name="wali_occupation" value="{{ $hasGuardian->occupation ?? '-' }}" editable="false" />
              <x-field.profile-field label="Alamat" name="wali_address" value="{{ $hasGuardian->address ?? '-' }}" editable="false" />
            @else
              {{-- Jika tidak ada wali, tampilkan data ayah dan ibu --}}
              @if($father)
                <div class="mb-4">
                  <h5 class="text-sm font-medium text-gray-700 mb-2">Data Ayah</h5>
                  <div class="space-y-2 pl-4 border-l-2 border-blue-200">
                    <x-field.profile-field label="Nama Ayah" name="father_name" value="{{ $father->name }}" editable="false" />
                    <x-field.profile-field label="NIK Ayah" name="father_nik" value="{{ $father->nik ?? '-' }}" editable="false" />
                    <x-field.profile-field label="Telepon Ayah" name="father_phone" value="{{ $father->phone ?? '-' }}" editable="false" />
                    <x-field.profile-field label="Pekerjaan Ayah" name="father_occupation" value="{{ $father->occupation ?? '-' }}" editable="false" />
                  </div>
                </div>
              @endif
              
              @if($mother)
                <div class="mb-4">
                  <h5 class="text-sm font-medium text-gray-700 mb-2">Data Ibu</h5>
                  <div class="space-y-2 pl-4 border-l-2 border-pink-200">
                    <x-field.profile-field label="Nama Ibu" name="mother_name" value="{{ $mother->name }}" editable="false" />
                    <x-field.profile-field label="NIK Ibu" name="mother_nik" value="{{ $mother->nik ?? '-' }}" editable="false" />
                    <x-field.profile-field label="Telepon Ibu" name="mother_phone" value="{{ $mother->phone ?? '-' }}" editable="false" />
                    <x-field.profile-field label="Pekerjaan Ibu" name="mother_occupation" value="{{ $mother->occupation ?? '-' }}" editable="false" />
                  </div>
                </div>
              @endif
              
              @if($father && $mother && $father->address)
                <x-field.profile-field label="Alamat Keluarga" name="family_address" value="{{ $father->address }}" editable="false" />
              @elseif($mother && $mother->address)
                <x-field.profile-field label="Alamat Keluarga" name="family_address" value="{{ $mother->address }}" editable="false" />
              @endif
            @endif
          @else
            <div class="text-gray-500 text-sm">Data orang tua/wali belum tersedia</div>
          @endif
        </div>

      </article>
    </div>
    @endforeach
@else
    <div class="flex-1 w-full">
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
        <div class="text-yellow-600 mb-2">
          <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-yellow-800 mb-1">Belum Ada Data Anak</h3>
        <p class="text-yellow-700">Saat ini belum ada data anak yang terdaftar untuk akun Anda. Silakan hubungi administrator sekolah untuk informasi lebih lanjut.</p>
      </div>
    </div>
@endif

    <!-- Header Icons -->
    <x-header.icon-header />
    

</main>
@endsection
