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
        assignedTemplates:[], // Change to array for multiple templates
        selectedTemplate:null,
        selectedStudent:null, // Add selectedStudent
        detailId:null,
        students:[],
        scores:{}, // Initialize scores object
        themeComments:{}, // Add theme comments
        subThemeComments:{}, // Add sub-theme comments
        teacherComments:{}, // Add teacher comments
        parentComments:{}, // Add parent comments
        physicalData:{}, // Add physical data
        attendanceData:{}, // Add attendance data
        savedReports:{}, // Add saved reports storage
        viewingReport: null, // Add viewing report state
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
              'Content-Type': 'application/json',
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
                       (typeof t.id === 'number' || typeof t.id === 'string') && 
                       t.title && 
                       typeof t.title === 'string' && 
                       t.title.trim() !== '';
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
                
                // Load all templates first
                let allTemplates = await this.loadTemplates();
                
                // Load assigned templates
                const assignedList = await this.loadAssignedTemplates();

                // Apply strict validation and deduplication
                this.assignedTemplates = this.dedup(assignedList);

                // Ensure we have clean, deduplicated data
                allTemplates = this.dedup(allTemplates);
                
                // Filter out assigned templates from available templates
                const assignedIds = this.assignedTemplates.map(t => t.id);
                this.templates = allTemplates.filter(t => !assignedIds.includes(t.id));
                
            } catch (e) {
                this.error = 'Gagal memuat data';
            }
            this.loading = false;
        },

        // ---------- data ----------
        async loadTemplates(){
            try {
                const data = await this.req('/rapor/templates');
                
                if (!Array.isArray(data)) {
                    if (data && data.data && Array.isArray(data.data)) {
                        return this.dedup(data.data);
                    }
                    return [];
                }
                
                // Apply basic validation
                const validTemplates = data.filter(t => {
                    return t && 
                           typeof t === 'object' && 
                           t.id && 
                           t.title;
                });
                
                return this.dedup(validTemplates);
                
            } catch (e) {
                return [];
            }
        },
        
        // Change to load multiple assigned templates
        async loadAssignedTemplates(){
            try{
                // Try the main endpoint first
                let assigned = await this.req(`/rapor/classes/${this.classId}/assigned-templates`);
                
                if (!assigned) {
                    return [];
                }
                
                // Handle different response formats
                let templatesArray = [];
                if (Array.isArray(assigned)) {
                    templatesArray = assigned;
                } else if (assigned.data && Array.isArray(assigned.data)) {
                    templatesArray = assigned.data;
                } else if (assigned.templates && Array.isArray(assigned.templates)) {
                    templatesArray = assigned.templates;
                } else {
                    return [];
                }
                
                // Filter and validate assigned templates
                const validAssigned = templatesArray.filter(t => {
                    return t && 
                           typeof t === 'object' && 
                           t.id && 
                           t.title && 
                           typeof t.title === 'string' && 
                           t.title.trim() !== '';
                });
                
                // Remove duplicates based on template ID
                const uniqueAssigned = validAssigned.filter((template, index, self) =>
                    index === self.findIndex(t => t.id === template.id)
                );

                return uniqueAssigned;
            }catch(e){ 
                return []; 
            }
        },
        
        async loadStudents(){
            try {
                // Try multiple possible endpoints
                let students = [];
                
                // First try the AJAX endpoint
                try {
                    const response = await this.req(`/ajax/classrooms/${this.classId}/students`);
                    students = response.data || response || [];
                } catch (e1) {
                    // Try alternative endpoint
                    try {
                        students = await this.req(`/classroom/${this.classId}/students`);
                    } catch (e2) {
                        // Try rapor endpoint
                        try {
                            const response = await this.req(`/rapor/classes/${this.classId}/students`);
                            students = response.data || response || [];
                        } catch (e3) {
                            students = [];
                        }
                    }
                }
                
                this.students = Array.isArray(students) ? students : [];
                
                // Load attendance data for each student
                await this.loadStudentAttendanceData();
                
            } catch (e) {
                this.students = [];
            }
        },

        // New method to load attendance data for students
        async loadStudentAttendanceData() {
            try {
                // Try to get raw attendance records from the database
                let attendanceRecords = [];
                
                try {
                    // Try the summary endpoint first
                    const summaryResponse = await this.req(`/ajax/classrooms/${this.classId}/attendance-summary`);
                    
                    if (summaryResponse && summaryResponse.data) {
                        // Use summary data if available
                        const attendanceData = summaryResponse.data;
                        this.students.forEach(student => {
                            const studentAttendance = attendanceData.find(att => att.student_id === student.id);
                            
                            if (studentAttendance) {
                                this.attendanceData[student.id] = {
                                    sick: studentAttendance.sick_count || 0,
                                    permission: studentAttendance.permission_count || 0,
                                    absent: studentAttendance.absent_count || 0,
                                    present: studentAttendance.present_count || 0,
                                    total_sessions: studentAttendance.total_sessions || 0
                                };
                            }
                        });
                        return;
                    }
                } catch (e1) {
                    // Silent fallback
                }
                
                // If summary fails, get raw attendance records and calculate manually
                try {
                    const rawResponse = await this.req(`/ajax/classrooms/${this.classId}/attendances`);
                    attendanceRecords = rawResponse.data || rawResponse || [];
                } catch (e2) {
                    try {
                        attendanceRecords = await this.req(`/attendance/classroom/${this.classId}`);
                    } catch (e3) {
                        attendanceRecords = [];
                    }
                }
                
                // Calculate attendance data manually from raw records
                this.students.forEach(student => {
                    // Filter records for this student
                    const studentRecords = attendanceRecords.filter(record => 
                        record.student_id === student.id || record.student_id == student.id
                    );
                    
                    // Count each status type
                    const sickCount = studentRecords.filter(r => 
                        r.status === 'sakit' || r.status === 'sick'
                    ).length;
                    
                    const permissionCount = studentRecords.filter(r => 
                        r.status === 'ijin' || r.status === 'izin' || r.status === 'permission'
                    ).length;
                    
                    const absentCount = studentRecords.filter(r => 
                        r.status === 'alpha' || r.status === 'absent' || r.status === 'alpa'
                    ).length;
                    
                    const presentCount = studentRecords.filter(r => 
                        r.status === 'hadir' || r.status === 'present'
                    ).length;
                    
                    const totalSessions = studentRecords.length;
                    
                    // Initialize attendance data for this student
                    this.attendanceData[student.id] = {
                        sick: sickCount,
                        permission: permissionCount,
                        absent: absentCount,
                        present: presentCount,
                        total_sessions: totalSessions
                    };
                });
                
            } catch (e) {
                // Initialize with default values if loading fails
                this.students.forEach(student => {
                    if (!this.attendanceData[student.id]) {
                        this.attendanceData[student.id] = {
                            sick: 0,
                            permission: 0,
                            absent: 0,
                            present: 0,
                            total_sessions: 0
                        };
                    }
                });
            }
        },

        // Method to load existing reports for all students
        async loadExistingReports() {
            if (!this.selectedTemplate || !this.students.length) return;
            
            try {
                for (const student of this.students) {
                    try {
                        const reportResponse = await this.req(`/rapor/classes/${this.classId}/reports/${student.id}/${this.selectedTemplate.id}`);
                        
                        if (reportResponse.success && reportResponse.data) {
                            const reportData = reportResponse.data;
                            
                            // Load scores
                            if (reportData.scores) {
                                this.scores[student.id] = { ...this.scores[student.id], ...reportData.scores };
                            }
                            
                            // Load comments
                            if (reportData.teacher_comment) {
                                if (!this.teacherComments) this.teacherComments = {};
                                this.teacherComments[student.id] = reportData.teacher_comment;
                            }
                            
                            if (reportData.parent_comment) {
                                if (!this.parentComments) this.parentComments = {};
                                this.parentComments[student.id] = reportData.parent_comment;
                            }
                            
                            // Load physical data
                            if (reportData.physical_data) {
                                if (!this.physicalData) this.physicalData = {};
                                this.physicalData[student.id] = reportData.physical_data;
                            }
                            
                            // Load theme comments
                            if (reportData.theme_comments) {
                                if (!this.themeComments) this.themeComments = {};
                                Object.keys(reportData.theme_comments).forEach(themeId => {
                                    const commentKey = student.id + '_' + themeId;
                                    this.themeComments[commentKey] = reportData.theme_comments[themeId];
                                });
                            }
                            
                            // Load sub-theme comments
                            if (reportData.sub_theme_comments) {
                                if (!this.subThemeComments) this.subThemeComments = {};
                                Object.keys(reportData.sub_theme_comments).forEach(subThemeId => {
                                    const commentKey = student.id + '_' + subThemeId;
                                    this.subThemeComments[commentKey] = reportData.sub_theme_comments[subThemeId];
                                });
                            }
                        }
                    } catch (e) {
                        // Student doesn't have a report yet, that's fine
                    }
                }
                
            } catch (e) {
                // Silent error handling
            }
        },

        // ---------- ui actions ----------
        previewTemplate(t){
            this.currentTemplate = this.getTemplateData(t);
            this.selectedTemplate = this.currentTemplate;
            this.mode='preview';
        },
        
        // Method to edit a template
        editTemplate(template) {
            // Load template data into form
            this.newTemplateForm = {
                id: template.id,
                title: template.title || '',
                description: template.description || '',
                semester_type: template.semester_type || '',
                themes: template.themes ? JSON.parse(JSON.stringify(template.themes)) : []
            };
            
            // Ensure themes have the correct structure
            this.newTemplateForm.themes = this.newTemplateForm.themes.map(theme => ({
                ...theme,
                subThemes: this.getSubThemes(theme)
            }));
            
            this.mode = 'edit';
        },
        
        // Method to save edited template
        async saveEditedTemplate() {
            const f = this.newTemplateForm;
            if (!f.title.trim() || !f.semester_type) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Isi judul & semester', isError: true }
                }));
                return;
            }
            
            for (const [ti, t] of f.themes.entries()) {
                if (!t.code.trim() || !t.name.trim()) {
                    window.dispatchEvent(new CustomEvent('open-success', {
                        detail: { message: `Tema ${ti + 1} belum lengkap`, isError: true }
                    }));
                    return;
                }
                for (const [si, s] of t.subThemes.entries()) {
                    if (!s.code.trim() || !s.name.trim()) {
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: `Subtema ${si + 1} Tema ${ti + 1} belum lengkap`, isError: true }
                        }));
                        return;
                    }
                }
            }
            
            try {
                const updatedTemplate = await this.req(`/rapor/templates/${f.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        title: f.title,
                        description: f.description,
                        semester_type: f.semester_type,
                        themes: f.themes
                    }),
                    headers: { 'Content-Type': 'application/json' }
                });
                
                // Update template in the list
                if (updatedTemplate) {
                    const cleanTemplate = this.getTemplateData(updatedTemplate);
                    if (cleanTemplate && cleanTemplate.title && cleanTemplate.semester_type) {
                        // Find and replace the template in the array
                        const templateIndex = this.templates.findIndex(t => t.id === f.id);
                        if (templateIndex !== -1) {
                            this.templates[templateIndex] = cleanTemplate;
                        }
                        
                        // Also update in assignedTemplates if it exists there
                        const assignedIndex = this.assignedTemplates.findIndex(t => t.id === f.id);
                        if (assignedIndex !== -1) {
                            this.assignedTemplates[assignedIndex] = cleanTemplate;
                        }
                    }
                }
                
                this.mode = 'view';
                this.resetForm();
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Template berhasil diperbarui' }
                }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Gagal memperbarui template', isError: true }
                }));
            }
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
                this.assignedTemplates = this.dedup([...this.assignedTemplates, this.selectedTemplate]);
                this.templates = this.templates.filter(t => t.id !== this.selectedTemplate.id);
                this.selectedTemplate = null;
                this.currentTemplate = null;
                this.mode = 'view';
                
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Template berhasil ditetapkan' }
                }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Gagal menetapkan template', isError: true }
                }));
            }
        },
        
        // New method for "Tambah Rapor" - shows template selection
        showTemplateSelection(){
            this.mode = 'select-template';
        },
        
        // Method to select template for creating report
        selectTemplateForReport(template){
            // Ensure we have fresh template data
            this.selectedTemplate = this.getTemplateData(template);
            this.openReportForm();
        },
        
        async openReportForm(){
          if(!this.selectedTemplate) {
              window.dispatchEvent(new CustomEvent('open-success', {
                  detail: { message: 'Pilih template terlebih dahulu', isError: true }
              }));
              return;
          }
          await this.loadStudents();
          
          // Force refresh template data to ensure we have the latest themes and sub-themes
          try {
              const freshTemplate = await this.req(`/rapor/templates/${this.selectedTemplate.id}`);
              if (freshTemplate && freshTemplate.themes) {
                  this.selectedTemplate = this.getTemplateData(freshTemplate);
              }
          } catch (e) {
              // Silent error if template refresh fails
          }
          
          this.currentTemplate = this.selectedTemplate;
          
          // initialize empty scores map
          this.scores = {};
          this.students.forEach(s=>{
            this.scores[s.id] = {};
            if (this.selectedTemplate.themes) {
              this.selectedTemplate.themes.forEach(th=>{
                this.getSubThemes(th).forEach(st=>{
                  this.scores[s.id][st.id] = null;
                });
              });
            }
          });
          
          // Load existing reports for all students
          await this.loadExistingReports();
          
          this.mode = 'score';
        },
        
        showDetail(t){
            this.detailId = this.detailId === t.id ? null : t.id;
        },
        
        async deleteTemplate(id){
            // Create confirmation handler
            const confirmHandler = {
                submit: async () => {
                    try {
                        await this.req(`/rapor/templates/${id}`,{method:'DELETE'});
                        
                        // Remove from templates array
                        this.templates = this.templates.filter(t => t.id !== id);
                        
                        // Also remove from assigned templates if it exists there
                        this.assignedTemplates = this.assignedTemplates.filter(t => t.id !== id);
                        
                        // Close detail if it was open for this template
                        if (this.detailId === id) {
                            this.detailId = null;
                        }
                        
                        // Close confirmation dialog by dispatching close event
                        window.dispatchEvent(new CustomEvent('close-confirmation'));
                        
                        // Show success message
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: 'Template berhasil dihapus' }
                        }));
                    } catch (e) {
                        // Close confirmation dialog even on error
                        window.dispatchEvent(new CustomEvent('close-confirmation'));
                        
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: 'Gagal menghapus template', isError: true }
                        }));
                    }
                }
            };
            
            window.dispatchEvent(new CustomEvent('open-confirmation', {
                detail: {
                    label: 'template ini',
                    action: 'menghapus',
                    target: confirmHandler
                }
            }));
        },

        // Method to remove assigned template
        async removeAssignedTemplate(templateId) {
            // Create confirmation handler
            const confirmHandler = {
                submit: async () => {
                    try {
                        await this.req(`/rapor/classes/${this.classId}/assigned-template/${templateId}`, {
                            method: 'DELETE'
                        });
                        
                        // Move template back to available templates
                        const removedTemplate = this.assignedTemplates.find(t => t.id === templateId);
                        if (removedTemplate) {
                            this.assignedTemplates = this.assignedTemplates.filter(t => t.id !== templateId);
                            this.templates = this.dedup([...this.templates, removedTemplate]);
                        }
                        
                        // Close confirmation dialog
                        window.dispatchEvent(new CustomEvent('close-confirmation'));
                        
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: 'Template berhasil dihapus dari kelas' }
                        }));
                    } catch (e) {
                        // Close confirmation dialog even on error
                        window.dispatchEvent(new CustomEvent('close-confirmation'));
                        
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: 'Gagal menghapus template', isError: true }
                        }));
                    }
                }
            };
            
            window.dispatchEvent(new CustomEvent('open-confirmation', {
                detail: {
                    label: 'template yang ditetapkan ini',
                    action: 'menghapus',
                    target: confirmHandler
                }
            }));
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
            if(!f.title.trim()||!f.semester_type) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Isi judul & semester', isError: true }
                }));
                return;
            }
            
            for(const [ti,t] of f.themes.entries()){
                if(!t.code.trim()||!t.name.trim()) {
                    window.dispatchEvent(new CustomEvent('open-success', {
                        detail: { message: `Tema ${ti+1} belum lengkap`, isError: true }
                    }));
                    return;
                }
                for(const [si,s] of t.subThemes.entries()) {
                    if(!s.code.trim()||!s.name.trim()) {
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: `Subtema ${si+1} Tema ${ti+1} belum lengkap`, isError: true }
                        }));
                        return;
                    }
                }
            }
            
            try {
                const newTemplate = await this.req('/rapor/templates',{
                    method:'POST',
                    body:JSON.stringify(f),
                    headers:{'Content-Type':'application/json'}
                });
                
                // Add new template to available list
                if (newTemplate) {
                    const cleanTemplate = this.getTemplateData(newTemplate);
                    if (cleanTemplate && cleanTemplate.title && cleanTemplate.semester_type) {
                        this.templates = this.dedup([...this.templates, cleanTemplate]);
                    }
                }
                
                this.mode='view';
                this.resetForm();
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Template berhasil ditambahkan' }
                }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Gagal menyimpan template', isError: true }
                }));
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
                    template_id: this.selectedTemplate.id,
                    scores: this.scores
                  })
                }
              );
              window.dispatchEvent(new CustomEvent('open-success', {
                  detail: { message: 'Rapor berhasil disimpan' }
              }));
              this.mode = 'view';
              this.selectedTemplate = null;
              this.currentTemplate = null;
          } catch (e) {
              window.dispatchEvent(new CustomEvent('open-success', {
                  detail: { message: 'Gagal menyimpan rapor', isError: true }
              }));
          }
        },

        // ---------- scoring ----------
        getStudentScore(studentId) {
            return this.scores[studentId] || null;
        },
        
        // Check if student has completed report
        hasStudentReport(studentId) {
            return this.scores[studentId] && Object.keys(this.scores[studentId]).length > 0;
        },
        
        async openStudentScoring(student) {
            this.selectedStudent = student;
            
            // Try to load existing report data
            try {
                const existingReport = await this.req(`/rapor/classes/${this.classId}/reports/${student.id}/${this.selectedTemplate.id}`);
                
                if (existingReport.success && existingReport.data) {
                    const reportData = existingReport.data;
                    
                    // Load existing scores
                    if (reportData.scores) {
                        this.scores[student.id] = reportData.scores;
                    }
                    
                    // Load existing comments
                    if (reportData.teacher_comment) {
                        if (!this.teacherComments) this.teacherComments = {};
                        this.teacherComments[student.id] = reportData.teacher_comment;
                    }
                    
                    if (reportData.parent_comment) {
                        if (!this.parentComments) this.parentComments = {};
                        this.parentComments[student.id] = reportData.parent_comment;
                    }
                    
                    // Load existing physical data
                    if (reportData.physical_data) {
                        if (!this.physicalData) this.physicalData = {};
                        this.physicalData[student.id] = reportData.physical_data;
                    }
                    
                    // Load existing attendance data (but don't override if we have current data)
                    if (reportData.attendance_data && !this.attendanceData[student.id]) {
                        this.attendanceData[student.id] = reportData.attendance_data;
                    }
                    
                    // Load existing theme comments
                    if (reportData.theme_comments) {
                        if (!this.themeComments) this.themeComments = {};
                        Object.keys(reportData.theme_comments).forEach(themeId => {
                            const commentKey = student.id + '_' + themeId;
                            this.themeComments[commentKey] = reportData.theme_comments[themeId];
                        });
                    }
                    
                    // Load existing sub-theme comments
                    if (reportData.sub_theme_comments) {
                        if (!this.subThemeComments) this.subThemeComments = {};
                        Object.keys(reportData.sub_theme_comments).forEach(subThemeId => {
                            const commentKey = student.id + '_' + subThemeId;
                            this.subThemeComments[commentKey] = reportData.sub_theme_comments[subThemeId];
                        });
                    }
                }
            } catch (e) {
                // No existing report found, create new
            }
            
            // Initialize scores if not exists
            if (!this.scores[student.id]) {
                this.scores[student.id] = {};
                this.selectedTemplate.themes.forEach(theme => {
                    this.getSubThemes(theme).forEach(subTheme => {
                        this.scores[student.id][subTheme.id] = null;
                    });
                });
            }
            
            // Initialize additional data structures
            if (!this.themeComments) this.themeComments = {};
            if (!this.subThemeComments) this.subThemeComments = {};
            if (!this.teacherComments) this.teacherComments = {};
            if (!this.parentComments) this.parentComments = {};
            if (!this.physicalData) this.physicalData = {};
            if (!this.attendanceData) this.attendanceData = {};
            
            // Initialize physical data for student
            if (!this.physicalData[student.id]) {
                this.physicalData[student.id] = {
                    head_circumference: '',
                    height: '',
                    weight: ''
                };
            }
            
            // Initialize attendance data for student if not already loaded
            if (!this.attendanceData[student.id]) {
                this.attendanceData[student.id] = {
                    sick: 0,
                    permission: 0,
                    absent: 0,
                    present: 0,
                    total_sessions: 0
                };
            }
        },
        
        async saveStudentScore() {
            if (!this.selectedStudent) return;
            
            try {
                const payload = {
                    template_id: this.selectedTemplate.id,
                    student_id: this.selectedStudent.id,
                    scores: this.scores[this.selectedStudent.id] || {},
                    teacher_comment: this.teacherComments[this.selectedStudent.id] || '',
                    parent_comment: this.parentComments[this.selectedStudent.id] || '',
                    physical_data: this.physicalData[this.selectedStudent.id] || {},
                    attendance_data: this.attendanceData[this.selectedStudent.id] || {},
                    theme_comments: {},
                    sub_theme_comments: {}
                };
                
                // Add theme comments
                this.selectedTemplate.themes.forEach(theme => {
                    const commentKey = this.selectedStudent.id + '_' + theme.id;
                    if (this.themeComments[commentKey]) {
                        payload.theme_comments[theme.id] = this.themeComments[commentKey];
                    }
                });
                
                // Add sub-theme comments
                this.selectedTemplate.themes.forEach(theme => {
                    this.getSubThemes(theme).forEach(subTheme => {
                        const commentKey = this.selectedStudent.id + '_' + subTheme.id;
                        if (this.subThemeComments[commentKey] && this.subThemeComments[commentKey].trim() !== '') {
                            payload.sub_theme_comments[subTheme.id] = this.subThemeComments[commentKey];
                        }
                    });
                });
                
                await this.req(
                    `/rapor/classes/${this.classId}/reports`,
                    {
                        method: 'POST',
                        headers: { 'Content-Type':'application/json' },
                        body: JSON.stringify(payload)
                    }
                );
                
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Penilaian berhasil disimpan' }
                }));
                this.selectedStudent = null;
            } catch (e) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Gagal menyimpan penilaian', isError: true }
                }));
            }
        },
        
        deleteStudentScore(studentId) {
            // Create confirmation handler
            const confirmHandler = {
                submit: async () => {
                    try {
                        // Remove all related data for this student
                        delete this.scores[studentId];
                        delete this.teacherComments[studentId];
                        delete this.parentComments[studentId];
                        delete this.physicalData[studentId];
                        delete this.attendanceData[studentId];
                        
                        // Remove theme comments
                        this.selectedTemplate.themes.forEach(theme => {
                            const commentKey = studentId + '_' + theme.id;
                            delete this.themeComments[commentKey];
                            
                            // Remove sub-theme comments
                            this.getSubThemes(theme).forEach(subTheme => {
                                const subCommentKey = studentId + '_' + subTheme.id;
                                delete this.subThemeComments[subCommentKey];
                            });
                        });
                        
                        // Close confirmation dialog
                        window.dispatchEvent(new CustomEvent('close-confirmation'));
                        
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: 'Nilai siswa berhasil dihapus' }
                        }));
                    } catch (e) {
                        // Close confirmation dialog even on error
                        window.dispatchEvent(new CustomEvent('close-confirmation'));
                        
                        window.dispatchEvent(new CustomEvent('open-success', {
                            detail: { message: 'Gagal menghapus nilai siswa', isError: true }
                        }));
                    }
                }
            };
            
            window.dispatchEvent(new CustomEvent('open-confirmation', {
                detail: {
                    label: 'nilai siswa ini',
                    action: 'menghapus',
                    target: confirmHandler
                }
            }));
        },

        // Method to view a completed report
        async viewStudentReport(student) {
            if (!this.hasStudentReport(student.id)) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Siswa belum memiliki rapor', isError: true }
                }));
                return;
            }
            
            try {
                const reportResponse = await this.req(`/rapor/classes/${this.classId}/reports/${student.id}/${this.selectedTemplate.id}`);
                
                if (reportResponse.success && reportResponse.data) {
                    this.viewingReport = {
                        student: student,
                        template: this.selectedTemplate,
                        data: reportResponse.data
                    };
                    this.mode = 'view-report';
                } else {
                    window.dispatchEvent(new CustomEvent('open-success', {
                        detail: { message: 'Gagal memuat data rapor', isError: true }
                    }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Gagal memuat rapor', isError: true }
                }));
            }
        },

        // Method to close report view
        closeReportView() {
            this.viewingReport = null;
            this.mode = 'score';
        },

        // Method to get score display for view mode
        getScoreDisplay(scores, subThemeId) {
            if (!scores || !scores[subThemeId]) return '-';
            return scores[subThemeId];
        },

        // Method to download PDF report
        async downloadReportPDF() {
            if (!this.viewingReport) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Tidak ada laporan yang sedang dilihat', isError: true }
                }));
                return;
            }
            
            try {
                const { student, template } = this.viewingReport;
                const pdfUrl = `/rapor/classes/${this.classId}/reports/${student.id}/${template.id}/pdf`;
                
                // Open the print-ready page in a new tab
                window.open(pdfUrl, '_blank');
            } catch (e) {
                window.dispatchEvent(new CustomEvent('open-success', {
                    detail: { message: 'Gagal membuka halaman cetak', isError: true }
                }));
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
<div x-data="raporApp({{ $class->id }})" x-init="init()" class="p-2">

    {{-- LOADING --}}
    <div x-show="loading" class="flex justify-center items-center h-40 text-sky-600">Memuat…</div>

    {{-- ERROR --}}
    <div x-show="!loading && error" class="bg-red-50 border border-red-200 p-4 rounded-lg">
        <div class="text-red-600" x-text="error"></div>
        <button @click="init()" class="mt-2 px-3 py-1 bg-red-600 text-white rounded text-sm">Coba lagi</button>
    </div>

 {{-- ===================== MAIN VIEW ===================== --}}
    <div x-show="!loading && !error && mode==='view'" x-cloak class="space-y-3 overflow-y-auto max-h-[60vh] md:max-h-[55vh] hide-scrollbar">
        {{-- Header --}}
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">Template Rapor Kelas</h1>
            
            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button class="flex items-center gap-2 bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700" 
                        @click="newTemplate()">
                    <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                        <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    Tambah Template
                </button>
                
                <button x-show="assignedTemplates.length > 0"
                        class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" 
                        @click="showTemplateSelection()">
                    <svg width="20" height="20" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 10V30" stroke="white" stroke-width="4" stroke-linecap="round" />
                        <path d="M10 20H30" stroke="white" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    Tambah Rapor
                </button>
            </div>
        </div>

        {{-- Assigned Templates Section --}}
        <div x-show="assignedTemplates.length > 0" class="space-y-3">
            <h3 class="text-sm font-medium text-gray-700">Template yang Sudah Ditetapkan:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="assignedTemplate in assignedTemplates" :key="'assigned-' + assignedTemplate.id">
                    <div x-show="assignedTemplate.title && assignedTemplate.title.trim() !== '' && assignedTemplate.id"
                         class="bg-white border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-green-800" x-text="assignedTemplate.title"></h3>
                                <p class="text-sm text-gray-500" x-text="assignedTemplate.semester_type ? `Semester ${assignedTemplate.semester_type}` : ''"></p>
                                <p class="text-sm text-gray-600 mt-2" x-text="assignedTemplate.description"></p>
                            </div>
                            <div class="flex flex-col gap-2 ml-4">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Template Aktif</span>
                                <button @click.prevent.stop="selectTemplateForReport(assignedTemplate)"
                                        class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                    Buat Rapor
                                </button>
                                <button @click.prevent.stop="removeAssignedTemplate(assignedTemplate.id)"
                                        class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Available Templates Section --}}
      <div x-show="templates.length > 0" class="space-y-3 overflow-y-auto max-h-[60vh] md:max-h-[47vh] hide-scrollbar">
            <h3 class="text-sm font-medium text-gray-700">Template Tersedia:</h3>
            
            {{-- Templates Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="(templateItem, templateIndex) in templates" :key="'template-card-' + templateItem.id">
                    <article x-show="templateItem.title && templateItem.title.trim() !== ''" 
                             class="p-4 bg-white border border-sky-500 rounded-xl flex flex-col justify-between cursor-pointer hover:shadow"
                             @click="previewTemplate(templateItem)">
                        <div>
                            <h2 class="font-bold text-sky-800 truncate" x-text="templateItem.title || 'Template Tanpa Judul'"></h2>
                            <p class="text-sm text-gray-500" x-text="templateItem.semester_type ? `Semester ${templateItem.semester_type}` : 'Tidak ada semester'"></p>
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
                    <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700" 
                            @click.stop="editTemplate(templateItem)">Edit</button>
                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" 
                            @click.stop="deleteTemplate(templateItem.id)">Hapus</button>
                </div>
            </div>
        </article>
    </template>
</div>
        </div>

        {{-- Empty State for Available Templates --}}
        <div x-show="templates.length === 0 && assignedTemplates.length > 0" class="text-center py-8 text-gray-500">
            <p>Semua template telah ditetapkan untuk kelas ini</p>
        </div>

        {{-- Empty State for No Templates --}}
        <div x-show="assignedTemplates.length === 0 && templates.length === 0" class="text-center py-8 text-gray-500">
            <p>Data template masih kosong</p>
            <p class="text-sm mt-2">Silakan buat template baru untuk memulai</p>
        </div>
    </div>

    {{-- ===================== TEMPLATE SELECTION FOR REPORT ===================== --}}
    <div x-show="!loading && !error && mode==='select-template'" x-cloak class="space-y-3 overflow-y-auto max-h-[60vh] md:max-h-[55vh] hide-scrollbar">>
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">Pilih Template untuk Rapor</h1>
            <button class="text-gray-600 hover:text-gray-800" @click="mode='view'">
                ← Kembali
            </button>
        </div>
        
        <div x-show="assignedTemplates.length === 0" class="text-center py-8 text-gray-500">
            <p>Belum ada template yang ditetapkan untuk kelas ini</p>
            <p class="text-sm mt-2">Silakan tetapkan template terlebih dahulu</p>
        </div>

        <div x-show="assignedTemplates.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template x-for="template in assignedTemplates" :key="'select-' + template.id">
                <div class="p-4 bg-white border border-green-200 rounded-xl cursor-pointer hover:shadow-lg hover:border-green-400"
                     @click="selectTemplateForReport(template)">
                    <div>
                        <h2 class="font-bold text-green-800" x-text="template.title"></h2>
                        <p class="text-sm text-gray-500" x-text="template.semester_type ? `Semester ${template.semester_type}` : ''"></p>
                        <p class="text-sm text-gray-600 mt-2" x-text="template.description"></p>
                    </div>
                    <div class="mt-3 text-right">
                        <span class="text-xs text-green-600">Klik untuk buat rapor</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ===================== TEMPLATE PREVIEW ===================== --}}
    <div x-show="!loading && !error && mode==='preview'" x-cloak class="space-y-3 overflow-y-auto max-h-[60vh] md:max-h-[55vh] hide-scrollbar">
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
            <div class="flex justify-between gap-3 pt-4 border-t">
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" 
                            @click="editTemplate(selectedTemplate)">
                        Edit Template
                    </button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" 
                            @click="deleteTemplate(selectedTemplate.id)">
                        Hapus Template
                    </button>
                </div>
                <div class="flex gap-2">
                    <button class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm" 
                            @click="cancelPreview()">
                        Batal
                    </button>
                    <button class="px-6 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700" 
                            @click="confirmAssignTemplate()">
                        Gunakan Template Ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== SCORING VIEW ===================== --}}
    <div x-show="!loading && !error && mode==='score'" x-cloak class="space-y-3 overflow-y-auto max-h-[60vh] md:max-h-[55vh] hide-scrollbar">>
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">
                Penilaian Siswa - <span x-text="selectedTemplate?.title"></span>
            </h1>
            <button class="text-gray-600 hover:text-gray-800" @click="mode='view'">
                ← Kembali
            </button>
        </div>

        {{-- Template Info --}}
        <div class="bg-white border border-sky-200 rounded-lg p-4">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="font-bold text-sky-800" x-text="selectedTemplate?.title"></h2>
                    <p class="text-sm text-gray-600" x-text="selectedTemplate?.description"></p>
                    <p class="text-sm text-gray-500 mt-1" x-text="selectedTemplate?.semester_type ? `Semester ${selectedTemplate.semester_type}` : ''"></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Jumlah Siswa: <span class="font-medium" x-text="students.length"></span></p>
                </div>
            </div>
        </div>

        {{-- Student List for Selection --}}
        <div x-show="students.length > 0 && !selectedStudent" class="space-y-3">
            <h3 class="text-sm font-medium text-gray-700">Pilih Siswa untuk Dinilai:</h3>
            
            {{-- Header --}}
            <div class="bg-sky-200 rounded-t-lg">
                <div class="grid grid-cols-3 gap-4 p-3">
                    <h3 class="text-sm font-medium text-center text-slate-600">Nama Lengkap</h3>
                    <h3 class="text-sm font-medium text-center text-slate-600">Status</h3>
                    <h3 class="text-sm font-medium text-center text-slate-600">Pilihan</h3>
                </div>
            </div>
            
            {{-- Student Rows --}}
            <template x-for="student in students" :key="'student-select-' + student.id">
                <div class="grid grid-cols-3 gap-4 p-3 border border-gray-200 bg-white">
                    <div class="text-sm text-center text-slate-600" x-text="student.name"></div>
                    <div class="text-sm text-center text-slate-600">
                        <span x-text="hasStudentReport(student.id) ? 'Sudah Dinilai' : 'Belum Dinilai'"></span>
                    </div>
                    <div class="flex flex-col gap-1 items-center">
                        <button class="w-20 text-xs font-medium bg-transparent rounded-lg border border-sky-300 text-slate-600 h-[25px]"
                                @click="openStudentScoring(student)">
                            <span x-text="hasStudentReport(student.id) ? 'Edit' : 'Nilai'"></span>
                        </button>
                        <button x-show="hasStudentReport(student.id)" 
                                class="w-20 text-xs font-medium bg-transparent rounded-lg border border-green-400 text-green-700 h-[25px]"
                                @click="viewStudentReport(student)">
                            Lihat
                        </button>
                        <button x-show="hasStudentReport(student.id)" 
                                class="w-20 text-xs font-medium bg-transparent rounded-lg border border-red-400 text-red-700 h-[25px]"
                                @click="deleteStudentScore(student.id)">
                            Hapus
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Individual Student Scoring Form --}}
        <div x-show="selectedStudent" class="space-y-4">
            {{-- Student Header --}}
            <div class="flex justify-between items-center bg-white border border-sky-200 rounded-lg p-4">
                <div>
                    <h3 class="font-semibold text-gray-800" x-text="selectedStudent?.name"></h3>
                    <p class="text-sm text-gray-500" x-text="selectedStudent?.nisn ? `NISN: ${selectedStudent.nisn}` : ''"></p>
                </div>
                <button class="text-gray-600 hover:text-gray-800" @click="selectedStudent = null">
                    ← Kembali ke Daftar Siswa
                </button>
            </div>

            {{-- Scoring Table --}}
            <div class="bg-white border border-sky-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-sky-600 rounded-xl text-xs md:text-sm bg-white">
                        <thead class="bg-sky-100 text-sky-800">
                            <tr>
                                <th class="px-1 py-2 border border-sky-200 font-bold text-center" style="width: 40px;">No</th>
                                <th class="px-3 py-2 border border-sky-200 font-bold text-left" style="width: 200px;">KOMPETENSI DASAR</th>
                                <th class="px-1 py-2 border border-sky-200 font-bold text-center" style="width: 32px;">BM</th>
                                <th class="px-1 py-2 border border-sky-200 font-bold text-center" style="width: 32px;">MM</th>
                                <th class="px-1 py-2 border border-sky-200 font-bold text-center" style="width: 32px;">BSH</th>
                                <th class="px-1 py-2 border border-sky-200 font-bold text-center" style="width: 32px;">BSB</th>
                                <th class="px-3 py-2 border border-sky-200 font-bold text-left" style="width: 300px;">CATATAN GURU</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop through each theme --}}
                            <template x-for="(theme, themeIndex) in selectedTemplate?.themes?.sort((a,b) => (a.order || a.id) - (b.order || b.id)) || []" :key="'theme-' + theme.id">
                                <tr>
                                    <td colspan="7" class="p-0 border-0">
                                        {{-- Theme Header Table --}}
                                        <table class="w-full border-collapse">
                                            <tr class="bg-sky-50">
                                                <td class="px-1 py-2 border border-sky-100 font-bold text-sky-700 text-center" style="width: 40px;" x-text="themeIndex + 1"></td>
                                                <td class="px-3 py-2 border border-sky-100 font-bold text-sky-800" style="width: 200px;" x-text="theme.name"></td>
                                                <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;"></td>
                                                <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;"></td>
                                                <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;"></td>
                                                <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;"></td>
                                                <td class="px-3 py-2 border border-sky-100 bg-sky-100" style="width: 300px;">
                                                    <textarea rows="2" 
                                                              :placeholder="`Catatan untuk tema ${theme.name}`"
                                                              x-model="themeComments[selectedStudent.id + '_' + theme.id]"
                                                              class="w-full px-2 py-1 border rounded text-xs resize-none"></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        {{-- Sub-theme Rows Table --}}
                                        <table class="w-full border-collapse">
                                            <template x-for="(subTheme, subIndex) in getSubThemes(theme).sort((a,b) => (a.order || a.id) - (b.order || b.id))" :key="'sub-' + theme.id + '-' + subTheme.id">
                                                <tr :class="subIndex % 2 === 0 ? 'bg-gray-50' : 'bg-white'">
                                                    <td class="px-1 py-2 border border-sky-100 text-center text-gray-600" style="width: 40px;" x-text="`${theme.code || 'T' + (themeIndex + 1)}.${subIndex + 1}`"></td>
                                                    <td class="px-3 py-2 border border-sky-100" style="width: 200px;" x-text="subTheme.name"></td>
                                                    
                                                    {{-- BM Radio --}}
                                                    <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;">
                                                        <label class="custom-radio-checklist">
                                                            <input type="radio" 
                                                                   :name="`score_${selectedStudent.id}_${subTheme.id}`"
                                                                   value="BM"
                                                                   x-model="scores[selectedStudent.id][subTheme.id]">
                                                            <span class="check-icon">✓</span>
                                                            <span class="box"></span>
                                                        </label>
                                                    </td>
                                                    
                                                    {{-- MM Radio --}}
                                                    <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;">
                                                        <label class="custom-radio-checklist">
                                                            <input type="radio" 
                                                                   :name="`score_${selectedStudent.id}_${subTheme.id}`"
                                                                   value="MM"
                                                                   x-model="scores[selectedStudent.id][subTheme.id]">
                                                            <span class="check-icon">✓</span>
                                                            <span class="box"></span>
                                                        </label>
                                                    </td>
                                                    
                                                    {{-- BSH Radio --}}
                                                    <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;">
                                                        <label class="custom-radio-checklist">
                                                            <input type="radio" 
                                                                   :name="`score_${selectedStudent.id}_${subTheme.id}`"
                                                                   value="BSH"
                                                                   x-model="scores[selectedStudent.id][subTheme.id]">
                                                            <span class="check-icon">✓</span>
                                                            <span class="box"></span>
                                                        </label>
                                                    </td>
                                                    
                                                    {{-- BSB Radio --}}
                                                    <td class="px-1 py-2 border border-sky-100 text-center" style="width: 32px;">
                                                        <label class="custom-radio-checklist">
                                                            <input type="radio" 
                                                                   :name="`score_${selectedStudent.id}_${subTheme.id}`"
                                                                   value="BSB"
                                                                   x-model="scores[selectedStudent.id][subTheme.id]">
                                                            <span class="check-icon">✓</span>
                                                            <span class="box"></span>
                                                        </label>
                                                    </td>
                                                    
                                                    {{-- Notes Column --}}
                                                    <td class="px-3 py-2 border border-sky-100" style="width: 300px;">
                                                        <textarea rows="1" 
                                                                  :placeholder="`Catatan untuk ${subTheme.name}`"
                                                                  x-model="subThemeComments[selectedStudent.id + '_' + subTheme.id]"
                                                                  class="w-full px-2 py-1 border rounded text-xs resize-none"></textarea>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Additional Data Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Physical Measurements --}}
                <div class="bg-white border border-sky-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-3">Data Fisik</h4>
                    <table class="w-full table-fixed border text-xs">
                        <tr>
                            <td class="border px-2 py-1">Lingkar Kepala</td>
                            <td class="border px-2 py-1">
                                <input type="text" x-model="physicalData[selectedStudent.id].head_circumference" class="w-full border-0 text-xs" placeholder="cm">
                            </td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Tinggi Badan</td>
                            <td class="border px-2 py-1">
                                <input type="text" x-model="physicalData[selectedStudent.id].height" class="w-full border-0 text-xs" placeholder="cm">
                            </td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Berat Badan</td>
                            <td class="border px-2 py-1">
                                <input type="text" x-model="physicalData[selectedStudent.id].weight" class="w-full border-0 text-xs" placeholder="kg">
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Attendance --}}
                <div class="bg-white border border-sky-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-3">Kehadiran</h4>
                    <table class="w-full table-fixed border text-xs">
                        <tr>
                            <td class="border px-2 py-1">Sakit</td>
                            <td class="border px-2 py-1">
                                <span x-text="attendanceData[selectedStudent.id]?.sick || 0"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Izin</td>
                            <td class="border px-2 py-1">
                                <span x-text="attendanceData[selectedStudent.id]?.permission || 0"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">Alpa</td>
                            <td class="border px-2 py-1">
                                <span x-text="attendanceData[selectedStudent.id]?.absent || 0"></span>
                            </td>
                        </tr>
                        <tr class="bg-green-50">
                            <td class="border px-2 py-1 font-medium">Hadir</td>
                            <td class="border px-2 py-1 font-medium text-green-700">
                                <span x-text="attendanceData[selectedStudent.id]?.present || 0"></span>
                            </td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="border px-2 py-1 font-medium">Total Sesi</td>
                            <td class="border px-2 py-1 font-medium text-blue-700">
                                <span x-text="attendanceData[selectedStudent.id]?.total_sessions || 0"></span>
                            </td>
                        </tr>
                    </table>
                    
                    {{-- Attendance Statistics --}}
                    <div class="mt-2 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Persentase Kehadiran:</span>
                            <span class="font-medium" 
                                  :class="(attendanceData[selectedStudent.id]?.present || 0) / Math.max(attendanceData[selectedStudent.id]?.total_sessions || 1, 1) >= 0.8 ? 'text-green-600' : 'text-red-600'"
                                  x-text="attendanceData[selectedStudent.id]?.total_sessions > 0 ? Math.round((attendanceData[selectedStudent.id]?.present || 0) / attendanceData[selectedStudent.id].total_sessions * 100) + '%' : '0%'"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Score Legend --}}
            <div class="bg-white border border-sky-200 rounded-lg p-4">
                <div class="text-xs text-gray-600">
                    <p><strong>Keterangan:</strong></p>
                    <p>BM : Belum Muncul</p>
                    <p>MM : Mulai Muncul</p>
                    <p>BSH : Berkembang Sesuai Harapan</p>
                    <p>BSB : Berkembang Sangat Baik</p>
                </div>
            </div>

            {{-- Teacher and Parent Comments --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white border border-sky-200 rounded-lg p-4">
                <div>
                    <label class="block text-sm font-semibold text-sky-700 mb-2">Pesan Guru</label>
                    <textarea x-model="teacherComments[selectedStudent.id]"
                              class="w-full border border-sky-300 rounded-lg px-3 py-2 text-xs resize-none focus:outline-none focus:ring-2 focus:ring-sky-400"
                              rows="4"
                              placeholder="Tulis pesan guru di sini..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-sky-700 mb-2">Pesan Orang Tua</label>
                    <textarea x-model="parentComments[selectedStudent.id]"
                              class="w-full border border-sky-300 rounded-lg px-3 py-2 text-xs resize-none focus:outline-none focus:ring-2 focus:ring-sky-400"
                              rows="4"
                              placeholder="Tulis pesan orang tua di sini..."></textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 pt-4 bg-white border border-sky-200 rounded-lg p-4">
                <button class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" 
                        @click="selectedStudent = null">
                    Batal
                </button>
                <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" 
                        @click="saveStudentScore()">
                    Simpan Penilaian
                </button>
            </div>
        </div>

        {{-- Empty State for No Students --}}
        <div x-show="students.length === 0" class="text-center py-8 text-gray-500">
            <p>Belum ada siswa terdaftar di kelas ini</p>
            <p class="text-sm mt-2">Silakan tambahkan siswa terlebih dahulu</p>
        </div>
    </div>

    {{-- ===================== VIEW REPORT MODE ===================== --}}
    <div x-show="!loading && !error && mode==='view-report'" x-cloak class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">
                Laporan Penilaian - <span x-text="viewingReport?.student?.name"></span>
            </h1>
            <button class="text-gray-600 hover:text-gray-800" @click="closeReportView()">
                ← Kembali
            </button>
        </div>

        <div x-show="viewingReport" class="space-y-4">
            {{-- Student & Template Info --}}
            <div class="bg-white border border-sky-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Data Siswa</h3>
                        <p class="text-sm text-gray-600">Nama: <span class="font-medium" x-text="viewingReport?.student?.name"></span></p>
                        <p class="text-sm text-gray-600" x-show="viewingReport?.student?.nisn">NISN: <span class="font-medium" x-text="viewingReport?.student?.nisn"></span></p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Template Penilaian</h3>
                        <p class="text-sm text-gray-600">Judul: <span class="font-medium" x-text="viewingReport?.template?.title"></span></p>
                        <p class="text-sm text-gray-600">Semester: <span class="font-medium" x-text="viewingReport?.template?.semester_type"></span></p>
                    </div>
                </div>
            </div>

            {{-- Assessment Results Table --}}
            <div class="bg-white border border-sky-200 rounded-lg overflow-hidden">
                <div class="bg-sky-100 p-3">
                    <h3 class="font-semibold text-sky-800">Hasil Penilaian</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-sky-600 rounded-xl text-xs md:text-sm bg-white">
                        <thead class="bg-sky-100 text-sky-800">
                            <tr>
                                <th class="px-1 py-2 border border-sky-200 font-bold text-center" style="width: 40px;">No</th>
                                <th class="px-3 py-2 border border-sky-200 font-bold text-left" style="width: 250px;">KOMPETENSI DASAR</th>
                                <th class="px-3 py-2 border border-sky-200 font-bold text-center" style="width: 80px;">PENILAIAN</th>
                                <th class="px-3 py-2 border border-sky-200 font-bold text-left" style="width: 300px;">CATATAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop through themes and subthemes for view mode --}}
                            <template x-for="(theme, themeIndex) in viewingReport?.template?.themes?.sort((a,b) => (a.order || a.id) - (b.order || b.id)) || []" :key="'view-theme-' + theme.id">
                                <tr>
                                    <td colspan="4" class="p-0 border-0">
                                        {{-- Theme Header --}}
                                        <table class="w-full border-collapse">
                                            <tr class="bg-sky-50">
                                                <td class="px-1 py-2 border border-sky-100 font-bold text-sky-700 text-center" style="width: 40px;" x-text="themeIndex + 1"></td>
                                                <td class="px-3 py-2 border border-sky-100 font-bold text-sky-800" style="width: 250px;" x-text="theme.name"></td>
                                                <td class="px-3 py-2 border border-sky-100 text-center font-bold text-sky-700" style="width: 80px;">TEMA</td>
                                                <td class="px-3 py-2 border border-sky-100 bg-sky-100 text-sm" style="width: 300px;" x-text="viewingReport?.data?.theme_comments?.[theme.id] || '-'"></td>
                                            </tr>
                                        </table>
                                        
                                        {{-- Sub-theme Rows --}}
                                        <table class="w-full border-collapse">
                                            <template x-for="(subTheme, subIndex) in getSubThemes(theme).sort((a,b) => (a.order || a.id) - (b.order || b.id))" :key="'view-sub-' + theme.id + '-' + subTheme.id">
                                                <tr :class="subIndex % 2 === 0 ? 'bg-gray-50' : 'bg-white'">
                                                    <td class="px-1 py-2 border border-sky-100 text-center text-gray-600" style="width: 40px;" x-text="`${theme.code || 'T' + (themeIndex + 1)}.${subIndex + 1}`"></td>
                                                    <td class="px-3 py-2 border border-sky-100" style="width: 250px;">
                                                        <div class="font-medium" x-text="subTheme.name"></div>
                                                        <div class="text-xs text-gray-500 mt-1" x-show="subTheme.description" x-text="subTheme.description"></div>
                                                    </td>
                                                    <td class="px-3 py-2 border border-sky-100 text-center font-semibold" style="width: 80px;"
                                                        :class="{
                                                            'text-red-600': getScoreDisplay(viewingReport?.data?.scores, subTheme.id) === 'BM',
                                                            'text-yellow-600': getScoreDisplay(viewingReport?.data?.scores, subTheme.id) === 'MM',
                                                            'text-blue-600': getScoreDisplay(viewingReport?.data?.scores, subTheme.id) === 'BSH',
                                                            'text-green-600': getScoreDisplay(viewingReport?.data?.scores, subTheme.id) === 'BSB'
                                                        }"
                                                        x-text="getScoreDisplay(viewingReport?.data?.scores, subTheme.id)"></td>
                                                    <td class="px-3 py-2 border border-sky-100 text-sm" style="width: 300px;" x-text="viewingReport?.data?.sub_theme_comments?.[subTheme.id] || '-'"></td>
                                                </tr>
                                            </template>
                                        </table>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Physical & Attendance Data --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Physical Measurements --}}
                <div class="bg-white border border-sky-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-3">Data Fisik</h4>
                    <table class="w-full table-fixed border text-sm">
                        <tr>
                            <td class="border px-2 py-1 bg-gray-50 font-medium">Lingkar Kepala</td>
                            <td class="border px-2 py-1" x-text="viewingReport?.data?.physical_data?.head_circumference || '-'"></td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1 bg-gray-50 font-medium">Tinggi Badan</td>
                            <td class="border px-2 py-1" x-text="viewingReport?.data?.physical_data?.height || '-'"></td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1 bg-gray-50 font-medium">Berat Badan</td>
                            <td class="border px-2 py-1" x-text="viewingReport?.data?.physical_data?.weight || '-'"></td>
                        </tr>
                    </table>
                </div>

                {{-- Attendance Data --}}
                <div class="bg-white border border-sky-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-3">Data Kehadiran</h4>
                    <table class="w-full table-fixed border text-sm">
                        <tr>
                            <td class="border px-2 py-1 bg-gray-50 font-medium">Sakit</td>
                            <td class="border px-2 py-1" x-text="viewingReport?.data?.attendance_data?.sick || '0'"></td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1 bg-gray-50 font-medium">Izin</td>
                            <td class="border px-2 py-1" x-text="viewingReport?.data?.attendance_data?.permission || '0'"></td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1 bg-gray-50 font-medium">Alpa</td>
                            <td class="border px-2 py-1" x-text="viewingReport?.data?.attendance_data?.absent || '0'"></td>
                        </tr>
                        <tr class="bg-green-50">
                            <td class="border px-2 py-1 font-medium text-green-700">Hadir</td>
                            <td class="border px-2 py-1 font-medium text-green-700" x-text="viewingReport?.data?.attendance_data?.present || '0'"></td>
                        </tr>
                        <tr class="bg-blue-50">
                            <td class="border px-2 py-1 font-medium text-blue-700">Total Sesi</td>
                            <td class="border px-2 py-1 font-medium text-blue-700" x-text="viewingReport?.data?.attendance_data?.total_sessions || '0'"></td>
                        </tr>
                        <tr class="bg-yellow-50">
                            <td class="border px-2 py-1 font-medium text-yellow-700">Persentase Kehadiran</td>
                            <td class="border px-2 py-1 font-medium text-yellow-700" 
                                x-text="viewingReport?.data?.attendance_data?.total_sessions > 0 ? Math.round((viewingReport?.data?.attendance_data?.present || 0) / viewingReport.data.attendance_data.total_sessions * 100) + '%' : '0%'"></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Comments Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white border border-sky-200 rounded-lg p-4">
                <div>
                    <h4 class="font-medium text-gray-800 mb-3">Pesan Guru</h4>
                    <div class="bg-gray-50 border rounded-lg p-3 min-h-[100px]">
                        <p class="text-sm text-gray-700" x-text="viewingReport?.data?.teacher_comment || 'Tidak ada pesan dari guru'"></p>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-3">Pesan Orang Tua</h4>
                    <div class="bg-gray-50 border rounded-lg p-3 min-h-[100px]">
                        <p class="text-sm text-gray-700" x-text="viewingReport?.data?.parent_comment || 'Tidak ada pesan dari orang tua'"></p>
                    </div>
                </div>
            </div>

            {{-- Score Legend --}}
            <div class="bg-white border border-sky-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-800 mb-3">Keterangan Penilaian</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-red-100 text-red-600 rounded flex items-center justify-center font-bold">BM</span>
                        <span class="text-gray-700">Belum Muncul</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded flex items-center justify-center font-bold">MM</span>
                        <span class="text-gray-700">Mulai Muncul</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center font-bold">BSH</span>
                        <span class="text-gray-700">Berkembang Sesuai Harapan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-green-100 text-green-600 rounded flex items-center justify-center font-bold">BSB</span>
                        <span class="text-gray-700">Berkembang Sangat Baik</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-between gap-3 pt-4 bg-white border border-sky-200 rounded-lg p-4">
                <button class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" 
                        @click="closeReportView()">
                    Tutup
                </button>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" 
                            @click="openStudentScoring(viewingReport.student); closeReportView();">
                        Edit Penilaian
                    </button>
                    <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" 
                            @click="downloadReportPDF()">
                        Cetak Rapor
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== ADD TEMPLATE FORM ===================== --}}
    <div x-show="!loading && !error && mode==='add'" x-cloak class="space-y-4">
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

    {{-- ===================== EDIT TEMPLATE FORM ===================== --}}
    <div x-show="!loading && !error && mode==='edit'" x-cloak class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-semibold text-sky-700">Edit Template Rapor</h1>
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
                    <template x-for="(theme, themeIndex) in newTemplateForm.themes" :key="'edit-theme-form-' + themeIndex">
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
                                    <template x-for="(subTheme, subThemeIndex) in theme.subThemes" :key="'edit-subtheme-form-' + themeIndex + '-' + subThemeIndex">
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
                        @click="saveEditedTemplate()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

{{-- Custom Alert Components --}}
<x-alert.success-alert />
<x-alert.confirmation-alert />

