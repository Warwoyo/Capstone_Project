{{-- resources/views/components/menu/report-menu.blade.php --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@php /** @var \App\Models\Classroom $class */ @endphp

<script defer>
// ================ HELPERS ================
const uniqBy = (arr, keyFn) => {
    const seen = new Set();
    return arr.filter(i => {
        const k = keyFn(i);
        return seen.has(k) ? false : seen.add(k);
    });
};

function raporApp(classId){
    return {
        /* ---------- state ---------- */
        mode:'view',
        classId,
        loading:false,
        error:null,
        templates:[],
        assignedTemplates:[],
        selectedTemplate:null,
        detailId:null,
        students:[],
        newTemplateForm:{
            title:'',description:'',semester_type:'',
            themes:[{code:'',name:'',subThemes:[{code:'',name:''}]}]
        },
        async saveNewTemplate(){
            const f=this.newTemplateForm;
            if(!f.title.trim()||!f.semester_type) return alert('Isi judul & semester');
            for(const [ti,t] of f.themes.entries()){
                if(!t.code.trim()||!t.name.trim()) return alert(`Tema ${ti+1} belum lengkap`);
                for(const [si,s] of t.subThemes.entries()) if(!s.code.trim()||!s.name.trim()) return alert(`Subtema ${si+1} Tema ${ti+1} belum lengkap`);
            }
            await this.req('/rapor/templates',{method:'POST',body:JSON.stringify(f),headers:{'Content-Type':'application/json'}});
            await this.loadTemplates();
            this.mode='view';
            this.resetForm();
            alert('Template ditambahkan');
        },


        // ---------- utils ----------
        csrf(){return document.querySelector('meta[name="csrf-token"]').content;},

        async req(url, opt = {}) {
          // pull headers aside so …rest doesn't clobber your merge
          const { headers = {}, ...rest } = opt;

          const res = await fetch(url, {
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': this.csrf(),
              ...headers
            },
            ...rest
          });

          if (!res.ok) throw new Error(`${res.status}`);
          return res.status === 204 ? null : res.json();
        },

        dedup(list){return uniqBy(list.filter(t=>t&&t.id&&t.title&&t.semester_type),t=>t.id);},
        getSubThemes(th){return th.sub_themes??th.subThemes??[];},
        totalSub(tpl){return tpl?.themes?.reduce((n,t)=>n+this.getSubThemes(t).length,0)||0;},

        // ---------- lifecycle ----------
        async init(){
            try{
                this.loading=true;
                [this.templates,this.assignedTemplate]=await Promise.all([
                    this.loadTemplates(),
                    this.loadAssignedTemplate()
                ]);
            }catch(e){this.error='Gagal memuat data';}
            this.loading=false;
        },

        // ---------- data ----------
        async loadTemplates(){
            const data = await this.req('/rapor/templates');
            return this.templates = this.dedup(data);
        },
        async loadAssignedTemplate(){
            try{return await this.req(`/rapor/classes/${this.classId}/assigned-template`);}catch{ return null; }
        },
        async loadStudents(){this.students=await this.req(`/classroom/${this.classId}/students`);},

        // ---------- ui actions ----------
        previewTemplate(t){this.selectedTemplate=t;this.mode='preview';},
        cancelPreview(){this.selectedTemplate=null;this.mode='view';},
        async confirmAssignTemplate(){
            await this.req(
            `/rapor/templates/${this.selectedTemplate.id}/assign`,
            {
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body: JSON.stringify({class_id:this.classId})
            }
          );
        },
        async openReportForm(){
          if(!this.assignedTemplate) return alert('Belum ada template');
          await this.loadStudents();
          this.selectedTemplate = this.assignedTemplate;
          // initialize empty scores map
          this.scores = {};
          this.students.forEach(s=>{
            this.scores[s.id] = {};
            this.selectedTemplate.themes.forEach(th=>{
              this.getSubThemes(th).forEach(st=>{
                this.scores[s.id][st.id] = null;
              });
            });
          });
          this.mode = 'score';
        },
        showDetail(t){this.detailId=this.detailId===t.id?null:t.id;},
        async deleteTemplate(id){
            if(!confirm('Yakin hapus?'))return;
            await this.req(`/rapor/templates/${id}`,{method:'DELETE'});
            await this.loadTemplates();
            alert('Hapus sukses');
        },

        // ---------- form ----------
        newTemplate(){this.resetForm();this.mode='add';},
        resetForm(){this.newTemplateForm={title:'',description:'',semester_type:'',themes:[{code:'',name:'',subThemes:[{code:'',name:''}]}]};},
        addTheme(){this.newTemplateForm.themes.push({code:'',name:'',subThemes:[{code:'',name:''}]});},
        removeTheme(i){if(this.newTemplateForm.themes.length>1) this.newTemplateForm.themes.splice(i,1);},
        addSubTheme(i){this.newTemplateForm.themes[i].subThemes.push({code:'',name:''});},
        removeSubTheme(i,j){const st=this.newTemplateForm.themes[i].subThemes;if(st.length>1) st.splice(j,1);},
        async saveNewTemplate(){
            const f=this.newTemplateForm;
            if(!f.title.trim()||!f.semester_type) return alert('Isi judul & semester');
            for(const [ti,t] of f.themes.entries()){
                if(!t.code.trim()||!t.name.trim()) return alert(`Tema ${ti+1} belum lengkap`);
                for(const [si,s] of t.subThemes.entries()) if(!s.code.trim()||!s.name.trim()) return alert(`Subtema ${si+1} Tema ${ti+1} belum lengkap`);
            }
            await this.req('/rapor/templates',{method:'POST',body:JSON.stringify(f),headers:{'Content-Type':'application/json'}});
            await this.loadTemplates();
            this.mode='view';
            this.resetForm();
            alert('Template ditambahkan');
        },
        async saveScores(){
          await this.req(
            `/rapor/classes/${this.classId}/reports`,
            {
              method: 'POST',
              headers: { 'Content-Type':'application/json' },
              body: JSON.stringify({
                template_id: this.assignedTemplate.id,
                scores: this.scores
              })
            }
          );
          alert('Rapor tersimpan');
          this.mode = 'view';
          this.selectedTemplate = null;
        },
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

    {{-- STATE DEBUG --}}
    <div x-show="false" class="mb-4 p-3 bg-gray-100 rounded text-xs">
        <div>Mode: <span x-text="mode"></span></div>
        <div>Loading: <span x-text="loading"></span></div>
        <div>Templates count: <span x-text="templates.length"></span></div>
        <div>Template IDs: <span x-text="JSON.stringify(templates.map(t => t.id))"></span></div>
    </div>

    {{-- LOADING --}}
    <div x-show="loading" class="flex justify-center items-center h-40 text-sky-600">Memuat…</div>

    {{-- ERROR --}}
    <div x-show="!loading && error" class="bg-red-50 border border-red-200 p-4 rounded-lg">
        <div class="text-red-600" x-text="error"></div>
        <button @click="init()" class="mt-2 px-3 py-1 bg-red-600 text-white rounded text-sm">Coba lagi</button>
    </div>

    {{-- ===================== MAIN VIEW ===================== --}}
    <div x-show="!loading && !error && mode==='view'" class="space-y-4">
        {{-- Header dengan kondisi berdasarkan apakah sudah ada template yang ditetapkan --}}
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">
                <span x-show="!assignedTemplate">Pilih Template Rapor</span>
                <span x-show="assignedTemplate">Template Rapor Kelas</span>
            </h1>
            
            {{-- Tombol berdasarkan kondisi --}}
            <div class="flex gap-2">
                <button x-show="!assignedTemplate" 
                        class="flex items-center gap-2 bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700" 
                        @click="newTemplate()">
                    <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                        <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    Tambah Template
                </button>
                
                <button x-show="assignedTemplate" 
                        class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" 
                        @click="openReportForm()">
                    <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                        <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    Tambah Rapor
                </button>
            </div>
        </div>

        {{-- Template yang sudah ditetapkan --}}
        <div x-show="assignedTemplate" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="font-semibold text-green-800" x-text="assignedTemplate?.title"></h3>
                    <p class="text-sm text-green-600" x-text="assignedTemplate?.semester_type ? `Semester ${assignedTemplate.semester_type}` : ''"></p>
                    <p class="text-sm text-green-700 mt-2" x-text="assignedTemplate?.description"></p>
                    <div class="mt-3 grid grid-cols-2 gap-4 text-xs text-green-600">
                        <div>
                            <span class="font-medium">Tema:</span> 
                            <span x-text="assignedTemplate?.themes?.length || 0"></span>
                        </div>
                        <div>
                            <span class="font-medium">Sub-tema:</span> 
                            <span x-text="getTotalSubThemes(assignedTemplate)"></span>
                        </div>
                    </div>
                    
                    {{-- Show theme structure for assigned template --}}
                    <div x-show="assignedTemplate?.themes?.length > 0" class="mt-4">
                        <h4 class="text-sm font-medium text-green-800 mb-2">Struktur Penilaian:</h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <template x-for="(theme, themeIndex) in assignedTemplate?.themes || []" :key="'assigned-theme-' + themeIndex">
                                <div class="bg-green-100 border border-green-300 rounded p-2">
                                    <div class="font-medium text-green-800 text-sm">
                                        <span x-text="`${theme.code} - ${theme.name}`"></span>
                                    </div>
                                    <div class="ml-3 mt-1 space-y-1">
                                        <template x-for="(subTheme, subIndex) in getSubThemes(theme)" :key="'assigned-sub-' + themeIndex + '-' + subIndex">
                                            <div class="text-xs text-green-700">
                                                • <span x-text="`${subTheme.code} - ${subTheme.name}`"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-4">Template Aktif</span>
            </div>
        </div>

        {{-- Daftar template yang tersedia (hanya tampil jika belum ada template yang ditetapkan) --}}
        <div x-show="!assignedTemplate">
            {{-- Empty State --}}
            <div x-show="templates.length === 0" class="text-center py-8 text-gray-500">
                <p>Data template masih kosong</p>
                <p class="text-sm mt-2">Silakan buat template baru untuk memulai</p>
            </div>

            {{-- Templates Grid --}}
            <div x-show="templates.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="template in templates" :key="'template-card-' + template.id">
                    <article class="p-4 bg-white border border-sky-500 rounded-xl flex flex-col justify-between cursor-pointer hover:shadow"
                             @click="previewTemplate(template)">
                        <div>
                            <h2 class="font-bold text-sky-800 truncate" x-text="template.title || 'Template Tanpa Judul'"></h2>
                            <p class="text-sm text-gray-500" x-text="template.semester_type ? `Semester ${template.semester_type}` : 'Tidak ada semester'"></p>
                            <div class="mt-2 flex gap-4 text-xs text-gray-600">
                                <span><span class="font-medium">Tema:</span> <span x-text="template.themes ? template.themes.length : 0"></span></span>
                                <span><span class="font-medium">Sub-tema:</span> <span x-text="getTotalSubThemes(template)"></span></span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <button type="button" class="text-xs text-sky-800" @click.stop="showDetail(template)">
                                Detail
                            </button>
                            <span class="text-xs text-sky-600">Klik untuk preview</span>
                        </div>
                        
                        {{-- DETAIL PANEL --}}
                        <div x-show="detailId === template.id" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="mt-3 border-t pt-3 text-sm text-gray-700">
                            <p x-text="template.description||'Tidak ada deskripsi.'"></p>
                            
                            {{-- Show themes and sub-themes in detail --}}
                            <div x-show="template.themes?.length > 0" class="mt-3">
                                <h5 class="font-medium text-gray-800 mb-2">Tema Penilaian:</h5>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    <template x-for="(theme, themeIndex) in template.themes || []" :key="'detail-theme-' + template.id + '-' + themeIndex">
                                        <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                            <div class="font-medium text-gray-800 text-sm">
                                                <span x-text="`${theme.code} - ${theme.name}`"></span>
                                            </div>
                                            <div class="ml-3 mt-1 space-y-1">
                                                <template x-for="(subTheme, subIndex) in getSubThemes(theme)" :key="'detail-sub-' + template.id + '-' + themeIndex + '-' + subIndex">
                                                    <div class="text-xs text-gray-600">
                                                        • <span x-text="`${subTheme.code} - ${subTheme.name}`"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 mt-3 text-xs">
                                <button class="text-red-600" @click.stop="deleteTemplate(template.id)">Hapus</button>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </div>
    </div>

    {{-- ===================== TEMPLATE PREVIEW ===================== --}}
    <div x-show="!loading && !error && mode==='preview'" class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">Preview Template</h1>
            <button class="text-gray-600 hover:text-gray-800" @click="cancelPreview()">
                ← Kembali
            </button>
        </div>
        
        <div class="bg-white border border-sky-200 rounded-lg p-6 space-y-6">
            {{-- Template Info --}}
            <div class="border-b pb-4">
                <h2 class="text-xl font-bold text-sky-800" x-text="selectedTemplate?.title"></h2>
                <p class="text-gray-600" x-text="selectedTemplate?.description"></p>
                <div class="mt-2 flex gap-4 text-sm">
                    <span class="bg-sky-100 text-sky-800 px-2 py-1 rounded" 
                          x-text="selectedTemplate?.semester_type ? `Semester ${selectedTemplate.semester_type}` : ''"></span>
                    <span class="text-gray-500" 
                          x-text="selectedTemplate?.themes ? `${selectedTemplate.themes.length} Tema` : ''"></span>
                    <span class="text-gray-500" 
                          x-text="`${getTotalSubThemes(selectedTemplate)} Sub-tema`"></span>
                </div>
            </div>

            {{-- Template Content --}}
            <div x-show="selectedTemplate?.themes?.length > 0">
                <h3 class="font-semibold text-gray-800 mb-4">Struktur Penilaian:</h3>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    <template x-for="(theme, themeIndex) in selectedTemplate?.themes || []" :key="'preview-theme-' + themeIndex">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="font-medium text-gray-800 mb-3 pb-2 border-b border-gray-100">
                                <span x-text="`${theme.code} - ${theme.name}`"></span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-700">Sub-tema:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <template x-for="(subTheme, subIndex) in getSubThemes(theme)" :key="'preview-sub-' + themeIndex + '-' + subIndex">
                                        <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                            <div class="text-sm text-gray-800">
                                                <span class="font-medium" x-text="subTheme.code"></span>
                                                <span x-text="' - ' + subTheme.name"></span>
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
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" 
                        @click="cancelPreview()">
                    Batal
                </button>
                <button class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700" 
                        @click="confirmAssignTemplate()">
                    Gunakan Template Ini
                </button>
            </div>
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
                    <template x-for="(theme, themeIndex) in newTemplateForm.themes" :key="'theme-form-' + themeIndex">
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
                                    <template x-for="(subTheme, subThemeIndex) in theme.subThemes" :key="'subtheme-form-' + themeIndex + '-' + subThemeIndex">
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
</div>