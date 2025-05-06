@props([
    'title' => 'Minggu 1 - Tema',
    'content' => '',
    'placeholder' => 'Tulis hasil observasi...'
])

<article class="w-full max-w-2xl border border-gray-300 rounded-2xl p-1">


    <!-- Header Judul Minggu -->
    <header class="text-sm text-center text-sky-800 bg-sky-200 h-11 rounded-t-lg">
        {{ $title }}
    </header>

    <div class="flex flex-col gap-2.5 items-start">
        <label class="px-2.5 text-xs text-slate-600">Observasi</label>

        <!-- Editor Container -->
        <div class="flex flex-col w-full px-4 py-2 bg-gray-50 rounded-3xl border border-sky-600 border-solid">
            <!-- Toolbar -->
            <div class="flex gap-3 items-center border-b border-gray-200 pb-2">
                <button class="text-lg font-bold text-black hover:text-sky-600">B</button>
                <button class="text-lg underline text-black hover:text-sky-600">U</button>
                <!-- Tambahkan ikon/tools lain sesuai kebutuhan -->
            </div>

            <!-- Text Area -->
            <textarea
                class="p-2.5 text-xs font-medium text-gray-700 bg-transparent resize-none focus:outline-none min-h-[80px]"
                placeholder="{{ $placeholder }}"
            >{{ $content }}</textarea>
        </div>
    </div>
</article>
