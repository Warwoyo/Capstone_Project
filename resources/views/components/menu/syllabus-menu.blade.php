@props([
    'classroom' => null,
    'syllabusList' => null
])

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css"
/>

<main class="pl-5 pr-5 pt-1 mx-auto max-w-none bg-white max-md:max-w-[991px] max-sm:max-w-screen-sm" x-data="modulePage()">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-transition
            class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded"
        >
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Action Buttons --}}
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'teacher')
    <div class="flex gap-2.5 justify-end max-md:flex-wrap mb-2">
        <button
            @click="toggleAddMode"
            class="px-6 py-2 bg-sky-600 text-white rounded-full hover:bg-sky-700"
            x-text="isAdding ? 'Batal' : '+ Add Module'"
        ></button>
    </div>
    @endif

    {{-- Add Module Form --}}
    <div x-show="isAdding" x-cloak class="p-4 w-full bg-white border border-sky-600 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200">
        <h2 class="text-lg font-semibold mb-3 text-gray-700">Tambah Modul</h2>
        <form action="{{ route('syllabus.store', $classroom->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-gray-700">Judul Modul</label>
                <input type="text" name="title" class="w-full p-2 border border-blue-300 rounded-full" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-gray-700">Upload File (PDF)</label>
                <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-blue-300 rounded-full p-2 bg-white" required>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-sky-600 text-white rounded-full hover:bg-sky-700">
                    Simpan Modul
                </button>
                <button type="button" @click="toggleAddMode" class="bg-red-500 text-white px-4 py-2 rounded-full text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- Module List --}}
    <div 
        class="overflow-y-auto hide-scrollbar max-h-[62vh] md:max-h-[54vh]" 
        x-show="!isAdding"
        x-transition
    >
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($syllabusList as $syllabus)
                <article class="flex flex-col justify-between p-4 w-full bg-white border border-sky-600 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex gap-4 items-center">
                        <i class="ti ti-file-type-pdf text-2xl text-red-500"></i>
                        <div class="flex flex-col gap-1.5">
                            <p class="text-xs text-gray-500">PDF File</p>
                            <h3 class="text-sm text-gray-900">{{ $syllabus->title }}</h3>
                            <time class="text-xs text-gray-500">Created at {{ $syllabus->created_at->format('d M Y') }}</time>
                        </div>
                    </div>
                    <div class="flex gap-4 max-sm:self-end">
                        <a href="{{ route('syllabus.view', $syllabus->id) }}" target="_blank" aria-label="View PDF" class="text-cyan-600 hover:text-cyan-800">
                            <i class="ti ti-eye text-lg cursor-pointer"></i>
                        </a>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'teacher')
                        <form action="{{ route('syllabus.destroy', $syllabus->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Delete module" class="text-red-500 hover:text-red-700">
                                <i class="ti ti-trash text-lg cursor-pointer"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="text-center py-8 text-gray-500 col-span-1 md:col-span-2">
                    <i class="ti ti-file-off text-4xl mb-2"></i>
                    <p>Belum ada modul silabus yang tersedia</p>
                </div>
            @endforelse
        </section>
    </div>
</main>

<script>
    function modulePage() {
        return {
            isAdding: false,
            toggleAddMode() {
                this.isAdding = !this.isAdding;
            }
        };
    }
</script>
