@php
    $navItems = [
        ['route' => 'client.dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'permission' => 'view clients area'],
        ['route' => 'client.estimations', 'label' => 'My Estimations', 'icon' => 'document-text'],
        ['route' => 'client.invoices', 'label' => 'My Invoices', 'icon' => 'receipt-percent'],
        ['route' => 'client.payments', 'label' => 'My Payments', 'icon' => 'banknotes'],

    ];
@endphp

<!-- Desktop Sidebar -->
<aside id="sidebar" :class="{ 'collapsed': $store.sidebar.collapsed }"
    class="sidebar hidden lg:flex flex-col flex-shrink-0 transition-all duration-300 ease-in-out relative z-50">

    <!-- Logo area -->
    <div class="flex items-center gap-3 px-4 py-5 border-b sidebar-border flex-shrink-0">
        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
            <img src="{{ Storage::url(app(\App\Settings\GeneralSetting::class)->favicon) }}" alt="icon"
                class="w-full h-full object-contain">
        </div>
        <div x-show="!$store.sidebar.collapsed" x-transition:enter="transition ease-out duration-200 delay-75"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="overflow-hidden">
            <p class="sidebar-fg font-bold text-base leading-tight tracking-tight">
                {{ app(\App\Settings\GeneralSetting::class)->company_name ?? config('app.name') }}
            </p>
            <p class="sidebar-muted text-xs">{{ __('Client Portal') }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 flex flex-col"
        :class="$store.sidebar.collapsed ? 'overflow-visible' : 'overflow-y-auto overflow-x-hidden'">
        <div class="px-3 mb-1">
            <p x-show="!$store.sidebar.collapsed"
                class="text-xs font-semibold uppercase tracking-widest sidebar-muted px-3 mb-2">{{ __('Portal Menu') }}</p>
        </div>

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
                class="sidebar-link {{ request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $item['route']) . '*') ? 'active' : '' }} flex items-center"
                :class="$store.sidebar.collapsed ? 'justify-center px-0 mx-2 gap-0' : ''"
                :title="$store.sidebar.collapsed ? '{{ addslashes($item['label']) }}' : null" wire:navigate>
                <div class="flex items-center" :class="$store.sidebar.collapsed ? 'gap-0' : 'gap-3'">
                    <x-icon name="{{ $item['icon'] }}" class="icon w-5 h-5 flex-shrink-0" />
                    <template x-if="!$store.sidebar.collapsed">
                        <span class="label truncate">{{ $item['label'] }}</span>
                    </template>
                </div>
            </a>
        @endforeach
    </nav>

    <!-- User section -->
    <div class="border-t sidebar-border p-3 flex-shrink-0">
        <div class="flex items-center gap-3 rounded-[3px] p-2 hover:bg-white/5 transition-colors cursor-pointer"
            :class="$store.sidebar.collapsed ? 'justify-center' : ''">
            <img src="{{ auth()->user()?->avatar_url }}" alt="{{ auth()->user()?->name }}" class="flex-shrink-0 w-9 h-9 rounded-[3px] object-cover border border-white/10">
            <div x-show="!$store.sidebar.collapsed" class="flex-1 min-w-0 overflow-hidden">
                <p class="text-sm font-semibold sidebar-fg truncate">{{ auth()->user()?->name ?? ' ' }}</p>
                <p class="text-xs sidebar-muted truncate">{{ __('Client Account') }}</p>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar -->
<aside class="sidebar lg:hidden fixed top-0 left-0 h-full w-[280px] shadow-2xl flex flex-col z-50"
    :style="$store.theme.dark ? 'background-color: #111827 !important;' : 'background-color: #ffffff !important;'"
    :class="$store.sidebar.mobileOpen ? 'mobile-open' : ''" x-cloak x-data>

    <!-- Logo area (mobile) -->
    <div class="flex items-center justify-between gap-3 px-4 py-5 border-b sidebar-border">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-[3px] flex items-center justify-center">
                <img src="{{ Storage::url(app(\App\Settings\GeneralSetting::class)->favicon) }}" alt="icon"
                    class="w-full h-full object-contain">
            </div>
            <div>
                <p class="sidebar-fg font-bold text-base leading-tight tracking-tight">
                    {{ app(\App\Settings\GeneralSetting::class)->company_name ?? config('app.name') }}
                </p>
                <p class="sidebar-muted text-xs">{{ __('Client Portal') }}</p>
            </div>
        </div>
        <button @click="$store.sidebar.close()"
            class="text-slate-400 hover:text-white hover:bg-black/10 rounded-[3px] dark:hover:bg-white/10 p-1">
            <x-icon name="x-mark" class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation links (mobile) -->
    <nav class="flex-1 py-4 overflow-y-auto flex flex-col">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
                class="sidebar-link {{ request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $item['route']) . '*') ? 'active' : '' }}"
                @click="$store.sidebar.close()" wire:navigate>
                <x-icon name="{{ $item['icon'] }}" class="icon w-5 h-5" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- User section (mobile) -->
    <div class="border-t sidebar-border p-3 flex-shrink-0">
        <div class="flex items-center gap-3 rounded-[3px] p-2 hover:bg-white/5 transition-colors cursor-pointer">
            <img src="{{ auth()->user()?->avatar_url }}" alt="{{ auth()->user()?->name }}" class="flex-shrink-0 w-9 h-9 rounded-[3px] object-cover border border-white/10">
            <div class="flex-1 min-w-0 overflow-hidden">
                <p class="text-sm font-semibold sidebar-fg truncate">{{ auth()->user()?->name ?? ' ' }}</p>
                <p class="text-xs sidebar-muted truncate">{{ __('Client Account') }}</p>
            </div>
        </div>
    </div>
</aside>
