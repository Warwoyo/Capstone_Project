
{{-- resources/views/components/menu/report-menu.blade.php --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    /** @var \App\Models\Classroom $class */
    $classId = $class->id;            // kirim via controller
@endphp

{{-- Define the function BEFORE Alpine tries to use it --}}
<script>
/**
 * Komponen Alpine utama
 */
function raporApp(classId){
    return {
        /* --- state --- */
        mode: 'view',              // view | score | form | add
        classId,
        templates: [],
        selectedTemplate: null,
        detailId: null,
        loading: false,
        error: null,

        students: [],
        currentStudent: null,
        tableRows: [],             // flat rows untuk form

        // New template form data
        newTemplateForm: {
            title: '',
            description: '',
            semester_type: '', // 'ganjil' or 'genap'
            themes: []
        },

        csrf(){ 
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.content : '';
        },

        /* --- lifecycle --- */
        async init(){
            try {
                console.log('Initializing raporApp with classId:', this.classId);
                this.loading = true;
                this.error = null;
                this.mode = 'view'; // Ensure mode is set
                await this.loadTemplates();
                this.loading = false;
                console.log('Initialization complete, mode:', this.mode);
            } catch (error) {
                console.error('Error during init:', error);
                this.error = 'Gagal memuat data template';
                this.loading = false;
            }
        },

        /* ---------- TEMPLATE ---------- */
        async loadTemplates(){
            try {
                console.log('Loading templates...');
                const res = await fetch('/rapor/templates', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf()
                    }
                });
                
                if (!res.ok) {
                    if (res.status === 500) {
                        throw new Error('Server error - pastikan TemplateController tersedia');
                    }
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                const data = await res.json();
                this.templates = Array.isArray(data) ? data : [];
                
                // Handle empty data gracefully
                if (this.templates.length === 0) {
                    console.log('No templates found - showing empty state');
                }
                
                console.log('Templates loaded:', this.templates);
            } catch (error) {
                console.error('Error loading templates:', error);
                this.error = error.message.includes('500') ? 
                    'Server error - silakan hubungi administrator' : 
                    'Gagal memuat template';
                throw error;
            }
        },

        async openTemplate(tpl){
            try {
                console.log('Opening template:', tpl);
                this.loading = true;
                this.selectedTemplate = tpl;
                this.mode = 'score';
                await this.loadStudents();
                this.loading = false;
            } catch (error) {
                console.error('Error opening template:', error);
                this.error = 'Gagal memuat data siswa';
                this.loading = false;
            }
        },

        showDetail(tpl){ 
            this.detailId = this.detailId === tpl.id ? null : tpl.id;
        },

        /* Template management */
        newTemplate() {
            console.log('New template clicked');
            this.resetNewTemplateForm();
            this.mode = 'add';
        },

        resetNewTemplateForm() {

          this.newTemplateForm = null;
            // Use nextTick to ensure clean state
            this.$nextTick(() => {
                this.newTemplateForm = {
                    title: '',
                    description: '',
                    semester_type: '',
                    themes: [
                        {
                            code: '',
                            name: '',
                            subThemes: [
                                {
                                    code: '',
                                    name: ''
                                }
                            ]
                        }
                    ]
                };
                console.log('Form reset with themes:', this.newTemplateForm.themes.length);
            });
        },

        // Theme management functions
        addTheme() {
            this.newTemplateForm.themes.push({
                code: '',
                name: '',
                subThemes: [
                    {
                        code: '',
                        name: ''
                    }
                ]
            });
            console.log('Theme added. Total themes:', this.newTemplateForm.themes.length);
        },

        removeTheme(themeIndex) {
            if (this.newTemplateForm.themes.length > 1) {
                this.newTemplateForm.themes.splice(themeIndex, 1);
                console.log('Theme removed. Total themes:', this.newTemplateForm.themes.length);
            }
        },

        addSubTheme(themeIndex) {
            if (this.newTemplateForm.themes[themeIndex]) {
                this.newTemplateForm.themes[themeIndex].subThemes.push({
                    code: '',
                    name: ''
                });
                console.log('Sub-theme added to theme', themeIndex);
            }
        },

        removeSubTheme(themeIndex, subThemeIndex) {
            if (this.newTemplateForm.themes[themeIndex] && 
                this.newTemplateForm.themes[themeIndex].subThemes.length > 1) {
                this.newTemplateForm.themes[themeIndex].subThemes.splice(subThemeIndex, 1);
                console.log('Sub-theme removed from theme', themeIndex);
            }
        },

        async saveNewTemplate() {
            try {
                // Validate required fields
                if (!this.newTemplateForm.title.trim()) {
                    alert('Judul template wajib diisi');
                    return;
                }
                if (!this.newTemplateForm.semester_type) {
                    alert('Jenis semester wajib dipilih');
                    return;
                }

                // Validate themes
                for (let i = 0; i < this.newTemplateForm.themes.length; i++) {
                    const theme = this.newTemplateForm.themes[i];
                    if (!theme.code.trim()) {
                        alert(`Kode tema ${i + 1} wajib diisi`);
                        return;
                    }
                    if (!theme.name.trim()) {
                        alert(`Nama tema ${i + 1} wajib diisi`);
                        return;
                    }

                    // Validate sub-themes
                    for (let j = 0; j < theme.subThemes.length; j++) {
                        const subTheme = theme.subThemes[j];
                        if (!subTheme.code.trim()) {
                            alert(`Kode sub-tema ${j + 1} pada tema "${theme.name}" wajib diisi`);
                            return;
                        }
                        if (!subTheme.name.trim()) {
                            alert(`Nama sub-tema ${j + 1} pada tema "${theme.name}" wajib diisi`);
                            return;
                        }
                    }
                }

                this.loading = true;
                
                const res = await fetch('/rapor/templates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newTemplateForm)
                });

                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }

                await this.loadTemplates();
                this.mode = 'view';
                this.resetNewTemplateForm();
                alert('Template berhasil dibuat!');
                this.loading = false;
            } catch (error) {
                console.error('Error saving template:', error);
                alert('Gagal menyimpan template');
                this.loading = false;
            }
        },

        // ...existing code for other methods...
    }
}
</script>

