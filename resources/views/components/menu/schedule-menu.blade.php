@props(['mode', 'scheduleList', 'class'])

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Include confirmation alert component -->
<x-alert.confirmation-alert />
<x-alert.success-alert />

    <!-- View Data -->
    <div x-show="mode === 'view'" x-cloak class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[63vh] md:max-h-[56vh]">
           <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 items-start">
                
                @foreach ($scheduleList as $schedule)
                
                    <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl" data-schedule-id="{{ $schedule['id'] }}">
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
                            aria-label="Lihat Detail {{ $schedule['title'] }}"
                        >
                            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path class="eye-path" stroke-linecap="round" stroke-linejoin="round" d="M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z" />
                            </svg>
                            <span class="toggle-text">Lihat Detail</span>
                        </button>

                        <div id="detail-{{ $schedule['id'] }}" class="hidden mt-4 p-4 border-t border-gray-300 transition-all duration-500 ease-in-out">
                            <!-- Description Section -->
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-sky-800 mb-2">Deskripsi:</h3>
                                <p class="text-sm text-gray-700">
                                    {{ $schedule['description'] ?? 'Tidak ada deskripsi.' }}
                                </p>
                            </div>

                            <!-- Sub-themes Section -->
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-sky-800 mb-2">Sub Tema:</h3>
                                <div id="sub-schedules-{{ $schedule['id'] }}" class="space-y-2">
                                    <!-- Sub-schedules will be loaded here via AJAX -->
                                    <div class="animate-pulse">
                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-4 mt-4">
                                <button 
                                    onclick="editSchedule({{ $schedule['id'] }}); return false;"
                                    class="flex gap-1 items-center px-3 py-1.5 text-xs font-medium text-sky-800 hover:bg-sky-50 rounded-full transition-colors"
                                >
                                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.25 1.5H6.75C3 1.5 1.5 3 1.5 6.75V11.25C1.5 15 3 16.5 6.75 16.5H11.25C15 16.5 16.5 15 16.5 11.25V9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.0299 2.26495L6.11991 8.17495C5.89491 8.39995 5.66991 8.84245 5.62491 9.16495L5.30241 11.4224C5.18241 12.2399 5.75991 12.8099 6.57741 12.6974L8.83491 12.3749C9.14991 12.3299 9.59241 12.1049 9.82491 11.8799L15.7349 5.96995C16.7549 4.94995 17.2349 3.76495 15.7349 2.26495C14.2349 0.764945 13.0499 1.24495 12.0299 2.26495Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Edit
                                </button>
                                <button 
                                    onclick="deleteSchedule({{ $schedule['id'] }})"
                                    class="flex gap-1 items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-full transition-colors"
                                >
                                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.75 4.48499C13.2525 4.23749 10.74 4.11749 8.235 4.11749C6.75 4.11749 5.265 4.18499 3.78 4.31999L2.25 4.48499" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6.375 3.7275L6.54 2.745C6.66 2.0325 6.75 1.5 8.0175 1.5H9.9825C11.25 1.5 11.3475 2.0625 11.46 2.7525L11.625 3.7275" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14.1375 6.855L13.65 14.4075C13.5675 15.585 13.5 16.5 11.4075 16.5H6.5925C4.5 16.5 4.4325 15.585 4.35 14.4075L3.8625 6.855" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </article>
               
                @endforeach

            </div>
        </div>
    </div>

    <!-- Add Form -->
    <div x-show="mode === 'add'" x-cloak class="flex-1 w-full">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-sky-800">Tambah Jadwal Baru</h3>
            <p class="text-sm text-gray-600">Isi form di bawah untuk menambah jadwal pembelajaran baru</p>
        </div>
        
        <form id="addScheduleForm" class="flex flex-col gap-3.5 ">
            @csrf
            <div class="flex flex-wrap gap-1 md:gap-6 w-full">
                <!-- Left Column -->
                <div class="flex-1 min-w-[300px]">
                    <div class="flex flex-col gap-4 max-md:gap-5 max-sm:gap-0">
                        <!-- Title Input -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Judul Tema</span><span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                placeholder="Masukkan judul tema pembelajaran"
                                class="px-4 py-0 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
                                required
                            />
                        </div>

                        <!-- Description -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Deskripsi</span><span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="description" 
                                class="p-2.5 text-xs font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full resize-none"
                                placeholder="Masukkan deskripsi tema pembelajaran..." 
                                rows="3"
                                required></textarea>
                        </div>
                         <div class="hidden md:flex gap-4 mx-auto mb-2">
                <button 
                    type="button" 
                    @click="mode = 'view'"
                    class="px-4 py-2 text-sky-600 border border-sky-600 font-semibold rounded-full hover:bg-sky-50 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="submitAddSchedule" 
                    onclick="submitAddScheduleForm()"
                    class="px-4 py-2 bg-sky-600 text-white font-semibold rounded-full hover:bg-sky-700 transition-colors"
                >
                    Simpan Jadwal
                </button>
            </div>
                    </div>
                </div>

                <!-- Right Column - Sub Themes -->
                <div class="flex-1 min-w-[300px] overflow-y-auto hide-scrollbar max-h-[50vh] md:max-h-[55vh]">
                    <div class="flex flex-col max-md:gap-5 max-sm:gap-0">
                        <div class=" sticky top-0 flex items-center mb-1 ml-auto md:mr-2">
                            <button type="button" id="addSubThemeAdd" class="px-3 py-1 bg-sky-600 text-white text-xs rounded-full hover:bg-sky-700 transition-colors">
                                + Tambah Sub Tema
                            </button>
                        </div>
                        
                        <div id="addSubThemesContainer" class="space-y-4 max-h overflow-y-auto pr-2 ">
                            <!-- Sub themes will be added here -->
                        </div>
                    </div>
                </div>
            </div>
            
           <div class="flex md:hidden gap-4 mx-auto mt-2">
                <button 
                    type="button" 
                    @click="mode = 'view'"
                    class="px-4 py-2 text-sky-600 border border-sky-600 font-semibold rounded-full hover:bg-sky-50 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="submitAddScheduleMobile"
                    onclick="submitAddScheduleForm()"
                    class="px-4 py-2 bg-sky-600 text-white font-semibold rounded-full hover:bg-sky-700 transition-colors"
                >
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- Edit Form -->
    <div x-show="mode === 'edit'" x-cloak class="flex-1 w-full">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-sky-800">Edit Jadwal</h3>
            <p class="text-sm text-gray-600">Ubah data jadwal pembelajaran yang sudah ada</p>
        </div>
        
        <form id="editScheduleForm" class="flex flex-col gap-3.5 ">
            @csrf
            <input type="hidden" name="schedule_id" id="editScheduleId">
            <div class="flex flex-wrap gap-1 md:gap-6 w-full">
                <!-- Left Column -->
                <div class="flex-1 min-w-[300px]">
                    <div class="flex flex-col gap-4 max-md:gap-5 max-sm:gap-0">
                        <!-- Title Input -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Judul Tema</span><span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                id="editTitle"
                                placeholder="Masukkan judul tema pembelajaran"
                                class="px-4 py-0 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
                                required
                            />
                        </div>

                        <!-- Description -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Deskripsi</span><span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="description" 
                                id="editDescription"
                                class="p-2.5 text-xs font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full resize-none"
                                placeholder="Masukkan deskripsi tema pembelajaran..." 
                                rows="3"
                                required></textarea>
                        </div>
                         <div class="hidden md:flex gap-4 mx-auto mb-2">
                <button 
                    type="button" 
                    @click="mode = 'view'"
                    class="px-4 py-2 text-sky-600 border border-sky-600 font-semibold rounded-full hover:bg-sky-50 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="submitEditSchedule" 
                    onclick="submitEditScheduleForm()"
                    class="px-4 py-2 bg-sky-600 text-white font-semibold rounded-full hover:bg-sky-700 transition-colors"
                >
                    Update Jadwal
                </button>
            </div>
                    </div>
                </div>

                <!-- Right Column - Sub Themes -->
                <div class="flex-1 min-w-[300px] overflow-y-auto hide-scrollbar max-h-[50vh] md:max-h-[55vh]">
                    <div class="flex flex-col max-md:gap-5 max-sm:gap-0">
                        <div class=" sticky top-0 flex items-center mb-1 ml-auto md:mr-2">
                           {{--<label class="text-sm font-medium text-slate-700">Sub Tema</label>--}} 
                            <button type="button" id="addSubThemeEdit" class="px-3 py-1 bg-sky-600 text-white text-xs rounded-full hover:bg-sky-700 transition-colors">
                                + Tambah Sub Tema
                            </button>
                        </div>
                        
                        <div id="editSubThemesContainer" class="space-y-4 max-h overflow-y-auto pr-2 ">
                            <!-- Sub themes will be added here -->
                        </div>
                    </div>
                </div>
            </div>
            
           <div class="flex md:hidden gap-4 mx-auto mt-2">
                <button 
                    type="button" 
                    @click="mode = 'view'"
                    class="px-4 py-2 text-sky-600 border border-sky-600 font-semibold rounded-full hover:bg-sky-50 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="submitEditScheduleMobile"
                    onclick="submitEditScheduleForm()"
                    class="px-4 py-2 bg-sky-600 text-white font-semibold rounded-full hover:bg-sky-700 transition-colors"
                >
                    Update Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sub Theme Template -->
