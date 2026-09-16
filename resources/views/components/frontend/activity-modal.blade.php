{{-- Activity Detail Modal --}}
<div
    x-data="{
        open: false,
        name: '',
        content: '',
        show(data) {
            this.name = data.name;
            this.content = data.content;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.open = false;
            document.body.style.overflow = '';
        }
    }"
    @open-activity-modal.window="show($event.detail)"
    @keydown.escape.window="close()"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"
        @click="close()"
    ></div>

    {{-- Modal Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
    >
        <div class="relative bg-white dark:bg-gray-900 rounded-[3px] shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col pointer-events-auto">

            {{-- Header --}}
            <div class="flex items-start justify-between p-6 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-brand-primary mb-1">{{ __('Activity') }}</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white font-brand" x-text="name"></h3>
                </div>
                <button
                    @click="close()"
                    class="ml-4 flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-sm text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    aria-label="{{ __('Close') }}"
                >
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Scrollable Content --}}
            <div class="overflow-y-auto p-6 flex-grow">
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300" x-html="content"></div>
            </div>

        </div>
    </div>
</div>