<style>
.custom-radio-checklist{position:relative;display:inline-block;width:22px;height:22px;cursor:pointer}
.custom-radio-checklist input{opacity:0;position:absolute;width:22px;height:22px;left:0;top:0;margin:0;cursor:pointer}
.custom-radio-checklist .check-icon{display:none;position:absolute;left:4px;top:0;font-size:18px;color:#0ea5e9}
.custom-radio-checklist .box{width:22px;height:22px;border:2px solid #94a3b8;border-radius:6px;background:#fff;display:block}
.custom-radio-checklist input:checked + .check-icon{display:block}
.custom-radio-checklist input:checked ~ .box{border-color:#0ea5e9;background:#e0f2fe}
</style>

{{-- ROOT COMPONENT --}}
<div x-data="raporApp({{ $class->id }})" x-init="init()" class="p-4">

    {{-- STATE DEBUG (hidden) --}}
    <template x-if="false">
        <div x-text="JSON.stringify({mode,loading,error})"></div>
    </template>

    {{-- LOADING --}}
    <div x-show="loading" class="flex justify-center items-center h-40 text-sky-600">Memuat…</div>

    {{-- ERROR --}}
    <div x-show="!loading && error" class="bg-red-50 border border-red-200 p-4 rounded-lg">
        <div class="text-red-600" x-text="error"></div>
        <button @click="init()" class="mt-2 px-3 py-1 bg-red-600 text-white rounded text-sm">Coba lagi</button>
    </div>

    {{-- ===================== TEMPLATE LIST ===================== --}}
    <div x-show="!loading && !error && mode==='view'" class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">Daftar Template Rapor</h1>
            
            {{-- Add Template Button --}}
            <button class="flex items-center gap-2 bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700" 
                    @click="newTemplate()">
                <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                    <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                </svg>
                Tambah Template
            </button>
        </div>

        {{-- Empty State --}}
        <div x-show="templates.length === 0" class="text-center py-8 text-gray-500">
            <p>Data template masih kosong</p>
            <p class="text-sm mt-2">Silakan buat template baru untuk memulai</p>
        </div>

        {{-- Templates Grid --}}
        <div x-show="templates.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- CARD TEMPLATE --}}
            <template x-for="tpl in templates" :key="tpl.id">
                <article class="p-4 bg-white border border-sky-500 rounded-xl flex flex-col justify-between cursor-pointer hover:shadow"
                         @click="openTemplate(tpl)">
                    <div>
                        <h2 class="font-bold text-sky-800 truncate" x-text="tpl.title || 'Template Tanpa Judul'"></h2>
                        <p class="text-sm text-gray-500" x-text="tpl.semester_type ? `Semester ${tpl.semester_type}` : 'Tidak ada semester'"></p>
                    </div>
                    <button type="button" class="self-end mt-3 text-xs text-sky-800" @click.stop="showDetail(tpl)">
                        Detail
                    </button>
                    {{-- DETAIL PANEL --}}
                    <div x-show="detailId===tpl.id" x-collapse class="mt-3 border-t pt-3 text-sm text-gray-700">
                        <p x-text="tpl.description||'Tidak ada deskripsi.'"></p>
                        <div class="mt-2 text-xs space-y-1">
                            <div><strong>Tema:</strong> <span x-text="tpl.theme_code + ' - ' + tpl.theme_name"></span></div>
                            <div><strong>Sub-tema:</strong> <span x-text="tpl.sub_theme_code + ' - ' + tpl.sub_theme_name"></span></div>
                        </div>
                        <div class="flex justify-end gap-3 mt-2 text-xs">
                            <button class="text-red-600" @click="deleteTemplate(tpl.id)">Hapus</button>
                            <button class="text-emerald-600" @click="assignTemplate(tpl.id)">Tetapkan ⇢ Kelas</button>
                        </div>
                    </div>
                </article>
            </template>
        </div>
    </div>

    {{-- ===================== ADD TEMPLATE FORM ===================== --}}
    <div x-show="!loading && !error && mode==='add'" class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">Tambah Template Rapor</h1>
        </div>
        
        <div class="bg-white border border-sky-200 rounded-lg p-6 space-y-6">
            {{-- Title Field --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Template <span class="text-red-500">*</span></label>
                <input type="text" 
                       x-model="newTemplateForm.title"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                       placeholder="Masukkan judul template">
            </div>

            {{-- Description Field --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea x-model="newTemplateForm.description"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                          placeholder="Masukkan deskripsi template (opsional)"></textarea>
            </div>

            {{-- Semester Type Field --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Semester <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" x-model="newTemplateForm.semester_type" value="ganjil" class="mr-2">
                        <span>Ganjil</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" x-model="newTemplateForm.semester_type" value="genap" class="mr-2">
                        <span>Genap</span>
                    </label>
                </div>
            </div>

            {{-- Themes Section --}}
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tema Penilaian <span class="text-red-500">*</span></label>
                    <button type="button" 
                            @click="addTheme()"
                            class="flex items-center gap-1 bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        <svg width="16" height="16" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                            <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                        </svg>
                        Tambah Tema
                    </button>
                </div>

                {{-- Themes Container --}}
                <div class="space-y-4">
                    <template x-for="(theme, themeIndex) in newTemplateForm.themes" :key="'theme-' + themeIndex">
                        <div class="border border-gray-200 rounded-lg p-4">
                            {{-- Theme Header --}}
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800" x-text="`Tema ${themeIndex + 1}`"></h4>
                                <button type="button" 
                                        @click="removeTheme(themeIndex)"
                                        x-show="newTemplateForm.themes.length > 1"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    Hapus Tema
                                </button>
                            </div>

                            {{-- Theme Fields --}}
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Tema <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           x-model="theme.code"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                           placeholder="Contoh: T01">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tema <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           x-model="theme.name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                           placeholder="Masukkan nama tema">
                                </div>
                            </div>

                            {{-- Sub-themes Section --}}
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="text-sm font-medium text-gray-700">Sub-tema Penilaian <span class="text-red-500">*</span></h5>
                                    <button type="button" 
                                            @click="addSubTheme(themeIndex)"
                                            class="flex items-center gap-1 bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">
                                        <svg width="12" height="12" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                                            <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                                        </svg>
                                        Tambah Sub-tema
                                    </button>
                                </div>

                                {{-- Sub-themes Container --}}
                                <div class="space-y-2">
                                    <template x-for="(subTheme, subThemeIndex) in theme.subThemes" :key="'subtheme-' + themeIndex + '-' + subThemeIndex">
                                        <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-medium text-gray-600" x-text="`Sub-tema ${subThemeIndex + 1}`"></span>
                                                <button type="button" 
                                                        @click="removeSubTheme(themeIndex, subThemeIndex)"
                                                        x-show="theme.subThemes.length > 1"
                                                        class="text-red-600 hover:text-red-800 text-xs">
                                                    Hapus
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Kode Sub-tema <span class="text-red-500">*</span></label>
                                                    <input type="text" 
                                                           x-model="subTheme.code"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                                                           placeholder="Contoh: ST01">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Sub-tema <span class="text-red-500">*</span></label>
                                                    <input type="text" 
                                                           x-model="subTheme.name"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                                                           placeholder="Masukkan nama sub-tema">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-2 pt-4">
                <button class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" 
                        @click="mode='view'">Batal</button>
                <button class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700" 
                        @click="saveNewTemplate()">Simpan</button>
            </div>
        </div>
    </div>