<template id="subThemeTemplate">
  <div class="sub-theme-item border border-sky-600 rounded-3xl p-4 bg-white">
    <div class="flex justify-between items-center mb-3">
      <h4 class="text-sm font-semibold text-slate-700">Sub Tema</h4>
      <button type="button" class="remove-sub-theme text-red-500 text-xs hover:text-red-700 transition-colors">Hapus</button>
    </div>

    <div class="flex flex-col gap-4">
      <!-- Judul Sub Tema -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs text-slate-600">Judul Sub Tema<span class="text-red-500">*</span></label>
        <input
          type="text"
          name="sub_themes[][title]"
          class="px-4 py-2 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
          required
        />
      </div>

      <!-- Tanggal -->
      <div class="grid grid-cols-2 gap-3">
        <div class="flex flex-col gap-1.5">
          <label class="text-xs text-slate-600">Tanggal Mulai<span class="text-red-500">*</span></label>
          <input
            type="date"
            name="sub_themes[][start_date]"
            class="px-4 py-2 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
            required
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-xs text-slate-600">Tanggal Selesai<span class="text-red-500">*</span></label>
          <input
            type="date"
            name="sub_themes[][end_date]"
            class="px-4 py-2 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
            required
          />
        </div>
      </div>

      <!-- Minggu -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs text-slate-600">Minggu ke- (opsional)</label>
        <input
          type="number"
          name="sub_themes[][week]"
          min="1"
          max="52"
          class="px-4 py-2 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full"
        />
      </div>
    </div>
  </div>
