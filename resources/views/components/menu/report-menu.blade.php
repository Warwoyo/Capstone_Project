{{-- resources/views/components/menu/report-menu.blade.php --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@php /** @var \App\Models\Classroom $class */ @endphp


<script defer>
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
        assignedTemplate:null,
        selectedTemplate:null,
        detailId:null,
        students:[],
        newTemplateForm:{
            title:'',description:'',semester_type:'',
            themes:[{code:'',name:'',subThemes:[{code:'',name:''}]}]
        },
        
        // Helper variable to prevent Alpine.js template scope conflicts
        currentTemplate: null,

        // ---------- utils ----------
        csrf(){return document.querySelector('meta[name="csrf-token"]').content;},

        async req(url, opt = {}) {
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

        // Enhanced deduplication with better validation
        dedup(list){
            if (!Array.isArray(list)) return [];
            const filtered = list.filter(t => {
                return t && 
                       typeof t === 'object' && 
                       t.id && 
                       typeof t.id === 'number' && 
                       t.title && 
                       typeof t.title === 'string' && 
                       t.semester_type;
            });
            return uniqBy(filtered, t => t.id);
        },
        
        getSubThemes(th){return th.sub_themes??th.subThemes??[];},
        totalSub(tpl){return tpl?.themes?.reduce((n,t)=>n+this.getSubThemes(t).length,0)||0;},

        // Helper method to safely get template data
        getTemplateData(template) {
            if (!template || typeof template !== 'object') return null;
            return {
                id: template.id,
                title: template.title || 'Template Tanpa Judul',
                description: template.description || 'Tidak ada deskripsi',
                semester_type: template.semester_type || '',
                themes: template.themes || []
            };
        },

        // ---------- lifecycle ----------
        async init() {
            try {
                this.loading = true;
                this.error = null;
                
                console.log('Starting init for class:', this.classId);
                
                // Load assigned template first
                const assigned = await this.loadAssignedTemplate();
                this.assignedTemplate = assigned;
                
                // Load all templates
                let allTemplates = await this.loadTemplates();
                
                // Ensure we have clean, deduplicated data
                allTemplates = this.dedup(allTemplates);
                
                // Filter out assigned template from available templates
                this.templates = assigned 
                    ? allTemplates.filter(t => t.id !== assigned.id)
                    : allTemplates;
                
                console.log('Init complete:', {
                    assignedTemplate: this.assignedTemplate?.id,
                    availableTemplates: this.templates.map(t => ({ id: t.id, title: t.title }))
                });
                
            } catch (e) {
                console.error('Init error:', e);
                this.error = 'Gagal memuat data: ' + e.message;
            }
            this.loading = false;
        },

        // ---------- data ----------
        async loadTemplates(){
            try {
                const data = await this.req('/rapor/templates');
                if (!Array.isArray(data)) {
                    console.warn('Templates API returned non-array:', data);
                    return [];
                }
                const dedupedData = this.dedup(data);
                console.log('Templates loaded:', dedupedData.length, 'unique items');
                return dedupedData;
            } catch (e) {
                console.error('Failed to load templates:', e);
                return [];
            }
        },
        
        async loadAssignedTemplate(){
            try{
                const assigned = await this.req(`/rapor/classes/${this.classId}/assigned-template`);
                console.log('Assigned template:', assigned?.id);
                return assigned && typeof assigned === 'object' ? assigned : null;
            }catch(e){ 
                console.log('No assigned template found:', e.message);
                return null; 
            }
        },
        
        async loadStudents(){
            try {
                this.students = await this.req(`/classroom/${this.classId}/students`);
            } catch (e) {
                console.error('Failed to load students:', e);
                this.students = [];
            }
        },

        // ---------- ui actions ----------
        previewTemplate(t){
            this.currentTemplate = this.getTemplateData(t);
            this.selectedTemplate = this.currentTemplate;
            this.mode='preview';
        },
        
        cancelPreview(){
            this.selectedTemplate=null;
            this.currentTemplate=null;
            this.mode='view';
        },
        
        async confirmAssignTemplate(){
            if (!this.selectedTemplate) return;
            
            try {
                await this.req(
                    `/rapor/templates/${this.selectedTemplate.id}/assign`,
                    {
                        method:'POST',
                        headers:{'Content-Type':'application/json'},
                        body: JSON.stringify({class_id:this.classId})
                    }
                );
                
                // Update state after successful assignment
                this.assignedTemplate = this.selectedTemplate;
                this.templates = this.templates.filter(t => t.id !== this.selectedTemplate.id);
                this.selectedTemplate = null;
                this.currentTemplate = null;
                this.mode = 'view';
                
                alert('Template berhasil ditetapkan');
            } catch (e) {
                console.error('Assignment error:', e);
                alert('Gagal menetapkan template: ' + e.message);
            }
        },
        
        async openReportForm(){
          if(!this.assignedTemplate) return alert('Belum ada template');
          await this.loadStudents();
          this.selectedTemplate = this.assignedTemplate;
          this.currentTemplate = this.getTemplateData(this.assignedTemplate);
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
        
        showDetail(t){
            this.detailId = this.detailId === t.id ? null : t.id;
        },
        
        async deleteTemplate(id){
            if(!confirm('Yakin hapus?'))return;
            try {
                await this.req(`/rapor/templates/${id}`,{method:'DELETE'});
                
                // Remove from templates array
                this.templates = this.templates.filter(t => t.id !== id);
                
                // Close detail if it was open for this template
                if (this.detailId === id) {
                    this.detailId = null;
                }
                
                alert('Template berhasil dihapus');
            } catch (e) {
                console.error('Delete error:', e);
                alert('Gagal menghapus template: ' + e.message);
            }
        },

        // ---------- form ----------
        newTemplate(){this.resetForm();this.mode='add';},
        resetForm(){
            this.newTemplateForm={
                title:'',
                description:'',
                semester_type:'',
                themes:[{code:'',name:'',subThemes:[{code:'',name:''}]}]
            };
        },
        addTheme(){this.newTemplateForm.themes.push({code:'',name:'',subThemes:[{code:'',name:''}]});},
        removeTheme(i){if(this.newTemplateForm.themes.length>1) this.newTemplateForm.themes.splice(i,1);},
        addSubTheme(i){this.newTemplateForm.themes[i].subThemes.push({code:'',name:''});},
        removeSubTheme(i,j){const st=this.newTemplateForm.themes[i].subThemes;if(st.length>1) st.splice(j,1);},
        
        async saveNewTemplate(){
            const f=this.newTemplateForm;
            if(!f.title.trim()||!f.semester_type) return alert('Isi judul & semester');
            for(const [ti,t] of f.themes.entries()){
                if(!t.code.trim()||!t.name.trim()) return alert(`Tema ${ti+1} belum lengkap`);
                for(const [si,s] of t.subThemes.entries()) {
                    if(!s.code.trim()||!s.name.trim()) return alert(`Subtema ${si+1} Tema ${ti+1} belum lengkap`);
                }
            }
            
            try {
                const newTemplate = await this.req('/rapor/templates',{
                    method:'POST',
                    body:JSON.stringify(f),
                    headers:{'Content-Type':'application/json'}
                });
                
                // Add new template to list if not assigned
                if (!this.assignedTemplate && newTemplate) {
                    const cleanTemplate = this.getTemplateData(newTemplate);
                    if (cleanTemplate) {
                        this.templates = this.dedup([...this.templates, cleanTemplate]);
                    }
                }
                
                this.mode='view';
                this.resetForm();
                alert('Template ditambahkan');
            } catch (e) {
                console.error('Save template error:', e);
                alert('Gagal menyimpan template: ' + e.message);
            }
        },
        
        async saveScores(){
          try {
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
              this.currentTemplate = null;
          } catch (e) {
              console.error('Save scores error:', e);
              alert('Gagal menyimpan rapor: ' + e.message);
          }
        },
        async removeAssignedTemplate() {
            if (!this.assignedTemplate || !confirm('Yakin hapus template yang ditetapkan?')) return;
            
            try {
                // Use the correct endpoint
                await this.req(`/rapor/classes/${this.classId}/assigned-template`, {
                    method: 'DELETE'
                });
                
                // Move assigned template back to available templates
                if (this.assignedTemplate) {
                    this.templates = this.dedup([...this.templates, this.assignedTemplate]);
                    this.assignedTemplate = null;
                }
                
                alert('Template berhasil dihapus dari kelas');
            } catch (e) {
                console.error('Remove assigned template error:', e);
                alert('Gagal menghapus template: ' + e.message);
            }
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
<div x-show="assignedTemplate" class="space-y-3">
    <h3 class="text-sm font-medium text-gray-700">Template yang Sudah Ditetapkan:</h3>
    <div class="bg-white border border-sky-200 rounded-lg p-4">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h3 class="font-semibold text-sky-800" x-text="assignedTemplate?.title"></h3>
                <p class="text-sm text-gray-500" x-text="assignedTemplate?.semester_type ? `Semester ${assignedTemplate.semester_type}` : ''"></p>
                <p class="text-sm text-gray-600 mt-2" x-text="assignedTemplate?.description"></p>
                <div class="mt-3 flex gap-4 text-xs text-gray-600">
                    <div>
                        <span class="font-medium">Tema:</span>
                        <span x-text="assignedTemplate?.themes?.length || 0"></span>
                    </div>
                    <div>
                        <span class="font-medium">Sub-tema:</span>
                        <span x-text="getTotalSubThemes(assignedTemplate)"></span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2 ml-4">
                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Template Aktif</span>
                <button @click.prevent.stop="openReportForm()"
                        class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                    Buat Rapor
                </button>
                <button @click.prevent.stop="removeAssignedTemplate()"
                        class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>



        {{-- Tombol untuk menambah template baru --}}

        {{-- Daftar template yang tersedia (hanya tampil jika belum ada template yang ditetapkan) --}}
        <div x-show="!assignedTemplate">
            {{-- Empty State --}}
            <div x-show="templates.length === 0" class="text-center py-8 text-gray-500">
                <p>Data template masih kosong</p>
                <p class="text-sm mt-2">Silakan buat template baru untuk memulai</p>
            </div>

            {{-- Templates Grid --}}
<div x-show="templates.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <template x-for="(templateItem, templateIndex) in templates" :key="'template-card-' + templateItem.id">
        <article class="p-4 bg-white border border-sky-500 rounded-xl flex flex-col justify-between cursor-pointer hover:shadow"
                 @click="previewTemplate(templateItem)">
            <div>
                <h2 class="font-bold text-sky-800 truncate" x-text="templateItem.title || 'Template Tanpa Judul'"></h2>
                <p class="text-sm text-gray-500" x-text="templateItem.semester_type ? `Semester ${templateItem.semester_type}` : 'Tidak ada semester'"></p>
                <div class="mt-2 flex gap-4 text-xs text-gray-600">
                    <span><span class="font-medium">Tema:</span> <span x-text="templateItem.themes ? templateItem.themes.length : 0"></span></span>
                
                </div>
            </div>
            <div class="flex justify-between items-center mt-3">
                <button type="button" class="text-xs text-sky-800" @click.stop="showDetail(templateItem)">
                    Detail
                </button>
                <span class="text-xs text-sky-600">Klik untuk preview</span>
            </div>
            
            {{-- DETAIL PANEL --}}
            <div x-show="detailId === templateItem.id" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mt-3 border-t pt-3 text-sm text-gray-700">
                <p x-text="templateItem.description || 'Tidak ada deskripsi.'"></p>
                
                {{-- Show themes and sub-themes in detail --}}
                <div x-show="templateItem.themes?.length > 0" class="mt-3">
                    <h5 class="font-medium text-gray-800 mb-2">Tema Penilaian:</h5>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <template x-for="(themeItem, themeIndex) in templateItem.themes || []" :key="'detail-theme-' + templateItem.id + '-' + themeIndex">
                            <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                <div class="font-medium text-gray-800 text-sm">
                                    <span x-text="`${themeItem.code} - ${themeItem.name}`"></span>
                                </div>
                                <div class="ml-3 mt-1 space-y-1">
                                    <template x-for="(subThemeItem, subIndex) in getSubThemes(themeItem)" :key="'detail-sub-' + templateItem.id + '-' + themeIndex + '-' + subIndex">
                                        <div class="text-xs text-gray-600">
                                            • <span x-text="`${subThemeItem.code} - ${subThemeItem.name}`"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-3 text-xs">
                    <button class="text-red-600" @click.stop="deleteTemplate(templateItem.id)">Hapus</button>
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