@extends('layouts.dashboard')

@section('content')

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<main x-data="{ mode: 'view', activeTab: 'parents' }" class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
    <section class="flex-1 pt-8 px-8 max-md:p-4 max-sm:p-6">
    <!-- ini bagian pencarian dan tambah kelas -->
    
    <header class="flex gap-3 items-center flex-wrap mt-11 md:mt-0">
    <img 
        src="https://cdn.builder.io/api/v1/image/assets/TEMP/7c611c0665bddb8f69e3b35c80f5477a6f0b559e?placeholderIfAbsent=true" 
        alt="PAUD Logo" 
        class="h-12 w-auto max-w-[60px]"
    />
    <div class="flex flex-col">
        <h1 class="text-[21px] md:text-2xl  font-bold text-sky-600">PAUD Kartika Pradana</h1>
        <p class=" text-[7px] text-sky-800 md:text-[8px]">
            Taman Penitipan Anak, Kelompok Bermain, dan Taman Kanak-Kanak
        </p>
    </div>
</header>



    <!-- Tab Navigation -->
    <div class="flex border-b border-gray-200 mt-6">
        <button 
            @click="activeTab = 'parents'"
            :class="activeTab === 'parents' ? 'border-sky-500 text-sky-600' : 'border-transparent text-gray-500'"
            class="px-6 py-3 border-b-2 font-medium text-sm">
            Manajemen Orang Tua
        </button>
        <button 
            @click="activeTab = 'teachers'"
            :class="activeTab === 'teachers' ? 'border-sky-500 text-sky-600' : 'border-transparent text-gray-500'"
            class="px-6 py-3 border-b-2 font-medium text-sm">
            Manajemen Guru
        </button>
    </div>

    <!-- Parents Management -->
    <div x-show="activeTab === 'parents'" class="flex-1 w-full pt-4">
        <div class="p-1 md:p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Data Orang Tua</h2>
                <button
                    onclick="generateNewToken()"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Generate Token Baru
                </button>
            </div>
        </div>
        
        <div class="overflow-y-auto hide-scrollbar max-h-[60vh] md:max-h-[60vh]">
            <!-- Header -->
            <div class="pl-2 flex items-center bg-sky-200 h-[43px] rounded-t-lg">
                @foreach (['Nama Lengkap', 'Email', 'Status', 'Token', 'Pilihan'] as $header)
                    <h3 class="flex-1 text-sm font-medium text-center text-slate-600 max-md:text-sm max-sm:text-xs">
                        {{ $header }}
                    </h3>
                @endforeach
            </div>

            <!-- Parent Data Rows -->
            @foreach ($parents as $parent)
                <div class="flex items-center px-3 py-2 border border-gray-200">
                    <div class="flex-1 text-sm text-center text-slate-600">{{ $parent['nama'] }}</div>
                    <div class="flex-1 text-sm text-center text-slate-600">{{ $parent['email'] }}</div>
                    <div class="flex-1 text-sm text-center">
                        @php
                            $statusClass = '';
                            switch($parent['status']) {
                                case 'Aktif':
                                    $statusClass = 'bg-green-100 text-green-800';
                                    break;
                                case 'Token Digunakan':
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'Token Belum Digunakan':
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'Belum Registrasi':
                                    $statusClass = 'bg-purple-100 text-purple-800';
                                    break;
                                case 'Perlu Bantuan':
                                    $statusClass = 'bg-red-100 text-red-800';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800';
                            }
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                            {{ $parent['status'] }}
                        </span>
                    </div>
                    <div class="flex-1 text-sm text-center text-slate-600">
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $parent['token'] ?? 'Belum ada' }}</span>
                    </div>
                    <div class="flex-1 text-sm text-center text-slate-600">
                        <div class="flex justify-center items-center">
                            @if(isset($parent['is_unused_token']) && $parent['is_unused_token'])
                                <button
                                    class="w-24 text-xs font-medium bg-red-500 text-white rounded-lg border border-red-300 h-[25px] hover:bg-red-600"
                                    onclick="deleteUnusedToken('{{ $parent['token'] }}')">
                                    Hapus Token
                                </button>
                            @else
                                <button
                                    class="w-24 text-xs font-medium bg-orange-500 text-white rounded-lg border border-orange-300 h-[25px] hover:bg-orange-600"
                                    onclick="resetParentToken({{ $parent['id'] }})">
                                    Reset Token
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Teachers Management -->
    <div x-show="activeTab === 'teachers'" x-cloak class="flex-1 w-full pt-4">
        <div x-show="mode === 'view'">
            <div class="p-1 md:p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Data Guru</h2>
                    <button
                        @click="mode = 'add'"
                        class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
                        Tambah Guru
                    </button>
                </div>
            </div>
            
            <div class="overflow-y-auto hide-scrollbar max-h-[60vh] md:max-h-[60vh]">
                <!-- Header -->
                <div class="pl-2 flex items-center bg-sky-200 h-[43px] rounded-t-lg">
                    @foreach (['Nama Lengkap', 'Email', 'Status', 'Pilihan'] as $header)
                        <h3 class="flex-1 text-sm font-medium text-center text-slate-600 max-md:text-sm max-sm:text-xs">
                            {{ $header }}
                        </h3>
                    @endforeach
                </div>

                <!-- Teacher Data Rows -->
                @foreach ($teachers ?? [] as $teacher)
                    <div class="flex items-center px-3 py-2 border border-gray-200">
                        <div class="flex-1 text-sm text-center text-slate-600">{{ $teacher['nama'] }}</div>
                        <div class="flex-1 text-sm text-center text-slate-600">{{ $teacher['email'] }}</div>
                        <div class="flex-1 text-sm text-center">
                            @php
                                $statusClass = '';
                                if ($teacher['status'] === 'Perlu Bantuan') {
                                    $statusClass = 'bg-red-100 text-red-800';
                                } elseif (str_contains($teacher['status'], 'Temp Pass:')) {
                                    $statusClass = 'bg-orange-100 text-orange-800';
                                } else {
                                    $statusClass = 'bg-green-100 text-green-800';
                                }
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                {{ $teacher['status'] }}
                            </span>
                        </div>
                        <div class="flex-1 text-sm text-center text-slate-600">
                            <div class="flex flex-col md:flex-row gap-2 justify-center items-center">
                                @if(isset($teacher['is_blocked']) && $teacher['is_blocked'])
                                    <button
                                        class="w-20 text-xs font-medium bg-orange-500 text-white rounded-lg border border-orange-300 h-[25px] hover:bg-orange-600"
                                        onclick="resetTeacherPassword({{ $teacher['id'] }})">
                                        Reset Pass
                                    </button>
                                @else
                                    <button
                                        class="w-16 text-xs font-medium bg-transparent rounded-lg border border-sky-300 text-slate-600 h-[25px]"
                                        @click="editTeacher({{ json_encode($teacher) }}); mode = 'edit'">
                                        Edit
                                    </button>
                                    <button
                                        class="w-16 text-xs font-medium bg-yellow-500 text-white rounded-lg border border-yellow-300 h-[25px] hover:bg-yellow-600"
                                        onclick="generateTempPassword({{ $teacher['id'] }})">
                                        Reset
                                    </button>
                                    <button
                                        class="w-16 text-xs font-medium text-white bg-red-500 rounded-lg border border-red-300 h-[25px]"
                                        onclick="deleteTeacher({{ $teacher['id'] }})">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


