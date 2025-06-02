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
        Masuk ke KANA
      </h1>
    </header>

    {{-- Form --}}
    <form id="loginForm"
          method="POST"
          action="{{ route('login') }}"
          class="mx-auto space-y-2 mt-4 max-w-4xl">
      @csrf

      {{-- Nomor Handphone --}}
      <div>
        <label for="identifier" class="text-sm text-gray-600">Email / No HP</label>
        
        <input
          type="text"
          name="identifier"
          id="identifier"
          placeholder="08... / email"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200 @error('identifier') border-red-500 @enderror"
          value="{{ old('identifier') }}"
          required
        >
        @error('identifier')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
        <p id="phoneError" class="text-sm text-orange-500 mt-1 hidden">Nomor telepon tidak valid.</p>
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="font-poppins block text-sm text-gray-600">Kata Sandi</label>
        <div class="relative">
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Masukkan Kata Sandi"
            class="w-full px-4 py-2 pr-12 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 bg-gray-200 @error('password') border-red-500 @enderror"
            required
          >
          <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-4 transform -translate-y-1 hover:text-gray-600 focus:outline-none">
            <svg id="eye-open" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <svg id="eye-closed" class="w-5 h-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414l-1.414 1.414m4.242 4.242l1.414 1.414M12 2.252A10.05 10.05 0 0118.7 5.25M12 2.252v16.496"></path>
            </svg>
          </button>
        </div>
        @error('password')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
        <p id="passwordError" class="text-sm text-orange-500 mt-1 hidden">Kata sandi tidak boleh kosong.</p>
      </div>

      {{-- Tombol Login --}}
      <div class="text-center">
        <button type="submit"
                class="w-full sm:w-1/2 mx-auto px-4 py-2 mt-4 bg-sky-600
                      text-white font-bold rounded-full hover:bg-sky-700
                      focus:outline-none focus:ring-2 focus:ring-sky-500">
            Login
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
      Belum punya akun orang&nbsp;tua?
      <a href="{{ route('parent.register') }}" class="text-sky-600 hover:text-sky-700">
          Daftar di sini
      </a>

      </div>
    </form>

</div>

{{-- VALIDASI & normalisasi JS --}}
<script>
  const form = document.getElementById('loginForm');
  const identifierInput = document.getElementById('identifier');
  const passwordInput = document.getElementById('password');
  const phoneError = document.getElementById('phoneError');
  const passwordError = document.getElementById('passwordError');

  function resetErrorStyles() {
    identifierInput.classList.remove('border-orange-500');
    phoneError.classList.add('hidden');
    passwordInput.classList.remove('border-orange-500');
    passwordError.classList.add('hidden');
  }

  function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      eyeOpen.classList.add('hidden');
      eyeClosed.classList.remove('hidden');
    } else {
      passwordField.type = 'password';
      eyeOpen.classList.remove('hidden');
      eyeClosed.classList.add('hidden');
    }
  }

  form.addEventListener('submit', function (e) {
    resetErrorStyles();

    const identifier = identifierInput.value.trim();
    
    // Check if it's an email or phone number
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^08\d{8,11}$/;
    
    let isValidIdentifier = false;
    
    if (emailRegex.test(identifier)) {
      // It's an email - no need to normalize
      isValidIdentifier = true;
    } else {
      // It might be a phone number - normalize it
      const normalizedPhone = identifier
        .replace(/^\s+|\s+$/g, '')    // trim
        .replace(/^\+?62/, '0')       // +62 / 62 → 0
        .replace(/\D/g, '');          // buang non-digit
      
      identifierInput.value = normalizedPhone;
      
      if (phoneRegex.test(normalizedPhone)) {
        isValidIdentifier = true;
      }
    }

    let ok = true;

    if (!isValidIdentifier) {
      e.preventDefault();
      identifierInput.classList.add('border-orange-500');
      phoneError.classList.remove('hidden');
      phoneError.textContent = 'Email atau nomor telepon tidak valid.';
      ok = false;
    }

    if (passwordInput.value.trim() === '') {
      e.preventDefault();
      passwordInput.classList.add('border-orange-500');
      passwordError.classList.remove('hidden');
      ok = false;
    }

    /* kalau valid → biarkan form submit ke backend */
  });
</script>

@endsection
