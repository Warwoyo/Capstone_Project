
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

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

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

      {{-- Pilihan Login Method --}}
      <div>
        <label class="block text-sm text-gray-600 mb-2">Pilih metode registrasi</label>
        <div class="flex space-x-4">
          <label class="flex items-center">
            <input type="radio" name="login_method" value="phone" id="phoneMethod" 
                   class="mr-2 text-sky-600 focus:ring-sky-500" checked>
            <span class="text-sm text-gray-700">Nomor Handphone</span>
          </label>
          <label class="flex items-center">
            <input type="radio" name="login_method" value="email" id="emailMethod" 
                   class="mr-2 text-sky-600 focus:ring-sky-500">
            <span class="text-sm text-gray-700">Email</span>
          </label>
        </div>
      </div>

      {{-- Nomor Handphone --}}
      <div id="phoneSection">
        <label for="phone" class="block text-sm text-gray-600">Nomor Handphone</label>
        <input type="tel" name="phone_number" id="phone"
               placeholder="08xxxxxxxxxx"
               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
               value="{{ old('phone_number') }}">
        <p id="phoneError" class="text-sm text-orange-500 mt-1 hidden">Nomor telepon tidak valid.</p>
      </div>

      {{-- Email --}}
      <div id="emailSection" class="hidden">
        <label for="email" class="block text-sm text-gray-600">Email</label>
        <input type="email" name="email" id="email"
               placeholder="contoh@email.com"
               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200"
               value="{{ old('email') }}">
        <p id="emailError" class="text-sm text-orange-500 mt-1 hidden">Format email tidak valid.</p>
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
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mt-3">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
              <div class="mt-2 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
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
  const emailInput = document.getElementById('email');
  const tokenInput = document.getElementById('token');
  const passInput = document.getElementById('password');
  const confirmInput = document.getElementById('password_confirmation');

  const phoneMethod = document.getElementById('phoneMethod');
  const emailMethod = document.getElementById('emailMethod');
  const phoneSection = document.getElementById('phoneSection');
  const emailSection = document.getElementById('emailSection');

  const phoneErr = document.getElementById('phoneError');
  const emailErr = document.getElementById('emailError');
  const tokenErr = document.getElementById('tokenError');
  const passErr  = document.getElementById('passwordError');
  const confErr  = document.getElementById('confirmError');

  // Toggle between phone and email sections
  function toggleLoginMethod() {
    if (phoneMethod.checked) {
      phoneSection.classList.remove('hidden');
      emailSection.classList.add('hidden');
      phoneInput.setAttribute('required', '');
      emailInput.removeAttribute('required');
      emailInput.value = '';
    } else {
      phoneSection.classList.add('hidden');
      emailSection.classList.remove('hidden');
      emailInput.setAttribute('required', '');
      phoneInput.removeAttribute('required');
      phoneInput.value = '';
    }
  }

  phoneMethod.addEventListener('change', toggleLoginMethod);
  emailMethod.addEventListener('change', toggleLoginMethod);

  function resetErr() {
    [phoneInput, emailInput, tokenInput, passInput, confirmInput].forEach(el => el.classList.remove('border-orange-500'));
    [phoneErr, emailErr, tokenErr, passErr, confErr].forEach(el => el.classList.add('hidden'));
  }

  form.addEventListener('submit', e => {
    resetErr();

    const phoneRegex  = /^08\d{8,11}$/;     // 10-13 digit
    const emailRegex  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const tokenRegex  = /^[A-Za-z0-9]{8}$/; // 8 alnum
    let ok = true;

    // Validate token
    if (!tokenRegex.test(tokenInput.value)) {
      tokenInput.classList.add('border-orange-500'); 
      tokenErr.classList.remove('hidden'); 
      ok = false;
    }

    // Validate based on selected method
    if (phoneMethod.checked) {
      // Normalize phone number
      phoneInput.value = phoneInput.value.trim()
          .replace(/^\+?62/, '0')
          .replace(/\D/g, '');

      if (!phoneRegex.test(phoneInput.value)) {
        phoneInput.classList.add('border-orange-500'); 
        phoneErr.classList.remove('hidden'); 
        ok = false;
      }
    } else {
      // Validate email
      if (!emailRegex.test(emailInput.value.trim())) {
        emailInput.classList.add('border-orange-500'); 
        emailErr.classList.remove('hidden'); 
        ok = false;
      }
    }

    // Validate password
    if (passInput.value.length < 6) {
      passInput.classList.add('border-orange-500');  
      passErr.classList.remove('hidden');  
      ok = false;
    }

    // Validate password confirmation
    if (passInput.value !== confirmInput.value) {
      confirmInput.classList.add('border-orange-500'); 
      confErr.classList.remove('hidden'); 
      ok = false;
    }

    if (!ok) e.preventDefault(); // block submit if there are errors
  });

  // Initialize the form
  toggleLoginMethod();
</script>
@endsection