</template>


<script>
document.addEventListener('DOMContentLoaded', function() {
    initScheduleForm();
});

// Helper function to format dates
function formatDate(dateString) {
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function initScheduleForm() {
    // Initialize Add Form
    initAddForm();
    // Initialize Edit Form
    initEditForm();
}

function initAddForm() {
    const addButton = document.getElementById('addSubThemeAdd');
    const container = document.getElementById('addSubThemesContainer');
    const template = document.getElementById('subThemeTemplate');
    const form = document.getElementById('addScheduleForm');

    // Define addSubTheme for add form
    window.addSubThemeAdd = function(existingData = null) {
        if (!template || !container) {
            console.error('Required elements not found for add form');
            return;
        }

        const clone = template.content.cloneNode(true);
        const index = container.children.length;
        
        // Add unique names to form fields
        const inputs = clone.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace('[]', `[${index}]`);
                input.setAttribute('name', newName);
                
                // Set existing values if editing
                if (existingData) {
                    if (name.includes('[title]')) {
                        input.value = existingData.title || '';
                    } else if (name.includes('[start_date]')) {
                        input.value = existingData.start_date || '';
                    } else if (name.includes('[end_date]')) {
                        input.value = existingData.end_date || '';
                    } else if (name.includes('[week]')) {
                        input.value = existingData.week || '';
                    }
                }
            }
        });

        // Add remove button handler
        const removeButton = clone.querySelector('.remove-sub-theme');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                if (container.children.length > 1) {
                    this.closest('.sub-theme-item').remove();
                } else {
                    window.dispatchEvent(new CustomEvent('open-confirmation', {
                        detail: {
                            label: 'sub tema terakhir',
                            action: 'menghapus',
                            target: null
                        }
                    }));
                }
            });
        }

        container.appendChild(clone);

        const lastSubTheme = container.lastElementChild;
        if (lastSubTheme) {
            lastSubTheme.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    };

    // Add first sub-theme by default if container is empty
    if (container && container.children.length === 0) {
        window.addSubThemeAdd();
    }

    // Add sub-theme button click handler
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.addSubThemeAdd();
        });
    }

    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAddScheduleForm();
        });
    }
}

