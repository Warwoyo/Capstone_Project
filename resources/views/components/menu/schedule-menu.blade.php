
@props(['mode', 'scheduleList', 'class'])

<div x-data="{ mode: @entangle('mode') }" class="flex-1 w-full">
    <!-- View Data -->
    <div x-show="mode === 'view'" class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 items-start">
                @foreach ($scheduleList as $schedule)
                    <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl">
                        <div class="flex flex-col gap-1 overflow-hidden">
                            <h2 class="text-base font-bold text-sky-800 truncate">
                                {{ $schedule['title'] }}
                            </h2>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $schedule['date'] }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="flex items-center gap-1 mt-4 text-xs font-medium text-sky-800 hover:opacity-80 transition-opacity"
                            onclick="toggleDetail('detail-{{ $schedule['id'] }}', this)"
                            aria-label="Lihat Detail {{ $class['title'] }}"
                        >
                            <!-- Mata tertutup default -->
                            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path class="eye-path" stroke-linecap="round" stroke-linejoin="round" d="M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z" />
                            </svg>
                            <span class="toggle-text">Lihat Detail</span>
                        </button>

                        <div id="detail-{{ $schedule['id'] }}" class="hidden mt-4 p-4 border-t border-gray-300 transition-all duration-500 ease-in-out">
                            <p class="text-sm text-gray-700">
                                Detail Jadwal: {{ $schedule['description'] ?? 'Tidak ada deskripsi.' }}
                            </p>
                            <div class="flex justify-end gap-4">
                                <button @click="mode = 'add'" class="flex gap-1 items-center text-xs font-medium text-sky-800">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.25 1.5H6.75C3 1.5 1.5 3 1.5 6.75V11.25C1.5 15 3 16.5 6.75 16.5H11.25C15 16.5 16.5 15 16.5 11.25V9.75" stroke="#065986" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.0299 2.26495L6.11991 8.17495C5.89491 8.39995 5.66991 8.84245 5.62491 9.16495L5.30241 11.4224C5.18241 12.2399 5.75991 12.8099 6.57741 12.6974L8.83491 12.3749C9.14991 12.3299 9.59241 12.1049 9.82491 11.8799L15.7349 5.96995C16.7549 4.94995 17.2349 3.76495 15.7349 2.26495C14.2349 0.764945 13.0499 1.24495 12.0299 2.26495Z" stroke="#065986" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11.1826 3.11255C11.6851 4.90505 13.0876 6.30755 14.8876 6.81755" stroke="#065986" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Edit
                                </button>
                                <x-button.delete-button label="Jadwal" />
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Data -->
    <div x-show="mode === 'add'" class="flex-1 w-full">
        <form id="scheduleForm" class="flex flex-col gap-3.5">
            @csrf
            <div class="flex flex-wrap gap-1 md:gap-6 w-full">
                <!-- Kolom Kiri -->
                <div class="flex-1 min-w-[300px]">
                    <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                        <!-- Form Input Judul -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Judul Tema</span><span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                placeholder="Judul Tema Utama"
                                class="px-4 py-0 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
                                required
                            />
                        </div>

                        <!-- Deskripsi -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Deskripsi</span><span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="description" 
                                class="p-2.5 text-xs font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full resize-none"
                                placeholder="Masukkan deskripsi tema..." 
                                rows="3"
                                required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan - Sub Tema Section -->
                <div class="flex-1 min-w-[300px]">
                    <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-medium text-slate-700">Sub Tema</label>
                            <button type="button" id="addSubTheme" class="px-3 py-1 bg-sky-600 text-white text-xs rounded-full">
                                + Tambah Sub Tema
                            </button>
                        </div>
                        
                        <div id="subThemesContainer" class="space-y-4 max-h-[300px] overflow-y-auto">
                            <!-- Sub theme items will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" id="submitSchedule" class="w-full px-4 py-2 bg-sky-600 text-white font-semibold rounded-full hover:bg-sky-700">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sub Theme Template (hidden) -->
