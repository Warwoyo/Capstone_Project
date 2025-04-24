@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="max-w-2xl mx-auto mt-2 p-6 bg-white rounded-lg">

    
    {{-- Header --}}
    <header class="flex flex-col items-center">
      <img
        src="https://cdn.builder.io/api/v1/image/assets/TEMP/f07397bd67a7aaaad2f851030494b9de122024d4?placeholderIfAbsent=true&apiKey=2e4725bf302f406685d56a363a84e166"
        alt="Logo"
        class="object-contain self-center max-w-full aspect-[1.38] w-[120px]"
      />
      <h1 class="mt-0 text-2xl font-bold text-center text-sky-600 max-md:max-w-full ">
  Daftar Akun
</h1>

    </header>

    {{-- Form --}}
    <form method="POST" action="" class="mx-auto space-y-2 mt-4 max-w-4xl ">
      
      @csrf

      {{-- Nomor Induk Guru --}}
      <div>
        <label for="teacherId" class="block text-sm text-gray-600 ">Nomor Induk Guru</label>
        <input
          type="text"
          name="teacherId"
          id="teacherId"
          value="{{ old('teacherId') }}"
          placeholder="225xxxxxxxxx"
          class="w-full px-4 py-2 mt-2 border border-gray-300  rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
          required
        >
      </div>

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

{{-- Teks Kebijakan Servis --}}
<div class="text-xs text-center text-gray-500 mt-2">
  Dengan membuat akun, Anda telah menyetujui <a href="#" class="text-sky-600 hover:text-sky-700">Kebijakan Servis kami</a>.
</div>

{{-- Tombol Daftar --}}
<div class="text-center">
  <button type="submit"
  class="w-1/2 mx-auto px-4 py-2 mt-4 bg-sky-600 text-white font-bold rounded-full hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
  Daftar
  </button>
</div>

{{-- Footer --}}
<div class="mt-4 text-center text-sm text-gray-600">
  Sudah memiliki akun? 
  <a href="{{ route('login') }}" class="text-sky-600 hover:text-sky-700">Masuk</a>
</div>

<script>
  const form = document.querySelector('form');
  const fields = {
    teacherId: {
      input: document.getElementById('teacherId'),
      error: 'Nomor Induk Guru tidak boleh kosong.',
      validate: val => val.trim() !== ''
    },
    fullName: {
      input: document.getElementById('fullName'),
      error: 'Nama Lengkap tidak boleh kosong.',
      validate: val => val.trim() !== ''
    },
    phone: {
      input: document.getElementById('phone'),
      error: 'Nomor Handphone tidak valid (08xxxxxxxx).',
      validate: val => /^08\d{8,10}$/.test(val.trim())
    },
    email: {
      input: document.getElementById('email'),
      error: 'Email tidak valid.',
      validate: val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim())
    },
    password: {
      input: document.getElementById('password'),
      error: 'Kata Sandi tidak boleh kosong.',
      validate: val => val.trim() !== ''
    }
  };

  function resetErrors() {
    document.querySelectorAll('.input-error').forEach(el => el.remove());
    Object.values(fields).forEach(field => {
      field.input.classList.remove('border-orange-500');
    });
  }

  function showError(input, message) {
    input.classList.add('border-orange-500');
    const errorText = document.createElement('p');
    errorText.className = 'input-error text-sm text-orange-500 mt-1';
    errorText.textContent = message;
    input.parentElement.appendChild(errorText);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    resetErrors();

    let isValid = true;

    for (const key in fields) {
      const { input, error, validate } = fields[key];
      if (!validate(input.value)) {
        showError(input, error);
        isValid = false;
      }
    }

    if (isValid) {
      alert('Pendaftaran berhasil! (Frontend Demo)');
      // form.submit(); // Jika ingin submit ke backend
    }
  });
</script>


@endsection