<!-- Add Teacher Form -->
<div x-show="mode === 'add'" x-cloak class="flex-1 w-full pt-1">
  <article class="grid grid-cols-1 w-full bg-white rounded-lg max-w-[1241px] max-md:grid-cols-1">
    
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Tambah Guru Baru</h2>
        <button @click="mode = 'view'" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Kembali
        </button>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.users.create') }}" class="w-full mx-auto space-y-2 max-w-4xl p-4">
      @csrf
      <input type="hidden" name="role" value="teacher">

      {{-- Nama Lengkap --}}
      <div>
        <label for="name" class="block text-sm text-gray-600">Nama Lengkap</label>
        <input
          type="text"
          name="name"
          id="name"
          value="{{ old('name') }}"
          placeholder="Nama Lengkap Guru"
          class="w-full px-4 py-2 mt-2 border border-sky-500 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
          required
        >
        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Email --}}
      <div>
        <label for="email" class="block text-sm text-gray-600">Email</label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          placeholder="email@example.com"
          class="w-full px-4 py-2 mt-2 border border-sky-500 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
          required
        >
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="block text-sm text-gray-600">Kata Sandi</label>
        <div class="relative">
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Masukkan Kata Sandi"
            class="w-full px-4 py-2 mt-2 pr-12 border border-sky-500 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
            required
            minlength="8"
          >
          <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 mt-1">
            <svg id="eye-password" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <svg id="eye-slash-password" class="w-5 h-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414l-1.414 1.414m4.242 4.242l1.414 1.414M12 2.252A10.05 10.05 0 0118.7 5.25M12 2.252v16.496"></path>
            </svg>
          </button>
        </div>
        @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Konfirmasi Password --}}
      <div>
        <label for="password_confirmation" class="block text-sm text-gray-600">Konfirmasi Kata Sandi</label>
        <div class="relative">
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            placeholder="Konfirmasi Kata Sandi"
            class="w-full px-4 py-2 mt-2 pr-12 border border-sky-500 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
            required
            minlength="8"
          >
          <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 mt-1">
            <svg id="eye-password_confirmation" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <svg id="eye-slash-password_confirmation" class="w-5 h-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414l-1.414 1.414m4.242 4.242l1.414 1.414M12 2.252A10.05 10.05 0 0118.7 5.25M12 2.252v16.496"></path>
            </svg>
          </button>
        </div>
        <div id="password-error" class="text-red-500 text-xs mt-1 hidden"></div>
      </div>

      {{-- Tombol Simpan --}}
      <div class="flex gap-4 pt-4">
        <button type="submit"
          onclick="return validatePasswords()"
          class="flex-1 px-4 py-2 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
          Simpan Guru
        </button>
        <button type="button" @click="mode = 'view'"
          class="flex-1 px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
          Batal
        </button>
      </div>
    </form>
  </article>    </div>

