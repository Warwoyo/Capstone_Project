
@props(['mode', 'scheduleList', 'class'])

<meta name="csrf-token" content="{{ csrf_token() }}">

<div x-data="{ mode: @entangle('mode') }" class="flex-1 w-full">
    <!-- View Data -->
    <div x-show="mode === 'view'" class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
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
                                    onclick="editSchedule({{ $schedule['id'] }})"
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

    <!-- Add/Edit Form -->
    <div x-show="mode === 'add' || mode === 'edit'" class="flex-1 w-full">
        <form id="scheduleForm" class="flex flex-col gap-3.5">
            @csrf
            <input type="hidden" name="schedule_id" id="scheduleId">
            <div class="flex flex-wrap gap-1 md:gap-6 w-full">
                <!-- Left Column -->
                <div class="flex-1 min-w-[300px]">
                    <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                        <!-- Title Input -->
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

                        <!-- Description -->
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

                <!-- Right Column - Sub Themes -->
                <div class="flex-1 min-w-[300px]">
                    <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-medium text-slate-700">Sub Tema</label>
                            <button type="button" id="addSubTheme" class="px-3 py-1 bg-sky-600 text-white text-xs rounded-full hover:bg-sky-700 transition-colors">
                                + Tambah Sub Tema
                            </button>
                        </div>
                        
                        <div id="subThemesContainer" class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                            <!-- Sub themes will be added here -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-4 justify-end mt-6">
                <button 
                    type="button" 
                    @click="mode = 'view'"
                    class="px-4 py-2 text-sky-600 border border-sky-600 font-semibold rounded-full hover:bg-sky-50 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    id="submitSchedule" 
                    class="px-4 py-2 bg-sky-600 text-white font-semibold rounded-full hover:bg-sky-700 transition-colors"
                >
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sub Theme Template -->
<template id="subThemeTemplate">
    <div class="sub-theme-item border border-sky-200 rounded-lg p-3 bg-sky-50">
        <div class="flex justify-between mb-2">
            <h4 class="text-sm font-medium text-sky-800">Sub Tema</h4>
            <button type="button" class="remove-sub-theme text-red-500 text-xs hover:text-red-700 transition-colors">Hapus</button>
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
let originalFormHtml = '';
let addSubTheme; // Declare globally

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    if (form) {
        // Store the template element instead of innerHTML
        originalFormHtml = form.cloneNode(true);
    }
    initScheduleForm();
});

function initScheduleForm() {
    const addButton = document.getElementById('addSubTheme');
    const container = document.getElementById('subThemesContainer');
    const template = document.getElementById('subThemeTemplate');
    const form = document.getElementById('scheduleForm');

    // Define addSubTheme as a global function
    addSubTheme = function(existingData = null) {
        if (!template || !container) {
            console.error('Required elements not found');
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
                    const fieldName = name.match(/\[(.*?)\]/)[1];
                    if (existingData[fieldName]) {
                        input.value = existingData[fieldName];
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
                    alert('Minimal harus ada satu sub tema');
                }
            });
        }

        container.appendChild(clone);
    };

    // Add first sub-theme by default if container is empty
    if (container && container.children.length === 0) {
        addSubTheme();
    }

    // Add sub-theme button click handler
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
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
        loadSubSchedules(scheduleId);
    } else {
        detailElement.classList.add('hidden');
        toggleText.textContent = 'Lihat Detail';
        eyePath.setAttribute('d', 'M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11ZM10 11.8571C9.22846 11.8571 8.48852 11.5561 7.94296 11.0203C7.3974 10.4845 7.09091 9.75776 7.09091 9C7.09091 8.24224 7.3974 7.51551 7.94296 6.97969C8.48852 6.44388 9.22846 6.14286 10 6.14286C10.7715 6.14286 11.5115 6.44388 12.057 6.97969C12.6026 7.51551 12.9091 8.24224 12.9091 9C12.9091 9.75776 12.6026 10.4845 12.057 11.0203C11.5115 11.5561 10.7715 11.8571 10 11.8571Z');
    }
}

function loadSubSchedules(scheduleId) {
    const container = document.getElementById(`sub-schedules-${scheduleId}`);
    container.innerHTML = '<div class="animate-pulse"><div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div><div class="h-4 bg-gray-200 rounded w-1/2"></div></div>';

    fetch(`/schedules/${scheduleId}/sub-themes`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = data.sub_themes.map(sub => `
                <div class="p-2 bg-sky-50 rounded border border-sky-100">
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
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-sm text-red-500">Gagal memuat sub tema.</p>';
        });
}

function editSchedule(id) {
    const form = document.getElementById('scheduleForm');
    if (!form) {
        console.error('Form element not found');
        return;
    }

    form.innerHTML = '<div class="animate-pulse"><div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div></div>';

    fetch(`/schedules/${id}/edit`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(error => {
                    throw new Error(error.message || 'Network response was not ok');
                });
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load schedule data');
            }

            // Restore form HTML by cloning the original template
            const newForm = originalFormHtml.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            
            // Re-initialize the form
            initScheduleForm();

            // Get the new form reference after replacement
            const updatedForm = document.getElementById('scheduleForm');

            // Populate form fields
            updatedForm.querySelector('[name="title"]').value = data.title;
            updatedForm.querySelector('[name="description"]').value = data.description;
            updatedForm.querySelector('#scheduleId').value = id;
            
            // Clear and populate sub-themes
            const container = document.getElementById('subThemesContainer');
            if (container) {
                container.innerHTML = '';
                if (data.sub_themes && data.sub_themes.length > 0) {
                    data.sub_themes.forEach(sub => addSubTheme(sub));
                } else {
                    addSubTheme();
                }
            }
            
            // Update Alpine.js mode
            if (window.Alpine) {
                // Use Alpine's nextTick to ensure DOM is ready
                window.Alpine.nextTick(() => {
                    window.Alpine.store('mode', 'edit');
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data jadwal: ' + error.message);
            if (window.Alpine) {
                window.Alpine.store('mode', 'view');
            }
        });
}

function deleteSchedule(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Show loading state
    const article = document.querySelector(`article[data-schedule-id="${id}"]`);
    if (article) {
        article.style.opacity = '0.5';
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
            // Remove the schedule item from DOM with animation
            if (article) {
                article.style.transition = 'all 0.3s ease';
                article.style.opacity = '0';
                article.style.transform = 'scale(0.95)';
                setTimeout(() => article.remove(), 300);
            }
            alert('Jadwal berhasil dihapus');
        } else {
            throw new Error(data.message || 'Gagal menghapus jadwal');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(`Error: ${error.message}`);
        if (article) {
            article.style.opacity = '1';
        }
    });
}

function submitScheduleForm() {
    const form = document.getElementById('scheduleForm');
    const submitBtn = document.getElementById('submitSchedule');
    const originalText = submitBtn.textContent;
    const scheduleId = form.querySelector('#scheduleId').value;
    
    submitBtn.textContent = 'Menyimpan...';
    submitBtn.disabled = true;

    // Get form data and convert to proper structure
    const formData = new FormData(form);
    const data = {
        title: formData.get('title'),
        description: formData.get('description'),
        sub_themes: []
    };

    // Get all sub-themes
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

    // Determine if this is an edit or create operation
    const url = scheduleId ? `/schedules/${scheduleId}` : '{{ route("schedules.store") }}';
    const method = scheduleId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
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
            alert(scheduleId ? 'Jadwal berhasil diperbarui' : 'Jadwal berhasil disimpan');
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal menyimpan jadwal');
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        alert(`Error: ${error.message}`);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Helper function to format dates
function formatDate(dateString) {
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}
</script>