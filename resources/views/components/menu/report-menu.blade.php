@php
    $mode = 'view';
    $semesterList = [
        ['id' => 1, 'semester' => 'Ganjil', 'year' => '2024/2025', 'timeline' => 'Agu - Des 2024', 'description' => 'Semester ganjil tahun ajaran 2024'],
        ['id' => 2, 'semester' => 'Genap', 'year' => '2024/2025', 'timeline' => 'Jan - Mei 2025', 'description' => 'Semester genap tahun ajaran 2024'],
    ];

    $studentList = [
        ['id' => 1, 'nama' => 'Ahmad Fauzi'],
        ['id' => 2, 'nama' => 'Siti Nurhaliza'],
        ['id' => 3, 'nama' => 'Rizki Ramadhan'],
    ];
@endphp

<div x-data="{ mode: '{{ $mode }}', openDetail: null }" class="flex-1 w-full">
    <!-- VIEW MODE -->
    <div x-show="mode === 'view'" class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 items-start">
                @foreach ($semesterList as $semester)
                    <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl"
                             @click="mode = 'score'">
                        <div class="flex flex-col gap-1 overflow-hidden">
                            <h2 class="text-base font-bold text-sky-800 truncate">
                                Semester {{ $semester['semester'] }} {{ $semester['year'] }}
                            </h2>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $semester['timeline'] }}
                            </p>
                        </div>

                        <button type="button"
                                class="flex items-center gap-1 mt-4 text-xs font-medium text-sky-800 hover:opacity-80 transition-opacity"
                                @click.stop="openDetail === {{ $semester['id'] }} ? openDetail = null : openDetail = {{ $semester['id'] }}">
                            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path class="eye-path" stroke-linecap="round" stroke-linejoin="round"
                                      d="M10 13.1429C12.9338 13.1429 15.5898 11.5357 17.0167 9C15.5898 6.46429 12.9338 4.85714 10 4.85714C7.06618 4.85714 4.41018 6.46429 2.98327 9C4.41018 11.5357 7.06618 13.1429 10 13.1429ZM10 4C13.4967 4 16.5251 6.03429 18 9C16.5251 11.9657 13.4967 14 10 14C6.50327 14 3.47491 11.9657 2 9C3.47491 6.03429 6.50327 4 10 4ZM10 11C10.5401 11 11.058 10.7893 11.4399 10.4142C11.8218 10.0391 12.0364 9.53043 12.0364 9C12.0364 8.46957 11.8218 7.96086 11.4399 7.58579C11.058 7.21071 10.5401 7 10 7C9.45992 7 8.94197 7.21071 8.56007 7.58579C8.17818 7.96086 7.96364 8.46957 7.96364 9C7.96364 9.53043 8.17818 10.0391 8.56007 10.4142C8.94197 10.7893 9.45992 11 10 11Z" />
                            </svg>
                            <span class="toggle-text">Lihat Detail</span>
                        </button>

                        <div x-show="openDetail === {{ $semester['id'] }}" class="mt-4 p-4 border-t border-gray-300 transition-all duration-500 ease-in-out">
                            <p class="text-sm text-gray-700">
                                Detail Jadwal: {{ $semester['description'] ?? 'Tidak ada deskripsi.' }}
                            </p>
                            <div class="flex justify-end gap-4">
                                <button @click="mode = 'add'" class="flex gap-1 items-center text-xs font-medium text-sky-800">Edit</button>
                                <button class="flex gap-1 items-center text-xs font-medium text-red-500">Hapus</button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    <!-- SCORE MODE -->
    <div x-show="mode === 'score'" class="flex-1 w-full">
        <div class="overflow-y-auto hide-scrollbar max-h-[43vh] md:max-h-[42vh]">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-x-6 gap-y-2 items-start">
                <div class="pl-2 flex items-center bg-sky-200 h-[43px] rounded-t-lg">
                    @foreach (['Nama Lengkap', 'Status', 'Pilihan'] as $header)
                        <h3 class="flex-1 text-sm font-medium text-center text-slate-600">
                            {{ $header }}
                        </h3>
                    @endforeach
                </div>

                @foreach ($studentList as $student)
                    <div class="flex items-center px-3 py-1 border border-gray-200">
                        <div class="flex-1 text-sm text-center text-slate-600">{{ $student['nama'] }}</div>
                        <div class="flex-1 text-sm text-center text-slate-600">Belum Dinilai</div>
                        <div class="flex-1 text-sm text-center text-slate-600">
                            <div class="flex flex-col gap-1 items-center">
                                <button class="w-20 text-xs font-medium bg-transparent rounded-lg border border-sky-300 text-slate-600 h-[25px]"
                                        @click="mode = 'add-score'">
                                    Nilai
                                </button>
                                <x-button.delete-button label="Nilai Rapor Ini" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ADD SCORE MODE -->
    <div x-show="mode === 'add-score'" class="flex-1 w-full">
        <x-header.scoring-name-header />

        <!-- Table wrapper (scrollable only for table) -->
        <div class="overflow-x-auto w-full">
            <table class="min-w-full w-full border border-sky-600 rounded-xl overflow-hidden text-xs md:text-sm bg-white">
                <thead class="bg-sky-100 text-sky-800">
                    <tr>
                       <th class="px-1 py-2 border border-sky-200 font-bold text-center w-6" style="width:10px;min-width:10px;max-width:40px;">No</th>
                        <th class="px-3 py-2 border border-sky-200 font-bold text-left">KOMPETENSI DASAR</th>
                        <th class="px-1 py-2 border border-sky-200 font-bold text-center w-8">BM</th>
                        <th class="px-1 py-2 border border-sky-200 font-bold text-center w-8">MM</th>
                        <th class="px-1 py-2 border border-sky-200 font-bold text-center w-8">BSH</th>
                        <th class="px-1 py-2 border border-sky-200 font-bold text-center w-8">BSB</th>
                        <th class="px-3 py-2 border border-sky-200 font-bold text-left min-w-[65vh]">CATATAN GURU</th>
                    </tr>
                </thead>
                <tbody id="reportContainer"></tbody>
            </table>
        </div>

        <!-- Now, OUTSIDE the table wrapper, always at the bottom: -->
        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <table class="w-full table-fixed border text-xs">
                        <tr>
                            <td class="border px-2 py-1">Lingkar Kepala</td>
                            <td class="border px-2 py-1">48 cm</td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Tinggi Badan</td>
                            <td class="border px-2 py-1">95 cm</td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Berat Badan</td>
                            <td class="border px-2 py-1">12.3 kg</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table class="w-full table-fixed border text-xs">
                        <tr>
                            <td class="border px-2 py-1">Sakit</td>
                            <td class="border px-2 py-1">0</td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Izin</td>
                            <td class="border px-2 py-1">0</td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Alpa</td>
                            <td class="border px-2 py-1">0</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4 text-xs text-gray-600">
            <p><strong>Keterangan:</strong></p>
            <p>BM : Belum Muncul</p>
            <p>MM : Mulai Muncul</p>
            <p>BSH : Berkembang Sesuai Harapan</p>
            <p>BSB : Berkembang Sangat Baik</p>
        </div>

        <!-- Pesan Guru & Pesan Orang Tua Area -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Pesan Guru -->
    <div>
        <label class="block text-sm font-semibold text-sky-700 mb-2">Pesan Guru</label>
        <textarea
            class="w-full border border-sky-300 rounded-lg px-3 py-2 text-xs resize-none focus:outline-none focus:ring-2 focus:ring-sky-400"
            rows="4"
            placeholder="Tulis pesan guru di sini..."
            style="min-height:60px;overflow:hidden;"
            oninput="this.style.height='auto';this.style.height=(this.scrollHeight)+'px';"
        ></textarea>
    </div>
    <!-- Pesan Orang Tua + Tanda Tangan -->
    <div class="flex flex-col h-full">
        <label class="block text-sm font-semibold text-sky-700 mb-2">Pesan Orang Tua</label>
        <textarea
            class="w-full border border-sky-300 rounded-lg px-3 py-2 text-xs resize-none focus:outline-none focus:ring-2 focus:ring-sky-400"
            rows="4"
            placeholder="Tulis pesan orang tua di sini..."
            style="min-height:60px;overflow:hidden;"
            oninput="this.style.height='auto';this.style.height=(this.scrollHeight)+'px';"
        ></textarea>
        <div class="flex flex-col items-end mt-4">
            <div class="flex gap-2 text-xs text-gray-600 mb-2">
                <input type="text" class="border-b border-gray-400 px-1 focus:outline-none" placeholder="Nama Kota" style="width:100px;">
                <span>,</span>
                <input type="date" id="tanggal" class="border-b border-gray-400 px-1 focus:outline-none" placeholder="dd-mm-yyyy" style="width:100px;">
            </div>
            <div class="text-xs text-gray-600 mb-8">Tanda Tangan Orang Tua/Wali</div>
            <div class="border-b border-gray-400 w-40 h-6"></div>
        </div>
    </div>
