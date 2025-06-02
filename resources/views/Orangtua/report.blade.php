@extends('layouts.dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- ini dashboard orang tua -->
<main class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
    <div class="flex-1 p-5 max-md:p-2.5 max-sm:p-2.5 overflow-y-auto hide-scrollbar max-h-[100vh] md:max-h-[100vh]" x-data="parentReportApp()">

        {{-- Header Logo --}}
        <header class="flex gap-3 items-center flex-wrap mt-11 md:mt-0">
            <img 
                src="https://cdn.builder.io/api/v1/image/assets/TEMP/7c611c0665bddb8f69e3b35c80f5477a6f0b559e?placeholderIfAbsent=true" 
                alt="PAUD Logo" 
                class="h-12 w-auto max-w-[60px]"
            />
            <div class="flex flex-col">
                <h1 class="text-[24px] md:text-2xl font-bold text-sky-600">PAUD Kartika Pradana</h1>
                <p class="text-[8px] text-sky-800">
                    Taman Penitipan Anak, Kelompok Bermain, dan Taman Kanak-Kanak
                </p>
            </div>
        </header>

        <x-header.parent-breadcrump-header label="Raport" />
        
        <div class="mb-4">
            <a href="{{ url('/dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Container Utama -->
        <div class="flex-1 w-full md:px-10 pt-2">

            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
                <span class="ml-2 text-gray-600">Memuat data raport...</span>
            </div>

            <!-- Error State -->
            <div x-show="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <span x-text="error"></span>
                <div class="mt-2">
                    <button @click="loadReports()" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                        Coba Lagi
                    </button>
                </div>
            </div>

            <!-- No Reports State -->
            <div x-show="!loading && !error && reports.length === 0" class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Raport</h3>
                <p class="text-gray-500 mb-4">Raport untuk anak Anda belum tersedia. Silakan hubungi guru atau sekolah untuk informasi lebih lanjut.</p>
                
                <!-- Link to check children data -->

            </div>

            <!-- Reports Grid -->
            <div x-show="!loading && !error && reports.length > 0" class="w-full overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-y-auto max-h-[500px] md:max-h-[400px] rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full table-auto text-sm text-slate-600">
                            <!-- Header -->
                            <thead class="bg-sky-200 text-sky-800 font-medium">
                                <tr>
                                    <th class="text-center px-4 py-2">Semester</th>
                                    <th class="text-center px-4 py-2">Nama Anak</th>
                                    <th class="text-center px-4 py-2">Kelas</th>
                                    <th class="text-center px-4 py-2">Status</th>
                                    <th class="text-center px-4 py-2">Tanggal Terbit</th>
                                    <th class="text-center px-4 py-2">Aksi</th>
                                </tr>
                            </thead>

                            <!-- Body -->
                            <tbody>
                                <template x-for="report in reports" :key="report.id">
                                    <tr class="border-t border-gray-200 hover:bg-gray-50">
                                        <td class="text-center px-4 py-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" 
                                                  x-text="'Semester ' + (report.template.semester_type === 'ganjil' ? 'Ganjil' : 'Genap')">
                                            </span>
                                        </td>
                                        <td class="text-center px-4 py-2 font-medium text-sky-700" x-text="report.student.name"></td>
                                        <td class="text-center px-4 py-2" x-text="report.classroom.name"></td>
                                        <td class="text-center px-4 py-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Tersedia
                                            </span>
                                        </td>
                                        <td class="text-center px-4 py-2 whitespace-nowrap" x-text="formatDate(report.issued_at)"></td>
                                        <td class="text-center px-4 py-2">
                                            <button @click="downloadPDF(report)" 
                                                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1 px-3 rounded transition-colors duration-200">
                                                <svg class="h-3 w-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                PDF
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Icon Header -->
        <x-header.icon-header />

    </div>

<script>
function parentReportApp() {
    return {
        loading: false,
        error: null,
        reports: [],
        
        async init() {
            await this.loadReports();
        },
        
        async loadReports() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch('/api/parent/reports', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                
                if (!response.ok) {
                    let errorMessage = `Error ${response.status}: ${response.statusText}`;
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        // Could not parse error response
                    }
                    
                    if (response.status === 403) {
                        this.error = 'Akses ditolak. Anda harus login sebagai orang tua.';
                    } else if (response.status === 404) {
                        this.error = 'Endpoint API tidak ditemukan.';
                    } else if (response.status === 500) {
                        this.error = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                    } else {
                        this.error = errorMessage;
                    }
                    return;
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.reports = data.data || [];
                } else {
                    this.error = data.message || 'Gagal memuat data raport';
                }
                
            } catch (error) {
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    this.error = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                } else if (error.name === 'SyntaxError') {
                    this.error = 'Respons server tidak valid. Silakan hubungi administrator.';
                } else {
                    this.error = 'Gagal memuat data raport. Silakan coba lagi.';
                }
            } finally {
                this.loading = false;
            }
        },
        
        viewReport(report) {
            // Redirect to print-ready page
            const url = `/parent/reports/${report.student.id}/${report.template.id}/view`;
            window.open(url, '_blank');
        },
        
        downloadPDF(report) {
            try {
                const pdfUrl = `/api/parent/reports/${report.student.id}/${report.template.id}/pdf`;
                window.open(pdfUrl, '_blank');
            } catch (e) {
                alert('Gagal membuka PDF. Silakan coba lagi.');
            }
        },
        
        formatDate(dateString) {
            if (!dateString) return '-';
            
            const date = new Date(dateString);
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                timeZone: 'Asia/Jakarta'
            };
            
            return date.toLocaleDateString('id-ID', options);
        }
    }
}
</script>
@endsection