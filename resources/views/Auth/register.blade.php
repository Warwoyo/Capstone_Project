@extends('layouts.app')

@section('title', 'Registrasi Orang Tua')

@section('content')
<div class="max-w-2xl mx-auto mt-2 p-6 bg-white rounded-lg">

    {{-- Header --}}
    <header class="flex flex-col items-center">
      <img
        src="https://cdn.builder.io/api/v1/image/assets/TEMP/f07397bd67a7aaaad2f851030494b9de122024d4?placeholderIfAbsent=true&apiKey=2e4725bf302f406685d56a363a84e166"
        alt="Logo"
        class="object-contain self-center max-w-full aspect-[1.38] w-[120px]"
      />
      <h1 class="mt-0 text-2xl font-bold text-center text-sky-600">
        Registrasi Orang Tua
      </h1>
    </header>

    {{-- Form --}}
    <form id="parentRegisterForm"
          method="POST"
          action="{{ route('parent.register') }}"
          class="mx-auto space-y-2 mt-4 max-w-4xl">
      @csrf

      {{-- Token Registrasi --}}
      <div>
        <label for="token" class="block text-sm text-gray-600">Token Registrasi</label>
        <input type="text" name="token" id="token"
               placeholder="ABCDEFGH"
               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
               value="{{ old('token') }}" required>
        <p id="tokenError" class="text-sm text-orange-500 mt-1 hidden">Token harus 8 karakter huruf/angka.</p>
      </div>

      {{-- Nomor Handphone --}}
      <div>
        <label for="phone" class="block text-sm text-gray-600">Nomor Handphone</label>
        <input type="tel" name="phone_number" id="phone"
               placeholder="08xxxxxxxxxx"
               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
               value="{{ old('phone_number') }}" required>
        <p id="phoneError" class="text-sm text-orange-500 mt-1 hidden">Nomor telepon tidak valid.</p>
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="block text-sm text-gray-600">Kata Sandi</label>
        <input type="password" name="password" id="password"
               placeholder="Masukkan Kata Sandi"
               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
               required>
        <p id="passwordError" class="text-sm text-orange-500 mt-1 hidden">Kata sandi minimal 6 karakter.</p>
      </div>

      {{-- Konfirmasi Password --}}
      <div>
        <label for="password_confirmation" class="block text-sm text-gray-600">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               placeholder="Ulangi Kata Sandi"
               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
               required>
        <p id="confirmError" class="text-sm text-orange-500 mt-1 hidden">Konfirmasi tidak cocok.</p>
      </div>

      {{-- Tombol Daftar --}}
      <div class="text-center">
        <button type="submit"
                class="w-full sm:w-1/2 mx-auto px-4 py-2 mt-4 bg-sky-600
                       text-white font-bold rounded-full hover:bg-sky-700
                       focus:outline-none focus:ring-2 focus:ring-sky-500">
            Daftar &amp; Masuk
        </button>
      </div>

      {{-- Error backend --}}
      @if ($errors->any())
        <p class="text-red-500 text-center text-sm mt-3">
           {{ $errors->first() }}
        </p>
      @endif

      {{-- Footer --}}
      <div class="mt-4 text-center text-sm text-gray-600">
        Sudah punya akun? <a href="{{ route('login') }}"
                             class="text-sky-600 hover:text-sky-700">Masuk</a>
      </div>
    </form>
</div>

{{-- VALIDASI & normalisasi JS --}}
<script>
  const form = document.getElementById('parentRegisterForm');
  const phoneInput = document.getElementById('phone');
  const tokenInput = document.getElementById('token');
  const passInput = document.getElementById('password');
  const confirmInput = document.getElementById('password_confirmation');

  const phoneErr = document.getElementById('phoneError');
  const tokenErr = document.getElementById('tokenError');
  const passErr  = document.getElementById('passwordError');
  const confErr  = document.getElementById('confirmError');

  function resetErr() {
    [phoneInput, tokenInput, passInput, confirmInput].forEach(el => el.classList.remove('border-orange-500'));
    [phoneErr, tokenErr, passErr, confErr].forEach(el => el.classList.add('hidden'));
  }

  form.addEventListener('submit', e => {
    resetErr();

    /* normalisasi no HP */
    phoneInput.value = phoneInput.value.trim()
        .replace(/^\+?62/, '0')
        .replace(/\D/g, '');

    const phoneRegex  = /^08\d{8,11}$/;     // 10-13 digit
    const tokenRegex  = /^[A-Za-z0-9]{8}$/; // 8 alnum
    let ok = true;

    if (!tokenRegex.test(tokenInput.value)) {
      tokenInput.classList.add('border-orange-500'); tokenErr.classList.remove('hidden'); ok = false;
    }

    if (!phoneRegex.test(phoneInput.value)) {
      phoneInput.classList.add('border-orange-500'); phoneErr.classList.remove('hidden'); ok = false;
    }

    if (passInput.value.length < 6) {
      passInput.classList.add('border-orange-500');  passErr.classList.remove('hidden');  ok = false;
    }

    if (passInput.value !== confirmInput.value) {
      confirmInput.classList.add('border-orange-500'); confErr.classList.remove('hidden'); ok = false;
    }

    if (!ok) e.preventDefault(); // blok submit bila ada error
  });
</script>
@endsection