</div>
<!-- Area Tanda Tangan -->
<div class="mt-12 w-full">
  <!-- Tanggal -->
  <div class="flex flex-col items-end mb-4">
    <div class="flex gap-2 text-xs text-gray-600 mb-2">
                <input type="text" class="border-b border-gray-400 px-1 focus:outline-none" placeholder="Nama Kota" style="width:100px;">
                <span>,</span>
                <input type="date" id="tanggal" class="border-b border-gray-400 px-1 focus:outline-none" placeholder="dd-mm-yyyy" style="width:100px;">
            </div>
  </div>

  <!-- Baris Guru -->
  <div class="grid grid-cols-3 gap-4 text-center text-xs mb-12">
    <div>Guru 1</div>
    <div>Guru 2</div>
    <div>Guru 3</div>
  </div>

  <!-- Garis tanda tangan Guru -->
  <div class="grid grid-cols-3 gap-4 text-center text-xs mb-12 pt-8">
    <div class="border-t border-gray-400 mx-4 pt-10"></div>
    <div class="border-t border-gray-400 mx-4 pt-10"></div>
    <div class="border-t border-gray-400 mx-4 pt-10"></div>
  </div>

  <!-- Kepala PAUD di bawah -->
  <div class="flex flex-col items-center text-xs text-center mt-4">
    <div>Mengetahui<br />Kepala PAUD</div>
    <div class="border-t border-gray-400 w-48 pt-10 mt-16"></div>
  </div>