function initEditForm() {
    const editButton = document.getElementById('addSubThemeEdit');
    const container = document.getElementById('editSubThemesContainer');
    const template = document.getElementById('subThemeTemplate');
    const form = document.getElementById('editScheduleForm');

    // Define addSubTheme for edit form
    window.addSubThemeEdit = function(existingData = null) {
        if (!template || !container) {
            console.error('Required elements not found for edit form');
            return;
        }

        const clone = template.content.cloneNode(true);
        const index = container.children.length;
        
        // Add unique names to form fields
        const inputs = clone.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace('[]', `[${index}]`);
                input.setAttribute('name', newName);
                
                // Set existing values if editing
                if (existingData) {
                    if (name.includes('[title]')) {
                        input.value = existingData.title || '';
                    } else if (name.includes('[start_date]')) {
                        input.value = existingData.start_date || '';
                    } else if (name.includes('[end_date]')) {
                        input.value = existingData.end_date || '';
                    } else if (name.includes('[week]')) {
                        input.value = existingData.week || '';
                    }
                }
            }
        });

        // Add remove button handler
        const removeButton = clone.querySelector('.remove-sub-theme');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                if (container.children.length > 1) {
                    this.closest('.sub-theme-item').remove();
                } else {
                    window.dispatchEvent(new CustomEvent('open-confirmation', {
                        detail: {
                            label: 'sub tema terakhir',
                            action: 'menghapus',
                            target: null
                        }
                    }));
                }
            });
        }

        container.appendChild(clone);

        const lastSubTheme = container.lastElementChild;
        if (lastSubTheme) {
            lastSubTheme.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    };

    // Add sub-theme button click handler
    if (editButton) {
        editButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.addSubThemeEdit();
        });
    }

    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditScheduleForm();
        });
    }
}

function toggleDetail(id, button) {
    const detailElement = document.getElementById(id);
    const toggleText = button.querySelector('.toggle-text');
    const eyePath = button.querySelector('.eye-path');
    const scheduleId = id.split('-')[1];
    
    if (detailElement.classList.contains('hidden')) {
        detailElement.classList.remove('hidden');
        toggleText.textContent = 'Sembunyikan Detail';
        eyePath.setAttribute('d', 'M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429Z');
        
        // Load sub schedules dan scroll setelah data selesai load
        loadSubSchedules(scheduleId, () => {
            detailElement.scrollIntoView({ behavior: 'smooth', block: 'end' });
        });
    } else {
        detailElement.classList.add('hidden');
        toggleText.textContent = 'Lihat Detail';
        eyePath.setAttribute('d', 'M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z');
    }
}