<!-- Edit Teacher Form -->
<div x-show="mode === 'edit'" x-cloak class="flex-1 w-full pt-1">
  <article class="grid grid-cols-1 gap-6 mx-auto w-full bg-white rounded-lg max-w-[1241px] max-md:grid-cols-1">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Edit Guru</h2>
        <button @click="mode = 'view'" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Kembali
        </button>
    </div>

    {{-- Form --}}
    <form method="POST" class="w-full mx-auto space-y-2 max-w-4xl p-4" onsubmit="this.action = `/admin/users/${editingTeacher}`">
      @csrf
      @method('PUT')

      {{-- Nama Lengkap --}}
      <div>
        <label for="edit_name" class="block text-sm text-gray-600">Nama Lengkap</label>
        <input
          type="text"
          name="name"
          id="edit_name"
          placeholder="Nama Lengkap Guru"
          class="w-full px-4 py-2 mt-2 border border-sky-500 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
          required
        >
      </div>

      {{-- Email --}}
      <div>
        <label for="edit_email" class="block text-sm text-gray-600">Email</label>
        <input
          type="email"
          name="email"
          id="edit_email"
          placeholder="email@example.com"
          class="w-full px-4 py-2 mt-2 border border-sky-500 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
          required
        >
      </div>

      {{-- Reset Password Section --}}
      <div class="p-4 bg-gray-50 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Reset Password</h3>
        <p class="text-xs text-gray-600 mb-3">Generate password sementara untuk guru ini</p>
        <button
          type="button"
          onclick="generateTempPasswordForEdit()"
          class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600">
          Generate Password Sementara
        </button>
      </div>

      {{-- Tombol Simpan --}}
      <div class="flex gap-4 pt-4">
        <button type="submit"
          class="flex-1 px-4 py-2 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
          Update Guru
        </button>
        <button type="button" @click="mode = 'view'"
          class="flex-1 px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
          Batal
        </button>
      </div>
    </form>
  </article>
