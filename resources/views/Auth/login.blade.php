@extends('layouts.app')

@section('title', 'Login')

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
        Login Akun
      </h1>
    </header>

    {{-- Form --}}
    <form id="loginForm" class="mx-auto space-y-2 mt-4 max-w-4xl ">

      {{-- Nomor Handphone --}}
      <div>
        <label for="phone" class="block text-sm text-gray-600">Nomor Handphone</label>
        <input
          type="tel"
          name="phone"
          id="phone"
          placeholder="08xxxx"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
          required
        >
        <p id="phoneError" class="text-sm text-orange-500 mt-1 hidden">Nomor telepon tidak valid.</p>
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="font-poppins block text-sm text-gray-600">Kata Sandi</label>
        <input
          type="password"
          name="password"
          id="password"
          placeholder="Masukkan Kata Sandi"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
          required
        >
        <p id="passwordError" class="text-sm text-orange-500 mt-1 hidden">Kata sandi tidak boleh kosong.</p>
      </div>

      {{-- Tombol Login --}}
      <div class="text-center">
        <button type="submit"
        class="w-1/2 mx-auto px-4 py-2 mt-4 bg-sky-600 text-white font-bold rounded-full hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
        Login
        </button>
      </div>

      {{-- Footer --}}
      <div class="mt-4 text-center text-sm text-gray-600">
        Belum Memiliki Akun? 
        <a href="{{ route('register') }}" class="text-sky-600 hover:text-sky-700">Daftar</a>
      </div>
    </form>

</div>

<script>
  const form = document.getElementById('loginForm');
  const phoneInput = document.getElementById('phone');
  const passwordInput = document.getElementById('password');
  const phoneError = document.getElementById('phoneError');
  const passwordError = document.getElementById('passwordError');

  function resetErrorStyles() {
    phoneInput.classList.remove('border-orange-500');
    phoneError.classList.add('hidden');
    passwordInput.classList.remove('border-orange-500');
    passwordError.classList.add('hidden');
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    resetErrorStyles();

    let isValid = true;

    // Validasi nomor telepon
    const phoneRegex = /^08\d{8,10}$/;
    if (!phoneRegex.test(phoneInput.value)) {
      phoneInput.classList.add('border-orange-500');
      phoneError.classList.remove('hidden');
      isValid = false;
    }

    // Validasi password
    if (passwordInput.value.trim() === '') {
      passwordInput.classList.add('border-orange-500');
      passwordError.classList.remove('hidden');
      isValid = false;
    }

    if (isValid) {
      alert('Login berhasil (validasi lulus)');
      // form.submit(); // aktifkan ini jika nanti backend sudah siap
    }
  });
</script>

@endsection
