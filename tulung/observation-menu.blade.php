
@props(['mode' => 'view', 'scheduleList' => [], 'class'])

<meta name="csrf-token" content="{{ csrf_token() }}">

<div x-data="{ mode: '{{ $mode }}' }" class="flex-1 w-full">
    
    <!-- Debug information -->
    <div class="mb-2 p-2 bg-gray-100 text-xs">
        Debug: Schedules count: {{ count($scheduleList) }}, 
        Class ID: {{ $class->id ?? 'null' }}
    </div>
    
    <!-- View Schedules for Observation -->
    <div x-show="mode === 'view'" class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
            @if(count($scheduleList) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 items-start">
                    @foreach ($scheduleList as $schedule)
                        <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl cursor-pointer hover:bg-sky-50 transition-colors" 
                                 data-schedule-id="{{ $schedule['id'] }}"
                                 onclick="selectScheduleForObservation({{ $schedule['id'] }})">
                            <div class="flex flex-col gap-1 overflow-hidden">
                                <h2 class="text-base font-bold text-sky-800 truncate">
                                    {{ $schedule['title'] }}
                                </h2>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $schedule['description'] ?? 'Tidak ada deskripsi' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    Dibuat: {{ $schedule['date'] }}
                                </p>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-sky-600 font-medium">
                                    {{ count($schedule['sub_themes'] ?? []) }} Sub Tema
                                </span>
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <!-- Empty state -->
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">Belum Ada Jadwal</h3>
                    <p class="text-sm text-gray-500">Tambahkan jadwal terlebih dahulu untuk melakukan observasi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Select Schedule Detail -->
    <div x-show="mode === 'select-detail'" class="flex-1 w-full">
        <div class="mb-4">
            <button @click="mode = 'view'" class="text-sky-600 text-sm hover:underline">← Kembali ke Jadwal</button>
            <h3 class="text-lg font-bold text-sky-800 mt-2" id="selectedScheduleTitle">Pilih Sub Tema</h3>
        </div>
        
        <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
            <div id="scheduleDetailsContainer" class="space-y-3">
                <!-- Sub themes will be loaded here -->
                <div class="animate-pulse">
                    <div class="h-16 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-16 bg-gray-200 rounded-lg mb-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Scoring -->
    <div x-show="mode === 'scoring'" class="flex-1 w-full">
        <div class="mb-4">
            <button @click="mode = 'select-detail'" class="text-sky-600 text-sm hover:underline">← Kembali ke Sub Tema</button>
            <div class="mt-2">
                <h3 class="text-lg font-bold text-sky-800" id="scoringScheduleTitle">Jadwal</h3>
                <p class="text-sm text-gray-600" id="scoringDetailTitle">Sub Tema</p>
            </div>
        </div>

        <div class="overflow-y-auto hide-scrollbar max-h-[35vh]">
            <div id="studentsContainer" class="space-y-4">
                <!-- Students will be loaded here -->
                <div class="animate-pulse">
                    <div class="h-24 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-24 bg-gray-200 rounded-lg mb-3"></div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex gap-3 justify-end">
            <button @click="mode = 'select-detail'" 
                    class="px-4 py-2 text-sky-600 border border-sky-600 rounded-full hover:bg-sky-50 transition-colors">
                Batal
            </button>
            <button onclick="saveObservationScores()" id="saveScoresBtn"
                    class="px-4 py-2 bg-sky-600 text-white rounded-full hover:bg-sky-700 transition-colors disabled:opacity-50">
                Simpan Nilai
            </button>
        </div>
    </div>
</div>

<script>
let currentScheduleId = null;
let currentDetailId = null;
let observationScores = {};

function selectScheduleForObservation(scheduleId) {
    currentScheduleId = scheduleId;
    
    // Find the schedule data
    const scheduleData = @json($scheduleList).find(s => s.id == scheduleId);
    if (!scheduleData) {
        alert('Data jadwal tidak ditemukan');
        return;
    }

    // Update title
    document.getElementById('selectedScheduleTitle').textContent = scheduleData.title;
    
    // Load schedule details
    loadScheduleDetails(scheduleId);
    
    // Switch to detail selection mode
    if (window.Alpine) {
        window.Alpine.nextTick(() => {
            document.querySelector('[x-data]').__x.$data.mode = 'select-detail';
        });
    }
}

function loadScheduleDetails(scheduleId) {
    const container = document.getElementById('scheduleDetailsContainer');
    container.innerHTML = '<div class="animate-pulse"><div class="h-16 bg-gray-200 rounded-lg mb-3"></div></div>';

    fetch(`/schedules/${scheduleId}/sub-themes`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.sub_themes) {
                container.innerHTML = data.sub_themes.map(detail => `
                    <div class="p-4 bg-white border border-sky-300 rounded-lg cursor-pointer hover:bg-sky-50 transition-colors"
                         onclick="selectScheduleDetail(${detail.id}, '${detail.title}')">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-sky-800">${detail.title}</h4>
                                <p class="text-sm text-gray-600">
                                    ${formatDate(detail.start_date)} - ${formatDate(detail.end_date)}
                                    ${detail.week ? `<span class="ml-2 text-sky-600">Minggu ke-${detail.week}</span>` : ''}
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">Tidak ada sub tema tersedia</p>';
            }
        })
        .catch(error => {
            console.error('Error loading schedule details:', error);
            container.innerHTML = '<p class="text-center text-red-500 py-8">Gagal memuat sub tema</p>';
        });
}

function selectScheduleDetail(detailId, detailTitle) {
    currentDetailId = detailId;
    
    // Update titles
    document.getElementById('scoringDetailTitle').textContent = detailTitle;
    
    // Load students for this class
    loadStudentsForScoring();
    
    // Switch to scoring mode
    if (window.Alpine) {
        window.Alpine.nextTick(() => {
            document.querySelector('[x-data]').__x.$data.mode = 'scoring';
        });
    }
}

function loadStudentsForScoring() {
    const container = document.getElementById('studentsContainer');
    const classId = {{ $class->id ?? 'null' }};
    
    container.innerHTML = '<div class="animate-pulse"><div class="h-24 bg-gray-200 rounded-lg mb-3"></div></div>';

    fetch(`/classroom/${classId}/students-for-observation`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.students) {
                // Initialize scores object
                observationScores = {};
                data.students.forEach(student => {
                    observationScores[student.id] = { score: '', note: '' };
                });

                // Load existing scores
                loadExistingScores(data.students);
            } else {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">Tidak ada siswa tersedia</p>';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            container.innerHTML = '<p class="text-center text-red-500 py-8">Gagal memuat data siswa</p>';
        });
}

function loadExistingScores(students) {
    // First render students with empty scores
    renderStudentScoring(students);
    
    // Then load existing scores
    fetch(`/observations/scores?schedule_detail_id=${currentDetailId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.scores) {
                data.scores.forEach(score => {
                    if (observationScores[score.student_id]) {
                        observationScores[score.student_id] = {
                            score: score.score,
                            note: score.note || ''
                        };
                        
                        // Update form fields
                        const scoreSelect = document.querySelector(`select[data-student-id="${score.student_id}"]`);
                        const noteTextarea = document.querySelector(`textarea[data-student-id="${score.student_id}"]`);
                        
                        if (scoreSelect) scoreSelect.value = score.score;
                        if (noteTextarea) noteTextarea.value = score.note || '';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading existing scores:', error);
        });
}

function renderStudentScoring(students) {
    const container = document.getElementById('studentsContainer');
    
    container.innerHTML = students.map(student => `
        <div class="p-4 bg-white border border-sky-300 rounded-lg">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h4 class="font-medium text-sky-800">${student.name}</h4>
                    <p class="text-sm text-gray-600">${student.student_number}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Nilai:</div>
                    <select data-student-id="${student.id}" 
                            class="mt-1 px-2 py-1 border border-sky-300 rounded text-sm"
                            onchange="updateScore(${student.id}, this.value, 'score')">
                        <option value="">-- Pilih --</option>
                        <option value="1">1 - Belum Berkembang</option>
                        <option value="2">2 - Mulai Berkembang</option>
                        <option value="3">3 - Berkembang Sesuai Harapan</option>
                        <option value="4">4 - Berkembang Sangat Baik</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Catatan:</label>
                <textarea data-student-id="${student.id}"
                          class="w-full px-3 py-2 border border-sky-300 rounded text-sm resize-none"
                          rows="2" 
                          placeholder="Tambahkan catatan observasi..."
                          onchange="updateScore(${student.id}, this.value, 'note')"></textarea>
            </div>
        </div>
    `).join('');
}

function updateScore(studentId, value, type) {
    if (!observationScores[studentId]) {
        observationScores[studentId] = { score: '', note: '' };
    }
    observationScores[studentId][type] = value;
}

function saveObservationScores() {
    const saveBtn = document.getElementById('saveScoresBtn');
    const originalText = saveBtn.textContent;
    
    // Prepare scores data
    const scoresData = [];
    Object.keys(observationScores).forEach(studentId => {
        const scoreData = observationScores[studentId];
        if (scoreData.score) {
            scoresData.push({
                student_id: parseInt(studentId),
                schedule_detail_id: currentDetailId,
                score: parseInt(scoreData.score),
                note: scoreData.note || null
            });
        }
    });

    if (scoresData.length === 0) {
        alert('Harap berikan nilai minimal untuk satu siswa');
        return;
    }

    saveBtn.textContent = 'Menyimpan...';
    saveBtn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/observations/scores', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ scores: scoresData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Nilai berhasil disimpan');
            // Reset to view mode
            if (window.Alpine) {
                window.Alpine.nextTick(() => {
                    document.querySelector('[x-data]').__x.$data.mode = 'view';
                });
            }
        } else {
            alert(data.message || 'Gagal menyimpan nilai');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan nilai');
    })
    .finally(() => {
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// Helper function
function formatDate(dateString) {
    if (!dateString) return '';
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}
</script>