function loadSubSchedules(scheduleId, callback) {
    const container = document.getElementById(`sub-schedules-${scheduleId}`);
    container.innerHTML = '<div class="animate-pulse"><div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div><div class="h-4 bg-gray-200 rounded w-1/2"></div></div>';

    fetch(`/schedules/${scheduleId}/sub-themes`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = data.sub_themes.map(sub => `
                <div class="p-3 bg-sky-50 border border-sky-600 rounded-2xl">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-sm">${sub.title}</div>
                            <div class="text-xs text-gray-600">
                                ${formatDate(sub.start_date)} - ${formatDate(sub.end_date)}
                                ${sub.week ? `(Minggu ke-${sub.week})` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('') || '<p class="text-sm text-gray-500">Tidak ada sub tema.</p>';

            if (callback) callback();
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-sm text-red-500">Gagal memuat sub tema.</p>';
            if (callback) callback();
        });
}


function editSchedule(id) {
    // Switch to edit mode using Alpine.js
    const component = document.querySelector('[x-data]');
    if (component && component._x_dataStack) {
        component._x_dataStack[0].mode = 'edit';
    }
    
    // Or try direct Alpine method if available
    if (window.Alpine) {
        const alpineComponent = Alpine.$data(component);
        if (alpineComponent) {
            alpineComponent.mode = 'edit';
        }
    }

    // Use a Promise.all approach to get both edit data and sub-themes data
    Promise.all([
        fetch(`/schedules/${id}/edit`).then(response => response.json()),
        fetch(`/schedules/${id}/sub-themes`).then(response => response.json())
    ])
    .then(([editData, subThemesData]) => {
        // Populate main form fields
        const titleInput = document.getElementById('editTitle');
        const descInput = document.getElementById('editDescription');
        const scheduleIdInput = document.getElementById('editScheduleId');
        
        // Get title/description from edit data or fallback to card display
        let displayTitle = editData.title;
        let displayDescription = editData.description;
        
        if (!displayTitle || !displayDescription) {
            const scheduleCard = document.querySelector(`article[data-schedule-id="${id}"]`);
            if (scheduleCard) {
                if (!displayTitle) {
                    const titleElement = scheduleCard.querySelector('h2');
                    displayTitle = titleElement ? titleElement.textContent.trim() : '';
                }
                if (!displayDescription) {
                    // Look for description in the detail section
                    let detailSection = scheduleCard.querySelector(`#detail-${id}`);
                    if (detailSection) {
                        // Make sure detail section is visible to get the description
                        if (detailSection.classList.contains('hidden')) {
                            detailSection.classList.remove('hidden');
                        }
                        const descElement = detailSection.querySelector('div:first-child p');
                        if (descElement && !descElement.textContent.includes('Tidak ada deskripsi')) {
                            displayDescription = descElement.textContent.trim();
                        }
                    }
                }
            }
        }
        
        if (titleInput) {
            titleInput.value = displayTitle || '';
        }
        if (descInput) {
            descInput.value = displayDescription || '';
        }
        if (scheduleIdInput) {
            scheduleIdInput.value = id;
        }
        
        // Clear and populate sub-themes using sub-themes endpoint data (which is working)
        const container = document.getElementById('editSubThemesContainer');
        if (container) {
            container.innerHTML = '';
            
            // Prioritize sub-themes endpoint data since it's working correctly
            const subThemes = subThemesData.sub_themes || editData.sub_themes || [];
            
            if (subThemes && subThemes.length > 0) {
                subThemes.forEach((sub, index) => {
                    window.addSubThemeEdit(sub);
                });
            } else {
                window.addSubThemeEdit();
            }
        }
    })
    .catch(error => {
        console.error('Error loading schedule data:', error);
        
        // Show error alert
        window.dispatchEvent(new CustomEvent('open-success', {
            detail: {
                message: 'Gagal memuat data jadwal: ' + error.message,
                isError: true
            }
        }));
    });
}

function deleteSchedule(id) {
    window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: {
            label: 'jadwal',
            action: 'menghapus',
            target: {
                submit: () => performDeleteSchedule(id)
            }
        }
    }));
}

