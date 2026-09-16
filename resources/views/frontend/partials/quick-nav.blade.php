<!-- Desktop/Mobile Floating Nav Container (sticks/absolute to parent container margin) -->
<div class="safaris-quick-nav-container absolute top-0 bottom-0 left-0 w-[240px] pointer-events-none z-40">
    <!-- Sticky outer wrapper with top/bottom margin -->
    <div class="sticky top-[100px] bottom-[40px] max-h-[calc(100vh-140px)] pointer-events-auto transition-all duration-300 flex items-start"
        id="quickNavWrapper">

        <!-- Opened State Navigation Panel -->
        <div id="quickNavPanel"
            class="bg-white dark:bg-gray-900 shadow-2xl rounded-r-lg border border-l-0 border-gray-200 dark:border-gray-800 transition-all duration-300 w-[240px] flex flex-col max-h-[calc(100vh-140px)] overflow-hidden">
            <!-- Header (toggles closed on click) -->
            <div class="flex items-center justify-between p-4 border-b border-gray-150 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                onclick="toggleQuickNav(false)">
                <h3
                    class="!text-xs font-bold text-gray-900 dark:text-white capitalize tracking-wider m-0 flex items-center gap-2">
                    <x-icon name="bars-3" class="w-5 h-5 mr-2 text-[#EC9824]" />
                    <span>{{ $categoryName ?? 'Quick Navigate' }}</span>
                </h3>
                <x-icon name="chevron-left" class="w-5 h-5 text-[#EC9824]" />
            </div>

            <!-- List Body (adds scrollbar if content is long) -->
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                @if($posts->count() > 0)
                    <ul class="quick-nav-list p-0 m-0">
                        @foreach($posts as $post)
                            <li>
                                <a href="{{ route('articles.show', $post->slug) }}" target="{{ $target ?? '_self' }}"
                                    class="quick-nav-link group">
                                    <div class="quick-nav-diamond"></div>
                                    <span class="quick-nav-text">{{ $post->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-gray-400">{{ __('No articles available.') }}</p>
                @endif
            </div>
        </div>

        <!-- Closed State Icon Button (attached to parent section edge) -->
        <button id="quickNavTrigger" onclick="toggleQuickNav(true)"
            class="hidden bg-white dark:bg-gray-900 text-[#2E1F12] dark:text-white p-4 rounded-r-lg shadow-md border border-l-0 border-gray-200 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-center">
            <x-icon name="bars-3" class="w-6 h-6 text-[#EC9824]" />
        </button>
    </div>
</div>

<style>
    /* Diamond and dashed line timeline styling */
    .quick-nav-list {
        position: relative;
        list-style: none;
    }

    .quick-nav-list li {
        position: relative;
        padding-left: 20px;
        padding-bottom: 18px;
    }

    .quick-nav-list li:last-child {
        padding-bottom: 0;
    }

    /* Vertical dashed line connector */
    .quick-nav-list li::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 14px;
        bottom: -8px;
        border-left: 1px dashed #D1D5DB;
        z-index: 1;
    }

    .dark .quick-nav-list li::before {
        border-left-color: #374151;
    }

    .quick-nav-list li:last-child::before {
        display: none;
    }

    /* Diamond Bullet */
    .quick-nav-diamond {
        position: absolute;
        left: 0;
        top: 6px;
        width: 9px;
        height: 9px;
        background: #D1D5DB;
        transform: rotate(45deg);
        transition: all 0.25s ease;
        z-index: 2;
    }

    .dark .quick-nav-diamond {
        background: #4B5563;
    }

    /* Link text styling matching image */
    .quick-nav-link {
        display: flex;
        align-items: flex-start;
        text-decoration: none;
    }

    .quick-nav-text {
        font-size: 13px;
        font-weight: 500;
        font-family: var(--font-serif), Georgia, serif;
        font-style: italic;
        color: #6B7280;
        line-height: 1.35;
        transition: color 0.25s ease;
    }

    .dark .quick-nav-text {
        color: #9CA3AF;
    }

    /* Hover states using brand colors */
    .quick-nav-link:hover .quick-nav-text {
        color: #EC9824;
    }

    .quick-nav-link:hover .quick-nav-diamond {
        background: #EC9824;
        box-shadow: 0 0 8px rgba(201, 168, 76, 0.4);
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #E5E7EB;
        border-radius: 2px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #374151;
    }
</style>

<script>
    function toggleQuickNav(open) {
        const panel = document.getElementById('quickNavPanel');
        const trigger = document.getElementById('quickNavTrigger');
        if (open) {
            panel.style.display = 'flex';
            trigger.style.display = 'none';
            localStorage.setItem('quick_nav_open', 'true');
        } else {
            panel.style.display = 'none';
            trigger.style.display = 'block';
            localStorage.setItem('quick_nav_open', 'false');
        }
    }

    // Check responsive default state on load
    document.addEventListener("DOMContentLoaded", function () {
        const isDesktop = window.innerWidth >= 1024;
        const storedState = localStorage.getItem('quick_nav_open');

        if (storedState === 'true') {
            toggleQuickNav(true);
        } else if (storedState === 'false') {
            toggleQuickNav(false);
        } else {
            // Default: open on desktop, closed on tablet/mobile
            toggleQuickNav(isDesktop);
        }
    });
</script>