</div>


        <div class="mt-6 flex justify-end">
            <button class="bg-sky-600 text-white px-6 py-2 rounded-full hover:bg-sky-700 transition">
                Simpan
            </button>
        </div>
    </div>

    <!-- ADD DATA MODE -->
    <div x-show="mode === 'add'" class="flex-1 w-full">
        <div class="flex flex-wrap gap-1 md:gap-6 w-full">
            <!-- Kolom Kiri -->
            <div class="flex-1 min-w-[300px]">
                <form class="flex flex-col gap-3.5">
                    <div class="flex flex-col gap-8 max-md:gap-5 max-sm:gap-0">
                        <!-- Form Input Judul -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                <span>Judul</span><span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                placeholder="Judul Jadwal"
                                class="px-4 py-0 h-10 text-sm font-medium text-gray-400 bg-gray-50 rounded-3xl border border-sky-600 w-full"
                            />
                        </div>
                        <!-- Form Tanggal Gambar -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-slate-600 max-sm:text-sm">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="tanggal"
                                placeholder="01 - Januari - 2025"
                                class="px-4 py-0 h-10 text-sm font-medium text-gray-700 bg-white rounded-3xl border border-sky-600 w-full max-sm:text-sm"
                            />
                        </div>
                    </div>
                </form>
            </div>
            <!-- Kolom Kanan -->
            <div class="flex-1 min-w-[300px]">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs text-slate-600 max-sm:text-sm">
                        <span>Deskripsi</span><span class="text-red-500">*</span>
                    </label>
                    <RichTextEditor />
                </div>
                <div class="flex flex-col px-4 py-0 bg-gray-50 rounded-3xl border-sky-600 border-solid border-[1.5px]">
                    <div class="flex gap-3 items-center px-0 py-2.5 border-b border-gray-200">
                        <button class="text-lg text-black font-bold">B</button>
                        <button class="text-lg text-black underline">U</button>
                    </div>
                    <textarea class="p-2.5 text-xs font-medium text-gray-500 h-[80px] bg-transparent resize-none focus:outline-none" placeholder="Masukkan deskripsi...."></textarea>
                </div>
            </div>
        </div>
        <div>
            <x-button.submit-button />
        </div>
    </div>