function submitAddScheduleForm() {
    const form = document.getElementById('addScheduleForm');
    const submitBtn = document.getElementById('submitAddSchedule');
    const submitBtnMobile = document.getElementById('submitAddScheduleMobile');
    
    // Handle both desktop and mobile buttons
    if (submitBtn) {
        submitBtn.textContent = 'Menyimpan...';
        submitBtn.disabled = true;
    }
    if (submitBtnMobile) {
        submitBtnMobile.textContent = 'Menyimpan...';
        submitBtnMobile.disabled = true;
    }

    // Get form data and convert to proper structure
    const formData = new FormData(form);
    const data = {
        title: formData.get('title'),
        description: formData.get('description'),
        sub_themes: []
    };

    // Get all sub-themes from add form
    const subThemes = form.querySelectorAll('.sub-theme-item');
    subThemes.forEach((item, index) => {
        data.sub_themes.push({
            title: formData.get(`sub_themes[${index}][title]`),
            start_date: formData.get(`sub_themes[${index}][start_date]`),
            end_date: formData.get(`sub_themes[${index}][end_date]`),
            week: formData.get(`sub_themes[${index}][week]`)
        });
    });

    // Add classroom ID if available
    const classId = '{{ $class->id ?? 'null' }}';
    if (classId !== 'null') {
        data.classroom_id = classId;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("schedules.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(error => {
                throw new Error(error.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.dispatchEvent(new CustomEvent('open-success', {
                detail: {
                    message: 'Berhasil menyimpan jadwal'
                }
            }));
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Gagal menyimpan jadwal');
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        window.dispatchEvent(new CustomEvent('open-success', {
            detail: {
                message: 'Gagal menyimpan jadwal. Silakan coba lagi.',
                isError: true
            }
        }));
    })
    .finally(() => {
        // Reset button states
        if (submitBtn) {
            submitBtn.textContent = 'Simpan Jadwal';
            submitBtn.disabled = false;
        }
        if (submitBtnMobile) {
            submitBtnMobile.textContent = 'Simpan Jadwal';
            submitBtnMobile.disabled = false;
        }
    });
}

function submitEditScheduleForm() {
    const form = document.getElementById('editScheduleForm');
    const submitBtn = document.getElementById('submitEditSchedule');
    const submitBtnMobile = document.getElementById('submitEditScheduleMobile');
    const scheduleId = document.getElementById('editScheduleId').value;
    
    if (!scheduleId) {
        window.dispatchEvent(new CustomEvent('open-success', {
            detail: {
                message: 'ID jadwal tidak ditemukan. Silakan coba lagi.',
                isError: true
            }
        }));
        return;
    }
    
    // Handle both desktop and mobile buttons
    if (submitBtn) {
        submitBtn.textContent = 'Memperbarui...';
        submitBtn.disabled = true;
    }
    if (submitBtnMobile) {
        submitBtnMobile.textContent = 'Memperbarui...';
        submitBtnMobile.disabled = true;
    }

    // Get form data and convert to proper structure
    const formData = new FormData(form);
    const data = {
        title: formData.get('title'),
        description: formData.get('description'),
        sub_themes: []
    };

    // Get all sub-themes from edit form
    const subThemes = form.querySelectorAll('.sub-theme-item');
    subThemes.forEach((item, index) => {
        data.sub_themes.push({
            title: formData.get(`sub_themes[${index}][title]`),
            start_date: formData.get(`sub_themes[${index}][start_date]`),
            end_date: formData.get(`sub_themes[${index}][end_date]`),
            week: formData.get(`sub_themes[${index}][week]`)
        });
    });

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/schedules/${scheduleId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(error => {
                throw new Error(error.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.dispatchEvent(new CustomEvent('open-success', {
                detail: {
                    message: 'Berhasil memperbarui jadwal'
                }
            }));
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Gagal memperbarui jadwal');
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        window.dispatchEvent(new CustomEvent('open-success', {
            detail: {
                message: 'Gagal memperbarui jadwal. Silakan coba lagi.',
                isError: true
            }
        }));
    })
    .finally(() => {
        // Reset button states
        if (submitBtn) {
            submitBtn.textContent = 'Update Jadwal';
            submitBtn.disabled = false;
        }
        if (submitBtnMobile) {
            submitBtnMobile.textContent = 'Update Jadwal';
            submitBtnMobile.disabled = false;
        }
    });
}

// Ensure performDeleteSchedule is globally available before DOMContentLoaded
function performDeleteSchedule(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const article = document.querySelector(`article[data-schedule-id="${id}"]`);
    if (article) {
        article.style.opacity = '0.5';
        article.style.pointerEvents = 'none';
    }

    fetch(`/schedules/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(error => {
                throw new Error(error.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (article) {
                article.style.transition = 'all 0.3s ease';
                article.style.opacity = '0';
                article.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    article.remove();
                    // Close confirmation modal first
                    window.dispatchEvent(new CustomEvent('close-confirmation'));
                    // Then show success alert
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: {
                                message: 'Berhasil menghapus jadwal'
                            }
                        }));
                    }, 100);
                }, 100);
            }
        } else {
            throw new Error(data.message || 'Gagal menghapus jadwal');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (article) {
            article.style.opacity = '1';
            article.style.pointerEvents = 'auto';
        }
        // Close confirmation modal first
        window.dispatchEvent(new CustomEvent('close-confirmation'));
        // Then show error alert
        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('open-success', {
                detail: {
                    message: 'Gagal menghapus jadwal. Silakan coba lagi.',
                    isError: true
                }
            }));
        }, 100);
    });
}

// Make functions globally available immediately
window.performDeleteSchedule = performDeleteSchedule;
window.deleteSchedule = deleteSchedule;
window.editSchedule = editSchedule;
window.toggleDetail = toggleDetail;
window.formatDate = formatDate;

</script>

