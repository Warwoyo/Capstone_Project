
@props([
    'announcementList',
    'maxHeight' => 'max-h-[44vh] md:max-h-[50vh]'
])

<section class="mt-5 max-md:mt-2.5">
    <h2 class="text-xl font-medium text-sky-800 mb-2">Pengumuman Kelas</h2>

    <div class="overflow-y-auto hide-scrollbar {{ $maxHeight }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
            @foreach($announcementList as $announcement)
                <article 
                    x-data="{ expanded: false, overflowed: false }" 
                    x-init="
                        $nextTick(() => {
                            let el = $refs.desc;
                            overflowed = el.scrollHeight > el.offsetHeight;
                        });
                    "
                    class="relative w-full bg-sky-200 rounded-2xl overflow-hidden shadow min-h-[100px] flex flex-col justify-between"
                >
                    <header class="bg-sky-600 text-white text-sm font-bold text-center py-2 rounded-t-2xl">
                        {{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('d F Y') }}
                    </header>

                    <div class="flex flex-col gap-1 p-5 pb-5">
                        <time datetime="{{ $announcement->created_at }}" class="text-xs italic font-light text-slate-600">
                            {{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('d F Y') }}
                        </time>
                        
                        {{-- Classroom Information --}}
                        @if(isset($announcement->classroom) && $announcement->classroom)
                            <div class="mb-1">
                                <span class="inline-block bg-white text-sky-700 text-xs px-2 py-1 rounded-full font-medium">
                                    Kelas: {{ $announcement->classroom->name }}
                                </span>
                            </div>
                        @else
                            <div class="mb-1">
                                <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">
                                    Pengumuman Umum
                                </span>
                            </div>
                        @endif
                        
                        <h3 class="text-sm font-medium text-sky-800">Informasi</h3>
                        <h4 class="text-xs font-bold text-black">
                            {{ $announcement->title }}
                        </h4>

                        <p 
                            x-ref="desc"
                            class="text-xs text-justify text-black transition-all duration-300 ease-in-out line-clamp-2"
                            :class="{ 'line-clamp-none': expanded }"
                        >
                            {{ $announcement->description ?? $announcement->content ?? 'Tidak ada konten' }}
                        </p>

                        <button 
                            x-show="overflowed"
                            x-on:click="expanded = !expanded"
                            type="button" 
                            class="text-xs text-sky-600 mt-1 underline focus:outline-none"
                        >
                            <span x-show="!expanded">Lihat Selengkapnya</span>
                            <span x-show="expanded">Sembunyikan</span>
                        </button>
                    </div>

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'teacher')
                        <form method="POST"
                            action="{{ route('announcements.destroy', $announcement) }}"
                            x-on:submit.prevent="
                                $dispatch('open-confirmation', {
                                    action : 'menghapus',
                                    label  : 'pengumuman',
                                    target : $el       // referensi form
                                })
                            "
                            class="absolute top-12 right-3 flex items-center gap-2 group">
                            @csrf
                            @method('DELETE')

                            <button type="submit" aria-label="Hapus pengumuman" class="flex items-center gap-2">
                                <!-- ikon merah X -->
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.0417 2.375H3.95833C3.08388 2.375 2.375 3.08388 2.375 3.95833V15.0417C2.375 15.9161 3.08388 16.625 3.95833 16.625H15.0417C15.9161 16.625 16.625 15.9161 16.625 15.0417V3.95833C16.625 3.08388 15.9161 2.375 15.0417 2.375Z" stroke="#F04438" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7.125 7.125L11.875 11.875" stroke="#F04438" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11.875 7.125L7.125 11.875" stroke="#F04438" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="text-xs font-medium text-red-500">Hapus</span>
                            </button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>