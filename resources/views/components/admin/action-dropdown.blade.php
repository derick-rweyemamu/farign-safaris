<div x-data="{ open: false, top: 0, left: 0, isUp: false, timeout: null }" class="relative flex justify-end z-[99]">
    <button x-ref="trigger" 
            @mouseenter="
                clearTimeout(timeout);
                open = true; 
                let rect = $refs.trigger.getBoundingClientRect(); 
                left = rect.right + window.scrollX;
                if (window.innerHeight - rect.bottom < 250) {
                    isUp = true;
                    top = rect.top + window.scrollY;
                } else {
                    isUp = false;
                    top = rect.bottom + window.scrollY;
                }
            " 
            @mouseleave="timeout = setTimeout(() => open = false, 150)"
            @click="
                open = !open;
                let rect = $refs.trigger.getBoundingClientRect(); 
                left = rect.right + window.scrollX;
                if (window.innerHeight - rect.bottom < 250) {
                    isUp = true;
                    top = rect.top + window.scrollY;
                } else {
                    isUp = false;
                    top = rect.bottom + window.scrollY;
                }
            " 
            type="button" class="p-2 rounded-full text-gray-500 bg-gray-100/50 dark:bg-gray-800/50 hover:text-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" title="{{ __('Actions') }}">
        <x-icon name="ellipsis-vertical" class="w-5 h-5" />
    </button>

    <template x-teleport="body">
        <div x-show="open" 
             @mouseenter="clearTimeout(timeout); open = true"
             @mouseleave="timeout = setTimeout(() => open = false, 150)"
             x-transition:enter="transition ease-out duration-100" 
             x-transition:enter-start="transform opacity-0 scale-95" 
             x-transition:enter-end="transform opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-75" 
             x-transition:leave-start="transform opacity-100 scale-100" 
             x-transition:leave-end="transform opacity-0 scale-95" 
             class="absolute mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-xl outline-none overflow-hidden z-[9999]"
             x-bind:class="isUp ? 'origin-bottom-right -mt-1' : 'origin-top-right'"
             x-bind:style="`top: ${top}px; left: ${left}px; transform: translate(-100%, ${isUp ? '-100%' : '0'});`"
             style="display: none;">
            
            <div class="flex flex-col py-1 
                [&>button]:w-full [&>button]:flex [&>button]:items-center [&>button]:justify-start [&>button]:gap-2 [&>button]:px-4 [&>button]:py-2.5 [&>button]:text-sm [&>button]:font-medium [&>button]:text-gray-700 dark:[&>button]:text-gray-300 [&>button]:hover:bg-gray-50 dark:[&>button]:hover:bg-gray-700/50 [&>button]:transition-colors
                [&>a]:w-full [&>a]:flex [&>a]:items-center [&>a]:justify-start [&>a]:gap-2 [&>a]:px-4 [&>a]:py-2.5 [&>a]:text-sm [&>a]:font-medium [&>a]:text-gray-700 dark:[&>a]:text-gray-300 [&>a]:hover:bg-gray-50 dark:[&>a]:hover:bg-gray-700/50 [&>a]:transition-colors
                [&_svg]:w-4 [&_svg]:h-4 [&_svg]:text-gray-400 dark:[&_svg]:text-gray-500
                [&>button::after]:content-[attr(title)] [&>button::after]:ml-1
                [&>a::after]:content-[attr(title)] [&>a::after]:ml-1
            ">
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
