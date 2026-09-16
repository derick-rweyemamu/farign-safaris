<!-- Toast notifications -->
<div
    class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"
    x-data
    role="alert"
    aria-live="polite">

    <template x-for="notif in $store.notifications.items" :key="notif.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-[3px] shadow-2xl backdrop-blur-md min-w-[280px] max-w-[380px]"
            :class="{
                'bg-white/95 dark:bg-gray-900/95 border border-green-200 dark:border-green-800':  notif.type === 'success',
                'bg-white/95 dark:bg-gray-900/95 border border-red-200 dark:border-red-800':    notif.type === 'error',
                'bg-white/95 dark:bg-gray-900/95 border border-yellow-200 dark:border-yellow-800': notif.type === 'warning',
                'bg-white/95 dark:bg-gray-900/95 border border-blue-200 dark:border-blue-800':   notif.type === 'info',
            }">

            <!-- Icon -->
            <div class="w-8 h-8 rounded-[3px] flex items-center justify-center flex-shrink-0"
                 :class="{
                     'gradient-green': notif.type === 'success',
                     'gradient-red':   notif.type === 'error',
                     'gradient-orange': notif.type === 'warning',
                     'gradient-blue':  notif.type === 'info',
                 }">
                <x-icon x-show="notif.type === 'success'" name="check" class="w-4 h-4 text-white" stroke-width="2.5" />
                <x-icon x-show="notif.type === 'error'" name="x-mark" class="w-4 h-4 text-white" stroke-width="2.5" />
                <x-icon x-show="notif.type === 'warning'" name="exclamation-triangle" class="w-4 h-4 text-white" stroke-width="2.5" />
                <x-icon x-show="notif.type === 'info'" name="information-circle" class="w-4 h-4 text-white" stroke-width="2.5" />
            </div>

            <!-- Message -->
            <p class="flex-1 text-sm font-medium text-gray-800 dark:text-gray-100" x-text="notif.message"></p>

            <!-- Close -->
            <button @click="$store.notifications.remove(notif.id)"
                    class="flex-shrink-0 p-1 rounded-[3px] text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <x-icon name="x-mark" class="w-3.5 h-3.5" stroke-width="2.5" />
            </button>
        </div>
    </template>
</div>

