@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-xl mx-auto mt-20 p-8 bg-white rounded-lg shadow">

    {{-- Header salam --}}
    <h1 class="text-2xl font-bold mb-4 text-center">
        Halo, {{ auth()->user()->name }}
    </h1>
    <p class="text-lg text-center">
        Anda login sebagai
        <span class="font-semibold text-sky-600">{{ auth()->user()->role }}</span>
    </p>

    {{-- ====================== ADMIN & GURU ====================== --}}
    @php
        $role = auth()->user()->role;
        $isStaff = in_array($role, ['admin', 'teacher']);
    @endphp

    @if ($isStaff)
        {{-- form tambah siswa (kode sama seperti sebelumnya) --}}
        <hr class="my-8">
        <h2 class="text-xl font-semibold mb-4 text-center">Tambah Data Siswa</h2>

        @if(session('success'))
            <p class="mb-4 text-green-600 text-center font-medium">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('students.store') }}" class="space-y-4 max-w-lg mx-auto text-left">
            @csrf
            {{-- nama --}}
            <div>
                <label class="block text-sm text-gray-600">Nama Lengkap</label>
                <input name="name" class="w-full p-2 border rounded-md bg-gray-100" value="{{ old('name') }}" required>
                @error('name')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- tanggal lahir --}}
            <div>
                <label class="block text-sm text-gray-600">Tanggal Lahir</label>
                <input type="date" name="birth_date" class="w-full p-2 border rounded-md bg-gray-100" value="{{ old('birth_date') }}" required>
                @error('birth_date')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- gender --}}
            <div>
                <label class="block text-sm text-gray-600">Jenis Kelamin</label>
                <select name="gender" class="w-full p-2 border rounded-md bg-gray-100" required>
                    <option value="">Pilih</option>
                    <option value="male"   {{ old('gender')=='male'?'selected':'' }}>Laki-laki</option>
                    <option value="female" {{ old('gender')=='female'?'selected':'' }}>Perempuan</option>
                </select>
                @error('gender')<p class="text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <button class="w-full sm:w-1/2 mx-auto block px-4 py-2 bg-sky-600 text-white font-bold rounded-full hover:bg-sky-700">
                Simpan &amp; Buat Token
            </button>
        </form>

    {{-- ====================== ORANG TUA ====================== --}}
    @elseif($role === 'parent')
        <hr class="my-8">
        <h2 class="text-xl font-semibold mb-4 text-center">Data Anak Anda</h2>

        @php($children = auth()->user()->students)

        @if($children->isEmpty())
            <p class="text-center text-gray-500">Belum ada data anak yang terhubung. Silakan hubungi wali kelas.</p>
        @else
            <div class="space-y-4">
                @foreach($children as $child)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="font-semibold text-lg">{{ $child->name }}</p>
                        <p class="text-sm">Tgl Lahir&nbsp;: {{ \Carbon\Carbon::parse($child->birth_date)->format('d-m-Y') }}</p>
                        <p class="text-sm">Jenis Kelamin&nbsp;: {{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
    {{-- ====================== END ROLE SECTION ====================== --}}

    {{-- Tombol logout --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-8 text-center">
        @csrf
        <button class="px-6 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none">
            Logout
        </button>
    </form>
</div>
@endsection
