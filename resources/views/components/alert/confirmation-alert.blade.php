@props([
    'title' => 'Konfirmasi',
    'label' => 'data ini',
    'action' => 'melanjutkan aksi ini',
    'confirmText' => 'Ya',
    'cancelText' => 'Tidak',
])

<div
  x-data="{
      show: false,
      label: '{{ $label }}',
      action: '{{ $action }}',
      confirmText: '{{ $confirmText }}',
      cancelText: '{{ $cancelText }}',
      open(data) {
          this.label = data.label ?? '{{ $label }}'
          this.action = data.action ?? '{{ $action }}'
          // Mengubah confirmText dan cancelText tergantung pada aksi
          if (this.action === 'menghapus') {
              this.confirmText = 'Hapus'
              this.cancelText = 'Batal'
          } else if (this.action === 'menyimpan') {
              this.confirmText = 'Simpan'
              this.cancelText = 'Tidak'
          }
          this.show = true
      }
  }"
  x-on:open-confirmation.window="open($event.detail)"
  x-show="show"
  x-transition
  class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  style="display: none"
>
  <div class="flex flex-col bg-white rounded-xl w-[280px]">
    <!-- Header -->
    <header class="px-4 py-3 bg-sky-200 text-center font-bold text-gray-700 rounded-t-xl">
      <h1>{{ $title }}</h1>
    </header>

    <!-- Content -->
    <main class="px-4 py-5 text-lg text-black text-center">
      Apakah Anda yakin ingin <strong x-text="action"></strong> <strong x-text="label"></strong>?
    </main>

    <!-- Actions -->
    <footer class="flex gap-2.5 items-center px-4 py-6">
      <button @click="show = false; $dispatch('cancel')" class="flex-1 h-12 font-bold text-sky-600 bg-gray-200 rounded-full">
        <span x-text="cancelText"></span>
      </button>
      <button @click="show = false; $dispatch('confirm')" class="flex-1 h-12 font-bold bg-red-500 text-white rounded-full">
        <span x-text="confirmText"></span>
      </button>
    </footer>
  </div>
</div>
