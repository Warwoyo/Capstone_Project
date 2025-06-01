@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center bg-sky-500 rounded-full">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Ubah Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Anda menggunakan password sementara. Silakan buat password baru untuk keamanan akun Anda.
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('teacher.password.change') }}">
            @csrf
            
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">
                    Password Saat Ini (Temp Password)
                </label>
                <input 
                    id="current_password" 
                    name="current_password" 
                    type="password" 
                    required 
                    class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" 
                    placeholder="Masukkan password sementara"
                    value="{{ auth()->user()->temp_password ?? '' }}"
                >
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">
                    Password Baru
                </label>
                <input 
                    id="new_password" 
                    name="new_password" 
                    type="password" 
                    required 
                    minlength="8"
                    class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" 
                    placeholder="Masukkan password baru (min. 8 karakter)"
                >
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">
                    Konfirmasi Password Baru
                </label>
                <input 
                    id="new_password_confirmation" 
                    name="new_password_confirmation" 
                    type="password" 
                    required 
                    minlength="8"
                    class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" 
                    placeholder="Ulangi password baru"
                >
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Persyaratan Password:
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc space-y-1 pl-5">
                                <li>Minimal 8 karakter</li>
                                <li>Kombinasi huruf dan angka direkomendasikan</li>
                                <li>Hindari menggunakan informasi pribadi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-sky-500 group-hover:text-sky-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    
    function validatePasswords() {
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Password tidak sama');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
    }
    
    newPassword.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
});
</script>
@endsection