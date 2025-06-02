{{-- resources/views/kelas/attendance.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Presensi Kelas</title>

    {{-- Tailwind (jika belum dipaketkan) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine + Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link   href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="px-8 py-6 text-slate-700">

@php
    /** @var \App\Models\Student[] $students */
    /** @var \App\Models\Schedule[] $schedules */
    $statuses = ['hadir'=>'Hadir','ijin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha'];
@endphp

<div x-data="attendance({
        classroomId : {{ $classroomId }},
        storeUrl    : '{{ route('attendance.store', $classroomId) }}'
    })"
    x-init="init()"
    class="w-full max-w-5xl mx-auto"
    x-cloak
>
   

    <form x-ref="form" method="POST" :action="storeUrl" class="space-y-4">
        @csrf

        {{-- ① Datepicker --}}
        <div>
            <label class="block text-sm font-bold text-sky-800">Tanggal Presensi</label>
            <input  x-ref="tgl"
                    name="attendance_date"
                    class="mt-2 px-4 h-10 bg-gray-50 rounded-3xl border border-sky-600 w-60 placeholder:text-slate-400"
                    placeholder="Klik untuk pilih tanggal"
                    autocomplete="off"
                    required>
        </div>

        {{-- ② Tema & Keterangan --}}
        <template x-if="hasDate">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
               <div class="flex flex-col gap-1.5">
  <label class="text-xs text-slate-600">
    Tema / Jadwal<span class="text-red-500">*</span>
  </label>
  <select 
    name="schedule_id"
    x-model="selectedSchedule"
    class="px-4 py-2 h-10 text-sm font-medium text-gray-700 bg-white border border-sky-600 rounded-3xl focus:outline-none"
    required
  >
    <option value="" disabled>-- pilih jadwal --</option>
    @foreach ($schedules as $sch)
      <option value="{{ $sch->id }}">{{ $sch->title }}</option>
    @endforeach
  </select>
</div>


                <div class="flex flex-col gap-1.5">
  <label class="text-xs text-slate-600">
    Keterangan<span class="text-red-500">*</span>
  </label>
  <input 
    type="text"
    name="description"
    x-model="selectedDescription"
    placeholder="Masukkan keterangan"
    class="px-4 py-2 h-10 text-sm font-medium text-gray-700 bg-white border border-sky-600 rounded-3xl focus:outline-none"
    required
  >
</div>

        </template>

        {{-- ③ Tabel siswa --}}
        <template x-if="hasDate">
            <div class="w-full overflow-x-auto hide-scrollbar max-h-[60vh] md:max-h-[55vh]">
                <section class="min-w-[100%] mx-auto my-2 rounded-lg border bg-white">
                    <header class="flex font-medium text-sky-800 bg-sky-200 text-sm">
                        <h3 class="flex-1 p-2.5 text-center">Nama Lengkap</h3>
                        <h3 class="flex-1 p-2.5 text-center"></h3>
                        <h3 class="flex-1 p-2.5 text-center">Total Hadir</h3>
                        <h3 class="flex-1 p-2.5 text-center">Persentase</h3>
                    </header>

                    <template x-for="(stu, idx) in students" :key="stu.id">
                        <article class="flex border-b text-sm">
                            <p class="flex-1 px-2.5 py-1.5 text-center" x-text="stu.name"></p>

                            <div class="flex-1 flex justify-center items-center">
                                <input type="hidden" :name="`attendance[${idx}][student_id]`" :value="stu.id">
                                <select :name="`attendance[${idx}][status]`"
                                        x-model="stu.statusToday"
                                        class="text-xs border rounded-md p-1 bg-white">
                                    <option value="" disabled>-- pilih --</option>
                                    @foreach ($statuses as $val=>$lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <p class="flex-1 px-2.5 py-1.5 text-center" x-text="stu.totalPresent"></p>
                            <p class="flex-1 px-2.5 py-1.5 text-center" x-text="stu.percentage"></p>
                        </article>
                    </template>
                </section>
            </div>
        </template>

        {{-- ④ Tombol simpan (selalu satu file!) --}}
        <button type="button"
                x-show="hasDate"
                @click="showConfirm = true"
                class="px-6 py-2 bg-sky-600 hover:bg-sky-700 rounded-full text-white font-semibold">
            Simpan
        </button>
    </form>

    {{-- —— Modal konfirmasi —— --}}
    <div  x-show="showConfirm"
          x-transition.opacity
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl p-6 w-72 text-center space-y-4">
            <h2 class="text-lg font-semibold text-sky-800">Simpan presensi?</h2>
            <p class="text-sm">Pastikan data sudah benar.</p>
            <div class="flex justify-center gap-3">
                <button @click="showConfirm=false"
                        class="px-4 py-1 rounded-md bg-gray-200 text-sm">Batal</button>
                <button @click="submitForm"
                        class="px-4 py-1 rounded-md bg-sky-600 text-white text-sm">Simpan</button>
            </div>
        </div>
    </div>

    {{-- —— Toast —— --}}
    <template x-if="toast.show">
        <div x-transition
             :class="toast.err ? 'bg-red-600' : 'bg-emerald-600'"
             class="fixed bottom-4 right-4 text-white px-4 py-2 rounded-md shadow">
            <p x-text="toast.msg"></p>
        </div>
    </template>
</div>

<script>
function attendance({ classroomId, storeUrl }) {
    return {
        /* ===== data ===== */
        classroomId, storeUrl,
        selectedDate        : '',
        selectedSchedule    : '',
        selectedDescription : '',
        students            : [],
        highlightedDates    : [],
        showConfirm  : false,
        toast        : { show:false, msg:'', err:false },

        /* ===== computed ===== */
        get hasDate() { return this.selectedDate !== '' },

        /* ===== init ===== */
        init() {
            // pre-fetch daftar tanggal yg sudah ada presensi
            fetch(`/kelas/${this.classroomId}/presensi/ajax`)
                .then(r => r.json())
                .then(d => this.highlightedDates = d.highlightedDates || []);

            flatpickr(this.$refs.tgl, {
                dateFormat : 'Y-m-d',
                altInput   : true,
                altFormat  : 'd M Y',
                locale     : 'id',
                onChange   : (_, dStr) => dStr && this.loadDate(dStr),
                onReady    : (_, __, inst) => {
                    // highlight
                    this.highlightedDates.forEach(dt => {
                        const el = inst.calendarContainer.querySelector(`[aria-label="${dt}"]`);
                        el?.classList.add('bg-emerald-200');
                    });
                }
            });
        },

        /* ===== ambil data per tanggal ===== */
        loadDate(dateStr) {
            this.selectedDate = dateStr;
            fetch(`/kelas/${this.classroomId}/presensi/ajax?date=${dateStr}`)
                .then(r => r.json())
                .then(d => {
                    this.selectedSchedule    = d.selectedSchedule    ?? '';
                    this.selectedDescription = d.selectedDescription ?? '';
                    this.students = (d.students || []).map(s => ({
                        id : +s.id,
                        name : s.name,
                        statusToday  : s.statusToday  ?? '',
                        totalPresent : s.totalPresent ?? '-',
                        percentage   : s.percentage   ?? '-'
                    }));
                })
                .catch(() => this.popToast('Gagal memuat data', true));
        },

        /* ===== submit form ===== */
        submitForm() {
            this.showConfirm = false;
            // biar ada feedback cepat
            this.popToast('Menyimpan…');

            // pakai native submit biar backend Laravel tetap happy
            this.$refs.form.requestSubmit();

            // display toast sukses setelah form selesai via turbolinks/hotwire?  
            // fallback sederhana:
            setTimeout(() => this.popToast('Presensi tersimpan!'), 500);
        },

        /* ===== toast helper ===== */
        popToast(msg, err=false) {
            this.toast = { show:true, msg, err };
            setTimeout(() => this.toast.show=false, 2500);
        }
    }
}
</script>
</body>
</html>
