<div 
    x-data="{ 
        show: false, 
        message: '', 
        isError: false,
        init() {
            window.addEventListener('open-success', (event) => {
                this.message = event.detail.message || 'Operasi berhasil';
                this.isError = event.detail.isError || false;
                this.show = true;
                
                setTimeout(() => {
                    this.show = false;
                }, 3000);
            });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    x-cloak
    class="fixed top-4 right-4 z-50 max-w-sm w-full"
>
    <div 
        :class="isError ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'"
        class="border rounded-lg p-4 shadow-lg"
    >
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg 
                    x-show="!isError"
                    class="h-5 w-5 text-green-400" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                
                <!-- Error Icon -->
                <svg 
                    x-show="isError"
                    class="h-5 w-5 text-red-400" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            
            <div class="ml-3 flex-1">
                <h3 
                    :class="isError ? 'text-red-800' : 'text-green-800'"
                    class="text-sm font-medium"
                    x-text="isError ? 'Error' : 'Berhasil'"
                ></h3>
                <p 
                    :class="isError ? 'text-red-700' : 'text-green-700'"
                    class="mt-1 text-sm"
                    x-text="message"
                ></p>
            </div>
            
            <div class="ml-4 flex-shrink-0">
                <button 
                    @click="show = false"
                    :class="isError ? 'text-red-400 hover:text-red-600' : 'text-green-400 hover:text-green-600'"
                    class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                    :class="isError ? 'focus:ring-red-500' : 'focus:ring-green-500'"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>