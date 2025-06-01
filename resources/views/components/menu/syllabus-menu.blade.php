@props([
    'classroom' => null,
    'syllabusList' => null
])

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css"
/>

<main class="p-5 mx-auto max-w-none bg-white max-md:max-w-[991px] max-sm:max-w-screen-sm" x-data="modulePage()">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
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
    <div class="flex gap-2.5 justify-end max-md:flex-wrap mb-5">
        <button
            @click="toggleAddMode"
            class="px-4 py-2 text-sm text-white bg-cyan-600 rounded cursor-pointer border-none max-md:flex-1"
            x-text="isAdding ? 'Batal' : '+ Add Module'"
        ></button>
    </div>
    @endif

    {{-- Add Module Form --}}
    <div x-show="isAdding" class="border border-gray-300 rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3 text-gray-700">Tambah Modul</h2>
        <form action="{{ route('syllabus.store', $classroom->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-gray-700">Judul Modul</label>
                <input type="text" name="title" class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-gray-700">Upload File (PDF)</label>
                <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 rounded p-2 bg-white" required>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded text-sm">
                    Simpan Modul
                </button>
                <button type="button" @click="toggleAddMode" class="bg-gray-500 text-white px-4 py-2 rounded text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- Module List --}}
    <section class="flex flex-col gap-4">
        @forelse($syllabusList as $syllabus)
            <article class="flex justify-between items-center p-4 rounded border border-solid border-gray-200 max-sm:flex-col max-sm:gap-4 max-sm:items-start">
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
            <div class="text-center py-8 text-gray-500">
                <i class="ti ti-file-off text-4xl mb-2"></i>
                <p>Belum ada modul silabus yang tersedia</p>
            </div>
        @endforelse
    </section>
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
