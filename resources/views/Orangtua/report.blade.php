@extends('layouts.app')

@section('title', 'Raport Siswa')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="parentReportApp()">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Raport Siswa</h1>
        <p class="text-gray-600">Lihat dan unduh raport untuk anak Anda</p>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">Memuat data raport...</span>
    </div>

    <!-- Error State -->
    <div x-show="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <span x-text="error"></span>
    </div>

    <!-- No Reports State -->
    <div x-show="!loading && !error && reports.length === 0" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
        <div class="text-center py-8">
            <svg class="mx-auto h-16 w-16 text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-yellow-800 mb-2">Belum Ada Raport</h3>
            <p class="text-yellow-700">Raport untuk anak Anda belum tersedia. Silakan hubungi guru atau sekolah untuk informasi lebih lanjut.</p>
        </div>
    </div>

    <!-- Reports Grid -->
    <div x-show="!loading && !error && reports.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="report in reports" :key="report.id">
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 cursor-pointer border-l-4 border-blue-500"
                 @click="viewReport(report)">
                <!-- Card Header -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1" x-text="report.template.title"></h3>
                            <p class="text-sm text-gray-600" x-text="'Semester ' + (report.template.semester_type === 'ganjil' ? 'Ganjil' : 'Genap')"></p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Tersedia
                            </span>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="border-t pt-4">
                        <div class="flex items-center mb-2">
                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm text-gray-700" x-text="report.student.name"></span>
                        </div>
                        <div class="flex items-center mb-2">
                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-6 0H3m2 0h4M9 3v18m6-18v18"></path>
                            </svg>
                            <span class="text-sm text-gray-700" x-text="report.classroom.name"></span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 9v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-700" x-text="formatDate(report.issued_at)"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex space-x-2">
                            <button @click.stop="viewReport(report)" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-md transition-colors duration-200">
                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Lihat
                            </button>
                            <button @click.stop="downloadPDF(report)" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded-md transition-colors duration-200">
                                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
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
                // Get current user's children reports
                const response = await fetch('/api/parent/reports', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                
                if (!response.ok) {
                    if (response.status === 403) {
                        this.error = 'Akses ditolak. Anda harus login sebagai orang tua.';
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return;
                }
                
                const data = await response.json();
                console.log('API Response:', data);
                
                if (data.success) {
                    this.reports = data.data || [];
                    console.log('Loaded reports:', this.reports);
                    
                    // Debug info
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                    }
                    
                    if (this.reports.length === 0) {
                        console.log('No reports found - checking debug info');
                        if (data.debug) {
                            console.log(`Parent ID: ${data.debug.parent_id}`);
                            console.log(`Children count: ${data.debug.children_count}`);
                            console.log(`Children IDs: ${JSON.stringify(data.debug.children_ids)}`);
                            console.log(`Reports count: ${data.debug.reports_count}`);
                            console.log(`Total reports in DB: ${data.debug.total_reports_in_db}`);
                        }
                    }
                } else {
                    this.error = data.message || 'Gagal memuat data raport';
                }
                
            } catch (error) {
                console.error('Error loading reports:', error);
                this.error = 'Gagal memuat data raport. Silakan coba lagi.';
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
            // Redirect to PDF generation endpoint
            const url = `/parent/reports/${report.student.id}/${report.template.id}/pdf`;
            window.open(url, '_blank');
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