<template id="subThemeTemplate">
    <div class="sub-theme-item border border-sky-200 rounded-lg p-3 bg-sky-50">
        <div class="flex justify-between mb-2">
            <h4 class="text-sm font-medium text-sky-800">Sub Tema</h4>
            <button type="button" class="remove-sub-theme text-red-500 text-xs">Hapus</button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-slate-600">Judul Sub Tema<span class="text-red-500">*</span></label>
                <input type="text" name="sub_themes[][title]" class="w-full px-3 py-1.5 mt-1 text-sm border border-sky-300 rounded-md" required>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs text-slate-600">Tanggal Mulai<span class="text-red-500">*</span></label>
                    <input type="date" name="sub_themes[][start_date]" class="w-full px-3 py-1.5 mt-1 text-sm border border-sky-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-xs text-slate-600">Tanggal Selesai<span class="text-red-500">*</span></label>
                    <input type="date" name="sub_themes[][end_date]" class="w-full px-3 py-1.5 mt-1 text-sm border border-sky-300 rounded-md" required>
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate-600">Minggu ke- (opsional)</label>
                <input type="number" name="sub_themes[][week]" min="1" max="52" class="w-full px-3 py-1.5 mt-1 text-sm border border-sky-300 rounded-md">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the form when Alpine loads the component
    initScheduleForm();
});

function initScheduleForm() {
    const addButton = document.getElementById('addSubTheme');
    const container = document.getElementById('subThemesContainer');
    const template = document.getElementById('subThemeTemplate');
    const form = document.getElementById('scheduleForm');
    
    // Add first sub-theme by default
    addSubTheme();
    
    // Add sub-theme button click handler
    if (addButton) {
        addButton.addEventListener('click', function() {
            addSubTheme();
        });
    }
    
    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitScheduleForm();
        });
    }
    
    // Function to add a new sub-theme
    function addSubTheme() {
        const newItem = template.content.cloneNode(true);
        container.appendChild(newItem);
        
        // Add event listener to remove button
        const removeButtons = container.querySelectorAll('.remove-sub-theme');
        removeButtons[removeButtons.length - 1].addEventListener('click', function() {
            if (container.children.length > 1) {
                this.closest('.sub-theme-item').remove();
            } else {
                alert('Minimal harus ada satu sub tema');
            }
        });
        
        // Initialize date pickers for the new item if needed
        const dateInputs = container.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            // You can initialize flatpickr here if needed
        });
    }
    
    // Function to submit the form via AJAX
    function submitScheduleForm() {
      // Show loading state
      const submitBtn = document.getElementById('submitSchedule');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Menyimpan...';
      submitBtn.disabled = true;

      // Get form data
      const form = document.getElementById('scheduleForm');
      const formData = new FormData(form);

      // Add classroom ID if available
      const classId = '{{ $class->id ?? 'null' }}';
      if (classId !== 'null') {
          formData.append('classroom_id', classId);
      }

      // Send AJAX request
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
      fetch('{{ route("schedules.store") }}', {
          method: 'POST',
          headers: {
              'X-CSRF-TOKEN': csrfToken
          },
          body: formData
      })
      .then(response => {
          if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
      })
      .then(data => {
          if (data.success) {
              // Show success message
              alert('Jadwal berhasil disimpan');

              // Reset form or redirect
              window.location.reload();
          } else {
              // Show error message
              alert('Error: ' + (data.message || 'Gagal menyimpan jadwal'));
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menyimpan jadwal. Silakan coba lagi.');
      })
      .finally(() => {
          // Reset button state
          submitBtn.textContent = originalText;
          submitBtn.disabled = false;
      });
  }
}

function toggleDetail(id, button) {
    const detailElement = document.getElementById(id);
    const toggleText = button.querySelector('.toggle-text');
    const eyePath = button.querySelector('.eye-path');
    
    if (detailElement.classList.contains('hidden')) {
        detailElement.classList.remove('hidden');
        toggleText.textContent = 'Sembunyikan Detail';
        // Change to eye open SVG path
        eyePath.setAttribute('d', 'M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429Z');
    } else {
        detailElement.classList.add('hidden');
        toggleText.textContent = 'Lihat Detail';
        // Change to eye closed SVG path
        eyePath.setAttribute('d', 'M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z');
    }
}
</script>