</div>

    <!-- Header Icons -->
    <x-header.icon-header />

    <!-- Custom Confirmation Alert -->
    <x-alert.confirmation-alert />

</main>

<script>
let editingTeacher = null;

function resetParentToken(parentId) {
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/orangtua/${parentId}/reset-token`;
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'token orang tua',
            action: 'mereset',
            target: form
        }
    }));
}

function deleteUnusedToken(token) {
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/orangtua/delete-token/${token}`;
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'token yang belum digunakan',
            action: 'menghapus',
            target: form
        }
    }));
}

function deleteTeacher(teacherId) {
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${teacherId}`;
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'data guru',
            action: 'menghapus',
            target: form
        }
    }));
}

function resetTeacherPassword(teacherId) {
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${teacherId}/reset-password`;
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'password guru',
            action: 'mereset',
            target: form
        }
    }));
}

function generateNewToken() {
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/orangtua/generate-token';
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'token baru',
            action: 'membuat',
            target: form
        }
    }));
}

function editTeacher(teacher) {
    console.log('Editing teacher:', teacher);
    
    // Set the global editingTeacher variable
    editingTeacher = teacher.id;
    
    // Wait for Alpine.js to switch to edit mode, then populate the form
    setTimeout(() => {
        const nameField = document.getElementById('edit_name');
        const emailField = document.getElementById('edit_email');
        
        console.log('Looking for form fields...');
        console.log('Name field found:', !!nameField);
        console.log('Email field found:', !!emailField);
        
        if (nameField && emailField) {
            nameField.value = teacher.nama || '';
            emailField.value = teacher.email || '';
            console.log('Fields populated successfully');
            console.log('Name:', teacher.nama);
            console.log('Email:', teacher.email);
        } else {
            console.error('Form fields not found, retrying...');
            // Retry after another delay
            setTimeout(() => {
                const retryNameField = document.getElementById('edit_name');
                const retryEmailField = document.getElementById('edit_email');
                
                if (retryNameField && retryEmailField) {
                    retryNameField.value = teacher.nama || '';
                    retryEmailField.value = teacher.email || '';
                    console.log('Fields populated on retry');
                } else {
                    console.error('Failed to find form fields after retry');
                }
            }, 200);
        }
    }, 200);
}

function generateTempPassword(teacherId) {
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${teacherId}/generate-temp-password`;
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'password sementara untuk guru',
            action: 'membuat',
            target: form
        }
    }));
}

function generateTempPasswordForEdit() {
    if (!editingTeacher) {
        alert('Tidak ada guru yang sedang diedit');
        return;
    }
    
    // Create hidden form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${editingTeacher}/generate-temp-password`;
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    
    // Show custom confirmation
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'password sementara untuk guru yang sedang diedit',
            action: 'membuat',
            target: form
        }
    }));
}

function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const eyeIcon = document.getElementById(`eye-${fieldId}`);
    const eyeSlashIcon = document.getElementById(`eye-slash-${fieldId}`);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}

function validatePasswords() {
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    const errorDiv = document.getElementById('password-error');
    
    if (password !== passwordConfirmation) {
        errorDiv.textContent = 'Password dan konfirmasi password tidak cocok';
        errorDiv.classList.remove('hidden');
        return false;
    }
    
    if (password.length < 8) {
        errorDiv.textContent = 'Password minimal 8 karakter';
        errorDiv.classList.remove('hidden');
        return false;
    }
    
    errorDiv.classList.add('hidden');
    return true;
}
</script>

@endsection
