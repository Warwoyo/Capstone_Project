@extends('layouts.app')

@section('content')
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css"
/>

<main class="p-5 mx-auto max-w-none bg-white max-md:max-w-[991px] max-sm:max-w-screen-sm" x-data="modulePage()">
    {{-- Module List --}}
    <section class="flex flex-col gap-4">
        <template x-for="module in modulesList" :key="module.id">
            <article class="flex justify-between items-center p-4 rounded border border-solid border-gray-200 max-sm:flex-col max-sm:gap-4 max-sm:items-start">
                <div class="flex gap-4 items-center">
                    <i class="ti ti-file text-2xl text-gray-500"></i>
                    <div class="flex flex-col gap-1.5">
                        <p class="text-xs text-gray-500">Single File</p>
                        <h3 class="text-sm text-gray-900" x-text="module.title"></h3>
                        <time class="text-xs text-gray-500" x-text="'Created at ' + module.createdAt"></time>
                    </div>
                </div>
                <div class="flex gap-4 max-sm:self-end">
                </div>
            </article>
        </template>
    </section>
</main>

<script>
    function modulePage() {
        return {
            isAdding: false,
            newModule: {
                title: ''
            },
            modulesList: [
                {
                    id: 1,
                    title: 'Modul Pembelajaran Bootcamp',
                    createdAt: '21 Mei 2025'
                }
            ],
            toggleAddMode() {
                this.isAdding = !this.isAdding;
                if (!this.isAdding) {
                    this.newModule.title = '';
                    this.$refs.fileInput.value = '';
                }
            },
            submitModule() {
                if (!this.newModule.title || !this.$refs.fileInput.files.length) {
                    alert('Mohon isi semua field dan upload file PDF.');
                    return;
                }

                const newId = Date.now();
                const fileName = this.$refs.fileInput.files[0].name;
                const createdAt = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

                this.modulesList.push({
                    id: newId,
                    title: `${this.newModule.title} (${fileName})`,
                    createdAt
                });

                this.toggleAddMode();
            },
            deleteModule(id) {
                this.modulesList = this.modulesList.filter(m => m.id !== id);
            }
        };
    }
</script>
@endsection
