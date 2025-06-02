
@props([
    'label' => ''
])

<div class="pb-1 flex justify-center items-center">
    <button
        type="button"
        onclick="window.dispatchEvent(new CustomEvent('open-confirmation', {
            detail: { 
                label: '{{ $label }}', 
                action: 'menyimpan'  // Menambahkan aksi 'Menyimpan'
            }
        }))"
        {{ $attributes->merge([
            'class' => 'mt-2 text-base font-bold text-white bg-sky-600 h-[38px] rounded-[100px] w-[118.5px] max-sm:text-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2'
        ]) }}
    >
        Simpan
    </button>
</div>


