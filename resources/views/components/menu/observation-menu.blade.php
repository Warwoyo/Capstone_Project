
@props(['mode' => 'view', 'scheduleList' => [], 'class'])

<meta name="csrf-token" content="{{ csrf_token() }}">

<div x-data="{ mode: '{{ $mode }}' }" 
     @change-mode.window="mode = $event.detail" 
     class="flex-1 w-full">

    <!-- View Schedules for Observation -->
    <div x-show="mode === 'view'" x-cloak class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[68vh] md:max-h-[64vh]">
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
    <div x-show="mode === 'select-detail'" x-cloak class="flex-1 w-full">
        <div class="mb-2">
            <button @click="mode = 'view'" class="text-sky-600 text-sm hover:underline">← Kembali ke Jadwal</button>
            <h3 class="text-lg font-bold text-sky-800 mt-2" id="selectedScheduleTitle">Pilih Sub Tema</h3>
        </div>
        
        <div class="overflow-y-auto hide-scrollbar max-h-[55vh] md:max-h-[44vh]">
            <div id="scheduleDetailsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                <!-- Sub themes will be loaded here -->
                <div class="animate-pulse">
                    <div class="h-16 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-16 bg-gray-200 rounded-lg mb-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Scoring -->
    <div x-show="mode === 'scoring'" x-cloak class="flex-1 w-full">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-2">
    
    <!-- Tombol kembali -->
    <button @click="mode = 'select-detail'" class="text-sky-600 text-sm hover:underline">
      ← Kembali ke Sub Tema
    </button>

    <!-- Jadwal dan Sub Tema -->
    <div class="flex items-center text-sm font-bold gap-1">
      <h3 class="text-sky-800" id="scoringScheduleTitle">Jadwal</h3>
      <span class="text-gray-400">|</span>
      <p class="text-gray-600" id="scoringDetailTitle">Sub Tema</p>
    </div>

  </div>

        <div class="overflow-y-auto hide-scrollbar max-h-[52vh] md:max-h-[45vh]">
            <div id="studentsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
                <!-- Students will be loaded here -->
                <div class="animate-pulse">
                    <div class="h-24 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-24 bg-gray-200 rounded-lg mb-3"></div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <button @click="mode = 'select-detail'" 
                    class="px-4 py-1 text-sky-600 border border-sky-600 rounded-full hover:bg-sky-50 transition-colors">
                Batal
            </button>
            <button onclick="saveObservationScores()" id="saveScoresBtn"
                    class="px-4 py-1 bg-sky-600 text-white rounded-full hover:bg-sky-700 transition-colors disabled:opacity-50">
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
    
    const scheduleList = @json($scheduleList);
    const scheduleData = scheduleList.find(s => s.id == scheduleId);
    
    if (!scheduleData) {
        alert('Data jadwal tidak ditemukan');
        return;
    }

    document.getElementById('selectedScheduleTitle').textContent = `${scheduleData.title} - Pilih Sub Tema`;
    
    // Load schedule details first
    loadScheduleDetails(scheduleId);
    
    // Switch mode using event dispatch
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('change-mode', { detail: 'select-detail' }));
    }, 200);
}

