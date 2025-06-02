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
          placeholder="081936478393 / email"
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
            <span class="relative w-6 h-6 block">
              <svg id="eye-open" class="absolute inset-0 w-6 h-6 transition-opacity duration-200" style="opacity:1;" xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 256 256">
                <path d="M243.65527,126.37561c-.33886-.7627-8.51172-18.8916-26.82715-37.208-16.957-16.96-46.13281-37.17578-88.82812-37.17578S56.12891,72.20764,39.17188,89.1676c-18.31543,18.31641-26.48829,36.44531-26.82715,37.208a3.9975,3.9975,0,0,0,0,3.249c.33886.7627,8.51269,18.88672,26.82715,37.19922,16.957,16.95606,46.13378,37.168,88.82812,37.168s71.87109-20.21191,88.82812-37.168c18.31446-18.3125,26.48829-36.43652,26.82715-37.19922A3.9975,3.9975,0,0,0,243.65527,126.37561Zm-32.6914,34.999C187.88965,184.34534,159.97656,195.99182,128,195.99182s-59.88965-11.64648-82.96387-34.61719a135.65932,135.65932,0,0,1-24.59277-33.375A135.63241,135.63241,0,0,1,45.03711,94.61584C68.11133,71.64123,96.02344,59.99182,128,59.99182s59.88867,11.64941,82.96289,34.624a135.65273,135.65273,0,0,1,24.59375,33.38379A135.62168,135.62168,0,0,1,210.96387,161.37463ZM128,84.00061a44,44,0,1,0,44,44A44.04978,44.04978,0,0,0,128,84.00061Zm0,80a36,36,0,1,1,36-36A36.04061,36.04061,0,0,1,128,164.00061Z"/>
              </svg>
              <svg id="eye-closed" class="absolute inset-0 w-6 h-6 transition-opacity duration-200 opacity-0" style="opacity:0;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" fill="none">
                <path d="M7 15.5L5.5 17.5M20.5 12.5C19.8612 13.5647 19.041 14.6294 18.0008 15.501M18.0008 15.501C16.5985 16.676 14.7965 17.5 12.5 17.5M18.0008 15.501L18 15.5M18.0008 15.501L19.5 17.5M12.5 17.5C8.5 17.5 6 15 4.5 12.5M12.5 17.5V20M15.5 17L16.5 19.5M9.5 17L8.5 19.5" stroke="#121923" stroke-width="1.2"/>
              </svg>
            </span>
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
    eyeOpen.style.opacity = 0;    // Mata terbuka sembunyi
    eyeClosed.style.opacity = 1;  // Mata tertutup tampil
  } else {
    passwordField.type = 'password';
    eyeOpen.style.opacity = 1;    // Mata terbuka tampil
    eyeClosed.style.opacity = 0;  // Mata tertutup sembunyi
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
