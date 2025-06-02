@props([
    'label' => ''
])

<button
    type="button"
    onclick="window.dispatchEvent(new CustomEvent('open-confirmation', {
        detail: { 
            label: '{{ $label }}', 
            action: 'menghapus'  // Menambahkan aksi 'Menghapus'
        }
    }))"
    {{ $attributes->merge(['class' => 'flex gap-1 items-center text-xs font-medium text-red-500 hover:underline']) }}
>
    <svg width="18" height="18" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15.0417 2.875H3.95833C3.08388 2.875 2.375 3.58388 2.375 4.45833V15.5417C2.375 16.4161 3.08388 17.125 3.95833 17.125H15.0417C15.9161 17.125 16.625 16.4161 16.625 15.5417V4.45833C16.625 3.58388 15.9161 2.875 15.0417 2.875Z" stroke="#F04438" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M7.125 7.625L11.875 12.375M11.875 7.625L7.125 12.375" stroke="#F04438" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Hapus
</button>