function loadScheduleDetails(scheduleId) {
    const container = document.getElementById('scheduleDetailsContainer');
    
    // Show loading state
    container.innerHTML = `
        <div class="animate-pulse space-y-3">
            <div class="h-16 bg-gray-200 rounded-lg"></div>
            <div class="h-16 bg-gray-200 rounded-lg"></div>
            <div class="h-16 bg-gray-200 rounded-lg"></div>
        </div>
    `;

    // Fetch sub-themes for the selected schedule
    fetch(`/schedules/${scheduleId}/sub-themes`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        
        if (data.success && data.sub_themes && data.sub_themes.length > 0) {
            const htmlContent = data.sub_themes.map(subTheme => {

                return `
                    <div class="p-4 bg-white border border-sky-300 rounded-lg cursor-pointer hover:bg-sky-50 hover:border-sky-500 transition-all duration-200"
                         onclick="selectScheduleDetail(${subTheme.id}, '${escapeHtml(subTheme.title)}')">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-sky-800 mb-1">${escapeHtml(subTheme.title)}</h4>
                                <p class="text-sm text-gray-600 mb-2">
                                    ${subTheme.description ? escapeHtml(subTheme.description) : 'Tidak ada deskripsi'}
                                </p>
                                <div class="text-xs text-gray-500">
                                    <span>📅 ${formatDate(subTheme.start_date)} - ${formatDate(subTheme.end_date)}</span>
                                    ${subTheme.week ? `<span class="ml-3 px-2 py-1 bg-sky-100 text-sky-700 rounded">Minggu ke-${subTheme.week}</span>` : ''}
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-3">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = htmlContent;
        } else {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">Belum Ada Sub Tema</h3>
                    <p class="text-sm text-gray-500">Sub tema belum dibuat untuk jadwal ini</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading schedule details:', error);
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="w-16 h-16 text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-600 mb-2">Gagal Memuat Data</h3>
                <p class="text-sm text-gray-500 mb-4">Terjadi kesalahan saat memuat sub tema</p>
                <button onclick="loadScheduleDetails(${scheduleId})" 
                        class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                    Coba Lagi
                </button>
            </div>
        `;
    });
}

function selectScheduleDetail(detailId, detailTitle) {
    currentDetailId = detailId;
    
    // Update titles for scoring section
    const scheduleList = @json($scheduleList);
    const scheduleData = scheduleList.find(s => s.id == currentScheduleId);
    
    document.getElementById('scoringScheduleTitle').textContent = scheduleData ? scheduleData.title : 'Jadwal';
    document.getElementById('scoringDetailTitle').textContent = detailTitle;
    
    // Load students for this class
    loadStudentsForScoring();
    
    // Switch to scoring mode
    window.dispatchEvent(new CustomEvent('change-mode', { detail: 'scoring' }));
}

// Pastikan variabel global ada
window.observationScores = window.observationScores || {};
window.observationTexts  = window.observationTexts  || {};

function loadStudentsForScoring() {
  const container = document.getElementById('studentsContainer');
  container.innerHTML = '<p class="text-center">Memuat data …</p>';

  Promise.all([
    fetch(`/schedules/${currentScheduleId}/students`).then(r => r.json()),
    fetch(`/observations/${currentScheduleId}/${currentDetailId}`).then(r => r.json())
  ])
  .then(([studentRes, obsRes]) => {
    if (!studentRes.success) throw new Error('Gagal memuat siswa');

    // Map observasi → { student_id: obsObj }
    const obsMap = {};
    if (obsRes.success) {
      obsRes.observations.forEach(o => { obsMap[o.student_id] = o; });
    }

    // Render kartu siswa
    container.innerHTML = studentRes.students.map(student => {
      const existing   = obsMap[student.id] || {};
      const score      = existing.score ?? null;
      const obsText    = existing.observation_text ?? '';

      // Cache ke memori supaya tombol langsung aktif
      if (score !== null) observationScores[student.id] = score;
      if (obsText)        observationTexts[student.id] = obsText;

      return `
        <div class="w-full max-w-2xl border border-gray-300 rounded-2xl p-1 mb-4">
          <!-- Header -->
          <header class="flex items-center justify-between text-sm text-sky-800 bg-sky-200 h-11 rounded-t-lg px-4">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                <span class="text-sky-600 font-medium text-xs">
                  ${escapeHtml(student.name).charAt(0).toUpperCase()}
                </span>
              </div>
              <div class="text-left">
                <div class="font-medium">${escapeHtml(student.name)}</div>
                <div class="text-xs text-sky-600">NIS: ${escapeHtml(student.student_number ?? 'N/A')}</div>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <span class="text-xs">Nilai:</span>
              <div class="flex space-x-1">
                ${generateScoreButtons(student.id, score)}
              </div>
            </div>
          </header>

          <!-- Body -->
          <div class="flex flex-col gap-2.5 items-start pl-4 pr-4">
            <!-- Editor -->
            <div class="flex flex-col w-full px-4 py-2 bg-gray-50 rounded-3xl border border-sky-600">
              <!-- Toolbar -->
              <div class="flex gap-3 items-center border-b border-gray-200 pb-2">
                <button onclick="formatText('bold', ${student.id})"      class="text-lg font-bold    hover:text-sky-600">B</button>
                <button onclick="formatText('underline', ${student.id})" class="text-lg underline     hover:text-sky-600">U</button>
                <button onclick="formatText('italic', ${student.id})"    class="text-lg italic        hover:text-sky-600">I</button>
              </div>

              <!-- Textarea -->
              <textarea
                id="observation-text-${student.id}"
                class="p-2.5 text-xs font-medium text-gray-700 bg-transparent resize-none focus:outline-none min-h-[80px]"
                placeholder="Tulis hasil observasi untuk ${escapeHtml(student.name)}…"
                onchange="updateObservationText(${student.id})"
              >${escapeHtml(obsText)}</textarea>
            </div>

            <!-- Status -->
            <div class="w-full mt-2">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Status penilaian:</span>
                <span id="score-display-${student.id}"
                      class="font-medium ${score ? 'text-sky-600' : 'text-gray-400'}">
                  ${score ? getScoreLabel(score) : 'Belum dinilai'}
                </span>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    // Aktifkan tombol nilai yg sudah tersimpan
    Object.entries(observationScores)
          .forEach(([sid, sc]) => setStudentScore(sid, sc));
  })
  .catch(err => {
    container.innerHTML = `<p class="text-red-500 text-center">${err.message}</p>`;
  });
}





function generateScoreButtons(studentId, activeScore = null) {
    const scores = [1, 2, 3, 4];
    return scores.map(score => `
        <button
            onclick="setStudentScore(${studentId}, ${score})"
            id="score-btn-${studentId}-${score}"
            class="w-6 h-6 text-xs border rounded-full
                   ${score === activeScore ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-gray-700'}
                   transition-colors focus:outline-none">
            ${score}
        </button>
    `).join('');
}


function setStudentScore(studentId, score) {
    observationScores[studentId] = score;
    
    const scores = [1, 2, 3, 4];
    scores.forEach(s => {
        const btn = document.getElementById(`score-btn-${studentId}-${s}`);
        if (btn) {
            if (s === score) {
                btn.className = 'w-6 h-6 text-xs bg-sky-600 text-white border border-sky-600 rounded-full focus:outline-none focus:ring-1 focus:ring-sky-500';
            } else {
                btn.className = 'w-6 h-6 text-xs border border-gray-300 rounded-full hover:bg-sky-100 hover:border-sky-300 transition-colors focus:outline-none focus:ring-1 focus:ring-sky-500';
            }
        }
    });
    
    const scoreDisplay = document.getElementById(`score-display-${studentId}`);
    if (scoreDisplay) {
        const scoreLabels = {1: 'Kurang', 2: 'Cukup', 3: 'Baik', 4: 'Sangat Baik'};
        scoreDisplay.textContent = `${scoreLabels[score]} (${score})`;
        scoreDisplay.className = 'font-medium text-sky-600';
    }
    
}
function getScoreLabel(score) {
    const scoreLabels = {1: 'Kurang', 2: 'Cukup', 3: 'Baik', 4: 'Sangat Baik'};
    return `${scoreLabels[score]} (${score})`;
}
function saveObservationScores() {
    // Prevent default action if this was triggered by a form or link
    event.preventDefault();
    
    const saveBtn = document.getElementById('saveScoresBtn');
    
    if (Object.keys(observationScores).length === 0) {
        alert('Silakan berikan nilai kepada minimal satu siswa sebelum menyimpan');
        return false;
    }
    
    saveBtn.disabled = true;
    saveBtn.textContent = 'Menyimpan...';
    
    // Prepare observation data including texts
    const observationsData = [];
    Object.keys(observationScores).forEach(studentId => {
        observationsData.push({
            student_id: parseInt(studentId),
            score: observationScores[studentId],
            observation_text: window.observationTexts ? (window.observationTexts[studentId] || '') : ''
        });
    });
    
    const observationData = {
        schedule_id: currentScheduleId,
        schedule_detail_id: currentDetailId,
        observations: observationsData
    };
    
    
    
    // Make sure CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
        saveBtn.disabled = false;
        saveBtn.textContent = 'Simpan Nilai';
        return false;
    }
    
    fetch('/observations/store', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(observationData)
    })
    .then(response => {
        
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        
        if (data.success) {
            alert('Nilai observasi berhasil disimpan!');
            
            // Reset data
            observationScores = {};
            window.observationTexts = {};
            
            // Go back to schedule list
            window.dispatchEvent(new CustomEvent('change-mode', { detail: 'view' }));
        } else {
            throw new Error(data.message || 'Gagal menyimpan nilai');
        }
    })
    .catch(error => {
        console.error('Error saving scores:', error);
        alert('Terjadi kesalahan saat menyimpan nilai: ' + error.message);
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Simpan Nilai';
    });
    
    return false;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return '';
    try {
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    } catch (error) {
        return dateString;
    }
}

function formatText(command, studentId) {
    const textarea = document.getElementById(`observation-text-${studentId}`);
    if (textarea) {
        textarea.focus();
        // Note: execCommand is deprecated, but for basic formatting in textarea we'll use a simple approach
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        
        let formattedText = selectedText;
        switch(command) {
            case 'bold':
                formattedText = `**${selectedText}**`;
                break;
            case 'italic':
                formattedText = `*${selectedText}*`;
                break;
            case 'underline':
                formattedText = `__${selectedText}__`;
                break;
        }
        
        textarea.value = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
        updateObservationText(studentId);
    }
}

function updateObservationText(studentId) {
    const textarea = document.getElementById(`observation-text-${studentId}`);
    if (textarea) {
        if (!window.observationTexts) {
            window.observationTexts = {};
        }
        window.observationTexts[studentId] = textarea.value;
    }
}
</script>