@props(['title', 'maxWidth' => '2xl'])

<div 
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="modal-backdrop"
    @click.self="show = false"
    style="display: none;"
>
    <div 
        x-show="show"
        x-transition
        class="modal-box max-w-{{ $maxWidth }} flex flex-col overflow-hidden shadow-2xl animate-fade-in-up"
    >
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button @click="show = false"
                class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
