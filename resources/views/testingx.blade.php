@extends('layouts.app')

@section('content')
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css"
/>

<main class="p-5 mx-auto max-w-none bg-white max-md:max-w-[991px] max-sm:max-w-screen-sm" x-data="modulePage()">
    {{-- Action Buttons --}}
    <div class="flex gap-2.5 justify-end max-md:flex-wrap mb-5">
        <button
            @click="toggleAddMode"
            class="px-4 py-2 text-sm text-white bg-cyan-600 rounded cursor-pointer border-none max-md:flex-1"
            x-text="isAdding ? 'Batal' : '+ Add Module'"
        ></button>
    </div>

    {{-- Add Module Form --}}
    <div x-show="isAdding" class="border border-gray-300 rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3 text-gray-700">Tambah Modul</h2>
        <form @submit.prevent="submitModule">
            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-gray-700">Judul Modul</label>
                <input type="text" x-model="newModule.title" class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-gray-700">Upload File (PDF)</label>
                <input type="file" accept=".pdf" x-ref="fileInput" class="w-full border border-gray-300 rounded p-2 bg-white" required>
            </div>
            <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded text-sm">
                Simpan Modul
            </button>
        </form>
    </div>

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
                    <button aria-label="Delete module" @click="deleteModule(module.id)">
                        <i class="ti ti-trash text-lg text-gray-500 cursor-pointer"></i>
                    </button>
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
