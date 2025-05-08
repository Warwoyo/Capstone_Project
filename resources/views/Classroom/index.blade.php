

@extends('layouts.dashboard')

@section('content')

<main x-data="{ mode: 'view' }" class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
    <section x-show="mode === 'view'" class="flex-1 pt-8 px-8 max-md:p-4 max-sm:p-6">
    <!-- ini bagian pencarian dan tambah kelas -->
    
    <x-header.search-header />

<div class="max-w-screen-xl mx-auto pt-3">
  <!-- Container Scrollable -->
  <div class="overflow-y-auto hide-scrollbar max-h-[67vh] md:max-h-[71vh]">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-5">
      @foreach ($classroom as $class)
        <article
          class="flex relative justify-start items-center pl-4 mx-auto w-full bg-sky-100 rounded-2xl h-[121px] max-w-[600px] max-md:pl-5 max-md:max-w-[991px] max-sm:pl-4 max-sm:max-w-screen-sm overflow-hidden"
        >
          <div
            aria-hidden="true"
            class="absolute -left-8 bg-sky-200 rounded-full h-[151px] top-[-38px] w-[151px] z-0"
          ></div>
          <a 
  href="{{ route('classroom.tab', ['class' => $class['id'], 'tab' => 'pengumuman']) }}"
  class="block"
  aria-label="Lihat Detail {{ $class['title'] }}"
>
          <div class="flex flex-col gap-1 justify-center items-start z-10 relative">
            
            <h2 class="text-base font-bold text-sky-800 max-md:text-base max-sm:text-sm">
              {{ $class['name'] }}
            </h2>
            <p class="text-sm text-gray-500 max-md:text-sm max-sm:text-xs">
              {{ $class['description'] }}
            </p>
          </div>
</a>
          <a
          href="{{ route('classroom.tab', ['class' => $class['id'], 'tab' => 'pengumuman']) }}"
  class="absolute bottom-2 right-4 flex gap-2 items-center mt-2 hover:opacity-80 transition-opacity"
  aria-label="Lihat Detail {{ $class['title'] }}"
>
            <span>
              <svg
                width="19"
                height="19"
                viewBox="0 0 19 19"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
                class="icon"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z"
                  fill="#065986"
                />
              </svg>
            </span>
            <span class="text-xs font-medium text-sky-800 max-md:text-xs max-sm:text-xs">
              Lihat Detail
            </span>
</a>
        </article>
      @endforeach
    </div>
  </div>
</div>
    </section>

    <section x-show="mode === 'add'" class="flex-1 pt-8 px-8 max-md:p-4 max-sm:p-6">
    <!-- ini bagian pencarian dan tambah kelas -->
    
    <x-header.search-header />

    <div class="pt-2 flex flex-col md:flex-row gap-1 md:gap-6 w-full">
    <!-- Kolom Kiri -->
    <div class="w-full md:w-1/2">
    <form method="POST" action="{{ route('classroom.store') }}" enctype="multipart/form-data" class="flex flex-col gap-3.5">
        @csrf
        <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
            <!-- Form Input Nama Kelas -->
            <div class="flex flex-col gap-1.5">
                <label class="text-xs text-slate-600 max-sm:text-sm">
                    <span>Nama Kelas</span><span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    placeholder="Contoh: Kelas Pelangi Ceria"
                    required
                    class="px-4 py-0 h-10 text-sm font-medium text-gray-400 bg-gray-50 rounded-3xl border border-sky-600 w-full"
                />
            </div>

            <!-- Form Upload Gambar -->
            <div class="flex flex-col gap-1.5 max-md:w-full max-sm:w-full">
                <label class="text-xs text-slate-600 max-sm:text-sm">Gambar</label>
                <label class="cursor-pointer">
                    <div class="flex items-center px-2 py-0 text-xs font-medium text-gray-400 bg-white rounded-3xl border border-sky-600 border-dashed h-[40px] w-full">
                        <svg width="24" height="24" ... class="mr-2.5">...</svg>
                        <span>Klik atau seret gambar ke area ini untuk upload</span>
                    </div>
                    <input type="file" name="photo" class="hidden" />
                </label>
            </div>

            <!-- Wali Kelas -->
            <div class="flex flex-col gap-1.5">
                <label class="text-xs text-slate-600 max-sm:text-sm">
                    <span>Wali Kelas</span><span class="text-red-500">*</span>
                </label>
                <select name="owner_id" required class="px-4 py-2 bg-gray-50 border border-sky-600 rounded-3xl text-sm text-gray-700">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
</div>

<!-- Kolom Kanan -->
<div class="w-full md:w-1/2 flex flex-col">
    <!-- Deskripsi -->
    <div class="flex flex-col gap-1.5">
        <label class="text-xs text-slate-600 max-sm:text-sm">
            <span>Deskripsi</span><span class="text-red-500">*</span>
        </label>
        <textarea
            name="description"
            class="p-2.5 text-xs font-medium text-gray-500 h-[80px] bg-gray-50 rounded-3xl resize-none border border-sky-600"
            placeholder="Masukkan deskripsi kelas..."
        ></textarea>
    </div>

    <!-- Toolbar (kosmetik, non-fungsional untuk sekarang) -->
    <div class="flex flex-col px-4 py-0 bg-gray-50 rounded-3xl border-sky-600 border-solid border-[1.5px]">
        <div class="flex gap-3 items-center px-0 py-2.5 border-b border-gray-200">
            <button class="text-lg text-black font-bold">B</button>
            <button class="text-lg text-black underline">U</button>
        </div>
    </div>

    <!-- Tombol Submit -->
    <div class="mt-4 flex justify-center">
        <button type="submit" class="bg-sky-600 text-white font-semibold px-6 py-2 rounded-full hover:bg-sky-700">
            Simpan Kelas
        </button>
    </div>
</form>

    </section>
    
    <!-- Header Icons -->
    <x-header.icon-header />

</main>
@endsection
