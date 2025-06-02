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
      target: null,                
      label: '{{ $label }}',
      action: '{{ $action }}',
      confirmText: '{{ $confirmText }}',
      cancelText: '{{ $cancelText }}',
      isSuccess: false,
      open(data) {
          this.label  = data.label  ?? this.label
          this.action = data.action ?? this.action
          this.target = data.target ?? null   // <— terima target
          this.isSuccess = data.isSuccess ?? false
          
          // Atur teks tombol
          if (this.action === 'menghapus') {
              this.confirmText = 'Hapus';  this.cancelText = 'Batal'
          } else if (this.action === 'menyimpan') {
              this.confirmText = 'Simpan'; this.cancelText = 'Tidak'
          } else if (this.action === 'memperbarui') {
              this.confirmText = 'Ya'; this.cancelText = 'Tutup'
          } else if (this.action === 'memuat') {
              this.confirmText = 'Tutup'; this.cancelText = ''
          } else if (this.isSuccess) {
              this.confirmText = 'OK'; this.cancelText = ''
          }
          this.show = true
      },
      close() {
          this.show = false;
      }
  }"
  x-on:open-confirmation.window="open($event.detail)"
  x-on:close-confirmation.window="close()"
  x-show="show"
  x-transition
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
  style="display:none"
>
  <div class="flex flex-col bg-white rounded-xl w-[280px]">
    <!-- Header -->
    <header class="px-4 py-3 text-center font-bold text-gray-700 rounded-t-xl"
            :class="isSuccess ? 'bg-green-200' : 'bg-sky-200'">
      <h1 x-text="isSuccess ? 'Berhasil' : '{{ $title }}'"></h1>
    </header>

    <!-- Content -->
    <main class="px-4 py-5 text-lg text-black text-center">
      <span x-show="!isSuccess">
        Apakah Anda yakin ingin <strong x-text="action"></strong> <strong x-text="label"></strong>?
      </span>
      <span x-show="isSuccess">
        Berhasil <strong x-text="action"></strong> <strong x-text="label"></strong>
      </span>
      <span x-show="action === 'memuat' && !isSuccess">
        Gagal <strong x-text="action"></strong> <strong x-text="label"></strong>
      </span>
    </main>

    <!-- Actions -->
    <footer class="flex gap-2.5 items-center px-4 py-6">
      <button x-show="cancelText !== ''" @click="close()" class="flex-1 h-12 font-bold text-sky-600 bg-gray-200 rounded-full">
        <span x-text="cancelText"></span>
      </button>
      <!-- ⇣ panggil submit pada target -->
      <button @click="close(); target?.submit?.()" 
              class="h-12 font-bold rounded-full"
              :class="isSuccess || action === 'memuat' ? 'flex-1 bg-sky-500 text-white' : (confirmText === 'Hapus' ? 'flex-1 bg-red-500 text-white' : 'flex-1 bg-sky-500 text-white')">
        <span x-text="confirmText"></span>
      </button>
    </footer>
  </div>
</div>
