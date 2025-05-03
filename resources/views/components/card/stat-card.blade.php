{{-- stat-card.blade.php --}}
<article class="relative w-full max-w-full h-[90px]">
    <div class="absolute w-full bg-sky-600 rounded-2xl h-[95px]"></div>
    <div class="absolute right-0 bg-sky-200 h-[97px] opacity-50 rounded-[48px_16px_16px_48px] w-[173px]"></div>
    
    <div class="absolute top-4 text-white left-6">
        <span class="text-3xl font-bold">{{ $count }}</span><br>
        <span class="text-base font-bold text-center">{{ $label }}</span>
    </div>
    
    <div class="absolute right-5 top-1">
        {{ $slot }}
    </div>
</article>