</div>

<script>
function getData() {
  return [
    {
      number: 1,
      label: "NILAI AGAMA DAN MORAL (NAM)",
      items: [
        {
          kode: "1.1",
          kompetensi: "Berdo’a sebelum dan sesudah melaksanakan kegiatan",
          nilai: { BSH: true },
          catatanText: "Ananda dalam aspek nilai agama dan moral sudah menunjukkan perkembangan yang baik",
          subitems: []
        },
        {
          kode: '1.2',
          kompetensi: 'Menyanyikan lagu-lagu keagamaan',
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: '1.3',
          kompetensi: 'Meniru pelaksanaan kegiatan ibadah secara sederhana',
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: '1.4',
          kompetensi: 'Terlibat dalam kegiatan keagamaan',
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: '1.5',
          kompetensi: 'Membedakan ciptaan-ciptaan Tuhan',
          nilai: { BSB: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: '1.7',
          kompetensi: 'Membiasakan memberi dan menjawab salam',
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: '1.8',
          kompetensi: "Menghafal Do’a sehari-hari",
          nilai: {},
          catatanText: '',
          subitems: [
            { kode: '1.8.1', label: "Do’a sebelum makan", nilai: { BM: true } },
            { kode: '1.8.2', label: "Do’a sesudah makan", nilai: { MM: true } },
            { kode: '1.8.3', label: "Do’a sebelum tidur", nilai: { BM: true } },
            { kode: '1.8.4', label: "Do’a bangun tidur", nilai: { MM: true } },
            { kode: '1.8.5', label: "Do’a untuk kedua orang tua", nilai: { MM: true } }
          ]
        },
        {
          kode: '1.9',
          kompetensi: 'Menghafal Hadist',
          nilai: {},
          catatanText: '',
          subitems: [
            { kode: '1.9.1', label: "Hadist kebersihan", nilai: { BM: true } },
            { kode: '1.9.2', label: "Hadist tidak boleh marah-marah", nilai: { BM: true } }
          ]
        },
        {
          kode: '1.10',
          kompetensi: 'Menghafal surat-surat pendek',
          nilai: {},
          catatanText: '',
          subitems: [
            { kode: '1.10.1', label: "Surat Al-Ikhlas", nilai: { MM: true } },
            { kode: '1.10.2', label: "Surat An-Nas", nilai: { MM: true } }
          ]
        },
        {
          kode: '1.11',
          kompetensi: 'Mengikuti gerakan berwudhu dan sholat',
          nilai: { BSB: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: '1.12',
          kompetensi: 'Menirukan bacaan sholat',
          nilai: { BM: true },
          catatanText: '',
          subitems: []
        }
      ]
    },
    {
      number: 2,
      label: "SOSIAL EMOSIONAL",
      items: [
        {
          kode: "2.1",
          kompetensi: "Membedakan perilaku yang benar-salah dan baik-buruk",
          nilai: { BSH: true },
          catatanText: "Ananda dalam aspek sosial emosional berkembang dengan baik",
          subitems: []
        },
        {
          kode: "2.2",
          kompetensi: "Bersosialisasi dan bermain bersama teman",
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: "2.3",
          kompetensi: "Menghormati orang tua dan menyayangi teman",
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: "2.4",
          kompetensi: "Berani bertanya secara sederhana",
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: "2.5",
          kompetensi: "Melaksanakan kegiatan sendiri sampai selesai",
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: "2.6",
          kompetensi: "Bertanggung jawab atas barang milik pribadinya",
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        },
        {
          kode: "2.7",
          kompetensi: "Berbagi dengan teman, menolong teman, sabar menunggu antrian",
          nilai: { BSH: true },
          catatanText: '',
          subitems: []
        }
      ]
    },
    {
      number: 3,
      label: "FISIK MOTORIK",
      items: [
        {
          kode: "3.1",
          kompetensi: "FISIK MOTORIK KASAR",
          nilai: {},
          catatanText: "",
          subitems: [
            {
              kode: "3.1.1",
              label: "Berjalan diatas papan titian",
              nilai: { BSH: true },
              catatanText: "Dalam motorik kasar Ananda berkembang dengan baik",
            },
            {
              kode: "3.1.2",
              label: "Memantulkan bola sambil berjalan /bergerak",
              nilai: { BSH: true },
              catatanText: "",
            },
            {
              kode: "3.1.3",
              label: "Melempar dan menangkap bola",
              nilai: { BSH: true },
              catatanText: "",
            },
            {
              kode: "3.1.4",
              label: "Berjalan dengan satu kaki",
              nilai: { BSH: true },
              catatanText: "",
            },
            {
              kode: "3.1.5",
              label: "Melompat ke depan dan ke belakang",
              nilai: { BSH: true },
              catatanText: "",
            },
            {
              kode: "3.1.6",
              label: "Mengikuti gerakan senam",
              nilai: { BSH: true },
              catatanText: "",
            },
          ],
        },
        {
          kode: "3.2",
          kompetensi: "FISIK MOTORIK HALUS",
          nilai: {},
          catatanText: "",
          subitems: [
            {
              kode: "3.2.1",
              label: "Life skill: memakai kaos kaki, memakai sepatu, melipat selimut, melipat alat sholat, sikat gigi, membuat susu",
              nilai: { BSH: true },
              catatanText: "Dalam motorik halus Ananda berkembang dengan baik",
            },
            {
              kode: "3.2.2",
              label: "Meniru melipat kertas sederhana (4 lipatan)",
              nilai: { BSH: true },
              catatanText: "",
            },
            {
              kode: "3.2.3",
              label: "Mewarnai sesuai dengan perintah maupun bebas",
              nilai: { BSH: true },
              catatanText: "",
            },
            {
              kode: "3.2.4",
              label: "Merobek kertas, menempel kertas, menarik garis, finger painting secara mandiri",
              nilai: { BSH: true },
              catatanText: "",
            },
          ],
        },
      ],
    },
    {
      number: 4,
      label: "BAHASA",
      items: [
        {
          kode: "4.1",
          kompetensi: "Membedakan dan menirukan kembali bunyi/ suara",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        },
        {
          kode: "4.2",
          kompetensi: "Bercerita sederhana tentang pengalaman pribadi",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        },
        {
          kode: "4.3",
          kompetensi: "Mengutarakan pendapat atau keinginannya",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        },
        {
          kode: "4.4",
          kompetensi: "Mendengarkan dan menceritakan kembali isi cerita",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        },
        {
          kode: "4.5",
          kompetensi: "Memahami perintah dan menjawab pertanyaan",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        }
      ]
    },
    {
      number: 5,
      label: "KOGNITIF",
      items: [
        {
          kode: "5.1",
          kompetensi: "Mengelompokkan benda dengan berbagai cara menurut ciri-ciri tertentu. Misal: menurut warna, bentuk, ukuran, jenis, dll",
          nilai: { BSH: true },
          catatanText: "Ananda dalam aspek kognitif berkembang dengan baik",
          subitems: []
        },
        {
          kode: "5.2",
          kompetensi: "Memasangkan benda sesuai dengan pasangannya, jenisnya, persamaannya, dll",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        },
        {
          kode: "5.3",
          kompetensi: "Mengenal kasar-halus, berat-ringan, banyak-sedikit dan sama-tidak sama",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        },
        {
          kode: "5.4",
          kompetensi: "Mengenal dan menghafal angka 1-5",
          nilai: { BSH: true },
          catatanText: "",
          subitems: []
        }
      ]
    },
    {
      number: 6,
      label: "SENI BUDAYA",
      items: [
    {
      kode: "6.1",
      kompetensi: "Menggambar bebas dengan berbagai media dengan rapi",
      nilai: { BSH: true },
      catatanText: "Ananda aspek seni Ananda mampu mengikuti dengan baik",
      subitems: []
    },
    {
      kode: "6.2",
      kompetensi: "Mewarnai bentuk gambar sedehana dengan rapi",
      nilai: { BSH: true },
      catatanText: "",
      subitems: []
    },
    {
      kode: "6.3",
      kompetensi: "Meronce dengan berbagai bentuk",
      nilai: { BSH: true },
      catatanText: "",
      subitems: []
    },
    {
      kode: "6.4",
      kompetensi: "Melukis dengan jari (Finger Painting)",
      nilai: { BSH: true },
      catatanText: "",
      subitems: []
    },
    {
      kode: "6.5",
      kompetensi: "Melukis dengan cetakan (Stamping)",
      nilai: { BSH: true },
      catatanText: "",
      subitems: []
    },
    {
      kode: "6.6",
      kompetensi: "Menciptakan bentuk bangunan dari balok",
      nilai: { BSH: true },
      catatanText: "",
      subitems: []
    }
  ]
    }
  ];
}



function renderData() {
  const data = getData();
  const container = document.getElementById("reportContainer");
  container.innerHTML = "";

  let isOdd = false;
  data.forEach((section, sectionIdx) => {
    // Special handling for section 3 (FISIK MOTORIK)
    if (section.number === 3) {
      // Section header row (no textarea)
      const sectionRow = document.createElement("tr");
      // No
      const tdNo = document.createElement("td");
      tdNo.className = "px-1 py-2 border border-sky-100 text-center";
      tdNo.style.width = "36px";
      tdNo.style.minWidth = "36px";
      tdNo.style.maxWidth = "40px";
      tdNo.textContent = section.number;
      sectionRow.appendChild(tdNo);
      // Label
      const tdLabel = document.createElement("td");
      tdLabel.className = "px-3 py-2 border border-sky-100 font-bold";
      tdLabel.textContent = section.label;
      sectionRow.appendChild(tdLabel);
      // 4 empty cells for BM, MM, BSH, BSB
      for (let i = 0; i < 4; i++) {
        const tdEmpty = document.createElement("td");
        tdEmpty.className = "px-3 py-2 border border-sky-100";
        sectionRow.appendChild(tdEmpty);
      }
      // No textarea for section 3
      const tdCatatan = document.createElement("td");
      tdCatatan.className = "px-3 py-2 border border-sky-100 bg-sky-50";
      sectionRow.appendChild(tdCatatan);
      container.appendChild(sectionRow);

      // Now render 3.1 and 3.2 as "subsection" rows with textarea
      section.items.forEach((item, itemIdx) => {
        // 3.1 or 3.2 header row with textarea
        const subSectionRow = document.createElement("tr");
        // No
        const tdNo = document.createElement("td");
        tdNo.className = "px-3 py-2 border border-sky-100 font-bold text-sky-700";
        tdNo.textContent = item.kode;
        subSectionRow.appendChild(tdNo);
        // Label
        const tdLabel = document.createElement("td");
        tdLabel.className = "px-3 py-2 border border-sky-100 font-bold";
        tdLabel.textContent = item.kompetensi;
        subSectionRow.appendChild(tdLabel);
        // 4 empty cells for BM, MM, BSH, BSB
        for (let i = 0; i < 4; i++) {
          const tdEmpty = document.createElement("td");
          tdEmpty.className = "px-3 py-2 border border-sky-100";
          subSectionRow.appendChild(tdEmpty);
        }
        // Catatan Guru textarea
        const tdCatatan = document.createElement("td");
        tdCatatan.className = "px-3 py-2 border border-sky-100 bg-sky-50";
        const inputCatatan = document.createElement("textarea");
inputCatatan.rows = 2;
inputCatatan.placeholder = "Catatan Guru untuk " + item.kompetensi;
inputCatatan.value = item.subitems.find(i => i.catatanText)?.catatanText || item.catatanText || "";
inputCatatan.className = "w-full px-2 py-1 border rounded text-xs";
inputCatatan.style.overflow = "hidden";
inputCatatan.style.resize = "none";
inputCatatan.style.minHeight = "40px";
inputCatatan.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});
        // Immediately expand after setting value
        inputCatatan.style.height = 'auto';
        inputCatatan.style.height = (inputCatatan.scrollHeight) + 'px';
        tdCatatan.appendChild(inputCatatan);
        subSectionRow.appendChild(tdCatatan);
        container.appendChild(subSectionRow);

        // Render subitems as normal rows (radio buttons)
        if (item.subitems && item.subitems.length > 0) {
          item.subitems.forEach((sub, subIdx) => {
            addRow(
              container,
              sub.kode,
              sub.label,
              sub.nilai,
              true,
              isOdd,
              `nilai_${item.kode}_${sub.kode}` // unique group for radio
            );
            isOdd = !isOdd;
          });
        }
      });
      return;
    }

    // For other sections (1,2,4,5): header row with textarea
    const sectionRow = document.createElement("tr");
    // No
    const tdNo = document.createElement("td");
    tdNo.className = "px-3 py-2 border border-sky-100 font-bold text-sky-700";
    tdNo.textContent = section.number;
    sectionRow.appendChild(tdNo);
    // Label
    const tdLabel = document.createElement("td");
    tdLabel.className = "px-3 py-2 border border-sky-100 font-bold";
    tdLabel.textContent = section.label;
    sectionRow.appendChild(tdLabel);
    // 4 empty cells for BM, MM, BSH, BSB
    for (let i = 0; i < 4; i++) {
      const tdEmpty = document.createElement("td");
      tdEmpty.className = "px-3 py-2 border border-sky-100";
      sectionRow.appendChild(tdEmpty);
    }
    // Catatan Guru textarea
    const tdCatatan = document.createElement("td");
    tdCatatan.className = "px-3 py-2 border border-sky-100 bg-sky-50";
    const inputCatatan = document.createElement("textarea");
    inputCatatan.rows = 2;
    inputCatatan.placeholder = "Catatan Guru untuk " + section.label;
    let catatanText = "";
    for (const item of section.items) {
      if (item.catatanText) {
        catatanText = item.catatanText;
        break;
      }
    }
    inputCatatan.value = catatanText;
    inputCatatan.className = "w-full px-2 py-1 border rounded text-xs";
inputCatatan.style.overflow = "hidden";
inputCatatan.style.resize = "none";
inputCatatan.style.minHeight = "40px"; // min height agar tidak terlalu kecil
inputCatatan.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});
    // Immediately expand after setting value
    inputCatatan.style.height = 'auto';
    inputCatatan.style.height = (inputCatatan.scrollHeight) + 'px';
    tdCatatan.appendChild(inputCatatan);
    sectionRow.appendChild(tdCatatan);
    container.appendChild(sectionRow);

    // Render item rows (radio buttons)
    section.items.forEach((item, itemIdx) => {
      // Skip 3.1 and 3.2 for section 3 (already handled above)
      if (section.number === 3) return;

      // For section 1, skip checklist for 1.8, 1.9, 1.10 (but render row without checklist)
      if (
        section.number === 1 &&
        (item.kode === "1.8" || item.kode === "1.9" || item.kode === "1.10")
      ) {
        // Render row without checklist columns
        const tr = document.createElement("tr");
        // No
        // No (bold if x.x)
const tdNo = document.createElement("td");
if (/^\d+\.\d+$/.test(item.kode)) {
  tdNo.className = "px-3 py-2 border border-sky-100 text-center font-bold";
} else {
  tdNo.className = "px-3 py-2 border border-sky-100 text-center";
}
tdNo.textContent = item.kode;
tr.appendChild(tdNo);

        // Kompetensi Dasar
        const tdKompetensi = document.createElement("td");
        tdKompetensi.className = "px-3 py-2 border border-sky-100";
        tdKompetensi.textContent = item.kompetensi;
        tr.appendChild(tdKompetensi);

        // 4 empty cells for BM, MM, BSH, BSB
        for (let i = 0; i < 4; i++) {
          const tdEmpty = document.createElement("td");
          tdEmpty.className = "px-1 py-2 border border-sky-100 text-center w-8";
          tr.appendChild(tdEmpty);
        }

        // No Catatan Guru column for item rows
        container.appendChild(tr);

        // Render subitems as usual (with checklist)
        if (item.subitems && item.subitems.length > 0) {
          item.subitems.forEach((sub, subIdx) => {
            addRow(
              container,
              sub.kode,
              sub.label,
              sub.nilai,
              true,
              isOdd,
              `nilai_${item.kode}_${sub.kode}`
            );
            isOdd = !isOdd;
          });
        }
        return; // Skip the rest for this item
      }

      // Default: render as usual
      addRow(
        container,
        item.kode,
        item.kompetensi,
        item.nilai,
        false,
        isOdd,
        `nilai_${section.number}_${item.kode}`
      );
      isOdd = !isOdd;

      // Render subitems if any
      if (item.subitems && item.subitems.length > 0) {
        item.subitems.forEach((sub, subIdx) => {
          addRow(
            container,
            sub.kode,
            sub.label,
            sub.nilai,
            true,
            isOdd,
            `nilai_${item.kode}_${sub.kode}`
          );
          isOdd = !isOdd;
        });
      }
    });
  });
}

