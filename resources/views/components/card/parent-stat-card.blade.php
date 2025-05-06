@props(['label', 'url' => '#'])

@php
    // Mengubah label menjadi lowercase dan mengganti spasi dengan '-'
    $formattedLabel = strtolower($label);
    $formattedLabel = str_replace(' ', '-', $formattedLabel);
@endphp

<a 
href="orangtua/anak/{{ $formattedLabel }}" 
class="w-full max-w-full h-[50px] bg-sky-600 rounded-2xl flex items-center justify-center text-white relative">
    <div class="text-center">
        <span class="text-base font-bold block">{{ $label }}</span>
    </div>

    <div class="absolute right-5 top-1">
        {{ $slot }}
    </div>
</a>