// Helper to add a row to the table (radio buttons for single check per row)
function addRow(container, kode, kompetensi, nilai, isSub, isOdd, radioGroup) {
  const tr = document.createElement("tr");
  tr.style.background = isOdd ? "#f8fafc" : "#fff";

  // No
const tdNo = document.createElement("td");
// Bold if kode is x.x (one dot), not x.x.x
if (/^\d+\.\d+$/.test(kode)) {
  tdNo.className = "px-3 py-2 border border-sky-100 text-center font-bold";
} else {
  tdNo.className = "px-3 py-2 border border-sky-100 text-center";
}
tdNo.textContent = kode;
tr.appendChild(tdNo);

  // Kompetensi Dasar
  // Kompetensi Dasar
const tdKompetensi = document.createElement("td");
tdKompetensi.className = "px-3 py-2 border border-sky-100";
tdKompetensi.textContent = kompetensi;
tr.appendChild(tdKompetensi);

  // BM, MM, BSH, BSB as radio buttons (only one can be checked per row)
  ["BM", "MM", "BSH", "BSB"].forEach((key) => {
    const td = document.createElement("td");
    td.className = "px-1 py-2 border border-sky-100 text-center w-8";
    // Custom radio as checklist
    const label = document.createElement("label");
    label.className = "custom-radio-checklist";
    const radio = document.createElement("input");
    radio.type = "radio";
    radio.name = radioGroup || `nilai_${kode}`;
    radio.value = key;
    radio.checked = nilai && nilai[key] ? true : false;
    // Checklist icon (SVG)
    const icon = document.createElement("span");
    icon.className = "check-icon";
    icon.innerHTML = `<svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20"><path fill-rule="evenodd" d="M16.704 6.29a1 1 0 010 1.42l-6.004 6a1 1 0 01-1.416 0l-2.996-3a1 1 0 111.416-1.42l2.288 2.29 5.296-5.29a1 1 0 011.416 0z" clip-rule="evenodd"/></svg>`;
    // Box
    const box = document.createElement("span");
    box.className = "box";
    // Structure: <label><input/><span class="check-icon">...</span><span class="box"></span></label>
    label.appendChild(radio);
    label.appendChild(icon);
    label.appendChild(box);
    td.appendChild(label);
    tr.appendChild(td);
  });

  // No Catatan Guru column for item/subitem rows
  container.appendChild(tr);
}
document.addEventListener('DOMContentLoaded', renderData);
</script>

<style>
/* Custom radio as checklist icon */
.custom-radio-checklist {
  position: relative;
  display: inline-block;
  width: 22px;
  height: 22px;
  cursor: pointer;
}
.custom-radio-checklist input[type="radio"] {
  opacity: 0;
  position: absolute;
  width: 22px;
  height: 22px;
  left: 0;
  top: 0;
  margin: 0;
  cursor: pointer;
}
.custom-radio-checklist .check-icon {
  display: none;
  position: absolute;
  left: 0; top: 0;
  width: 22px; height: 22px;
  color: #0ea5e9; /* sky-500 */
}
.custom-radio-checklist input[type="radio"]:checked + .check-icon {
  display: block;
}
.custom-radio-checklist .box {
  width: 22px; height: 22px;
  border: 2px solid #94a3b8; /* slate-400 */
  border-radius: 6px;
  background: #fff;
  display: block;
}
.custom-radio-checklist input[type="radio"]:checked ~ .box {
  border-color: #0ea5e9;
  background: #e0f2fe;
}

</style>