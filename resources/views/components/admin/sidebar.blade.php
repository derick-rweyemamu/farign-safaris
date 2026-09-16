@php
    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'permission' => 'view dashboard'],

        [
            'label' => 'CRM',
            'icon' => 'funnel',
            'permission' => ['view clients', 'view leads', 'view newsletters'],
            'children' => [
                ['route' => 'crm.leads', 'label' => 'Leads', 'permission' => 'view leads',],
                ['route' => 'crm.kanban', 'label' => 'Pipeline', 'permission' => 'view leads',],
                ['route' => 'clients.index', 'label' => 'Clients', 'permission' => 'view clients',],
                ['route' => 'crm.newsletters', 'label' => 'Newsletters', 'permission' => 'view newsletters',],
            ]
        ],

        [
            'label' => 'Financials',
            'icon' => 'banknotes',
            'permission' => ['view estimates', 'view invoices', 'view invoice expenses', 'view expenses', 'view expense type', 'view payments', 'view profit loss'],
            'children' => [
                ['route' => 'estimations.index', 'label' => 'Estimations', 'permission' => 'view estimates',],
                ['route' => 'invoices.index', 'label' => 'Invoices', 'permission' => 'view invoices',],
                ['route' => 'invoice-expenses.index', 'label' => 'Invoice Expenses', 'permission' => 'view invoice expenses',],
                ['route' => 'expenses.index', 'label' => 'General Expenses', 'permission' => 'view expenses',],
                ['route' => 'expense-types.index', 'label' => 'Expense Types', 'permission' => 'view expense type',],
                ['route' => 'payments.index', 'label' => 'Payments', 'permission' => 'view payments',],
                ['route' => 'financial.profit-loss', 'label' => 'Profit & Loss', 'permission' => 'view profit loss',],
            ]
        ],

        [
            'label' => 'Inventory',
            'icon' => 'archive-box',
            'permission' => 'view product',
            'children' => [
                ['route' => 'products.index', 'label' => 'Products', 'permission' => 'view product',],
                ['route' => 'product-categories.index', 'label' => 'Categories', 'permission' => 'view product category',],
            ]
        ],

        [
            'label' => 'Tours',
            'icon' => 'map',
            'permission' => 'view product',
            'children' => [
                ['route' => 'admin.tours.index', 'label' => 'All Tours', 'permission' => 'view product',],
                ['route' => 'admin.tour-categories.index', 'label' => 'Categories', 'permission' => 'view tour categories',],
                ['route' => 'admin.tour-types.index', 'label' => 'Types', 'permission' => 'view product',],
                ['route' => 'admin.tours.routes.index', 'label' => 'Routes', 'permission' => 'view product',],
                ['route' => 'admin.tour-tags.index', 'label' => 'Tags', 'permission' => 'view product',],
                ['route' => 'admin.destinations.index', 'label' => 'Destinations', 'permission' => 'view destinations',],
                ['route' => 'admin.accommodations.index', 'label' => 'Accommodations', 'permission' => 'view accommodations',],
                ['route' => 'admin.activities.index', 'label' => 'Activities', 'permission' => 'view tour activities',],
                ['route' => 'admin.transports.index', 'label' => 'Transport', 'permission' => 'view transports',],
                ['route' => 'admin.meal-plans.index', 'label' => 'Meal Plans', 'permission' => 'view meal plans',],
                ['route' => 'admin.tours.animals', 'label' => 'Animals', 'permission' => 'view product',],
            ]
        ],

        [
            'label' => 'Group Tours',
            'icon' => 'user-group',
            'permission' => 'view product',
            'children' => [
                ['route' => 'admin.group-tours.index', 'label' => 'All Group Tours', 'permission' => 'view product',],
                ['route' => 'admin.group-categories.index', 'label' => 'Categories', 'permission' => 'view product',],
            ]
        ],

        [
            'label' => 'Tour Proposals',
            'icon' => 'globe',
            'permission' => 'view itineraries',
            'children' => [
                ['route' => 'admin.cost-sheets.index', 'label' => 'Cost Sheets', 'permission' => 'view cost sheets',],
                ['route' => 'itineraries.index', 'label' => 'All Proposals', 'permission' => 'view itineraries',],
            ]
        ],

        [
            'label' => 'HR Management',
            'icon' => 'user-group',
            'permission' => ['view employees', 'view departments', 'view designations', 'view holidays', 'view leaves'],
            'children' => [
                ['route' => 'hr.employees.index', 'label' => 'Employees', 'permission' => 'view employees'],
                ['route' => 'hr.departments.index', 'label' => 'Departments', 'permission' => 'view departments'],
                ['route' => 'hr.designations.index', 'label' => 'Designations', 'permission' => 'view designations'],
                ['route' => 'hr.holidays.index', 'label' => 'Holidays', 'permission' => 'view holidays'],
                ['route' => 'hr.leaves.index', 'label' => 'Leaves', 'permission' => 'view leaves'],
                ['route' => 'hr.appraisals.index', 'label' => 'Appraisals', 'permission' => 'view employees'],
            ]
        ],

        [
            'label' => 'Payroll',
            'icon' => 'calculator',
            'permission' => 'view payroll',
            'children' => [
                ['route' => 'payroll.process.index', 'label' => 'Payroll Process', 'permission' => 'view payroll'],
                ['route' => 'payroll.batches.index', 'label' => 'Payroll Batches', 'permission' => 'view payroll'],
                ['route' => 'payroll.transaction-heads.index', 'label' => 'Transaction Heads', 'permission' => 'view payroll'],
                ['route' => 'payroll.paye-bands', 'label' => 'PAYE Bands', 'permission' => 'view payroll'],
                ['route' => 'payroll.pay-years.index', 'label' => 'Pay Years', 'permission' => 'view payroll'],
            ]
        ],

        ['route' => 'admin.media.index', 'label' => 'Media Library', 'icon' => 'photo', 'permission' => null],

        [
            'label' => 'CMS',
            'icon' => 'document-duplicate',
            'permission' => 'view pages',
            'children' => [
                ['route' => 'cms.pages.index', 'label' => 'Pages', 'permission' => 'view pages',],
                ['route' => 'cms.navigation.index', 'label' => 'Navigation', 'permission' => 'view pages',],
                ['route' => 'cms.posts.index', 'label' => 'Posts', 'permission' => 'view posts',],
                ['route' => 'cms.post-categories.index', 'label' => 'Post Categories', 'permission' => 'view posts',],
            ]
        ],

        ['route' => 'roles.index', 'label' => 'Roles & Permissions', 'icon' => 'shield-check', 'permission' => 'view roles',],

        ['route' => 'activity-log.index', 'label' => 'Activity Log', 'icon' => 'clipboard-list', 'permission' => 'view activities',],

        [
            'label' => 'Settings',
            'icon' => 'cog-6-tooth',
            'permission' => 'view settings',
            'children' => [
                ['route' => 'settings.company', 'label' => 'Company Settings', 'permission' => 'view settings',],
                ['route' => 'settings.currencies', 'label' => 'Currencies', 'permission' => 'view settings',],
                ['route' => 'banks.index', 'label' => 'Banks', 'permission' => 'view banks',],
                ['route' => 'settings.email', 'label' => 'Email Settings', 'permission' => 'view settings',],
                ['route' => 'settings.translations', 'label' => 'UI Translations', 'permission' => 'view settings',],
            ]
        ],

        [
            'label' => 'Locations',
            'icon' => 'map-pin',
            'permission' => 'view countries',
            'children' => [
                ['route' => 'countries.index', 'label' => 'Countries', 'permission' => 'view countries'],
                ['route' => 'cities.index', 'label' => 'Cities', 'permission' => 'view cities'],
            ]
        ],
    ];

    // Compute Online Users
    $onlineUserIds = \Illuminate\Support\Facades\DB::table('sessions')
        ->whereNotNull('user_id')
        ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
        ->distinct()
        ->pluck('user_id');

    $onlineStaff = 0;
    $onlineClients = 0;

    if ($onlineUserIds->isNotEmpty()) {
        $users = \App\Models\User::whereIn('id', $onlineUserIds)->with(['client'])->get();
        foreach ($users as $u) {
            if ($u->client) {
                $onlineClients++;
            } else {
                // If they are not a client, treat them as staff (Admins, Employees, etc)
                $onlineStaff++;
            }
        }
    }
@endphp

<!-- Desktop Sidebar -->
<aside id="sidebar" :class="{ 'collapsed': $store.sidebar.collapsed }"
    class="sidebar hidden lg:flex flex-col flex-shrink-0 transition-all duration-300 ease-in-out relative z-50" x-data="{ 
        openMenus: @js(collect($navItems)->filter(fn($i) => isset($i['children']) && collect($i['children'])->contains(fn($c) => request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $c['route']) . '*')))->map(fn($i) => $i['label'])->values())
    }">

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
            <p class="sidebar-muted text-xs">{{ __('Management Suite') }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 flex flex-col"
        :class="$store.sidebar.collapsed ? 'overflow-visible' : 'overflow-y-auto overflow-x-hidden'">
        <div class="px-3 mb-1">
            <p x-show="!$store.sidebar.collapsed"
                class="text-xs font-semibold uppercase tracking-widest sidebar-muted px-3 mb-2">{{ __('Menu') }}</p>
        </div>

        @foreach($navItems as $item)
            @if(!$item['permission'] || (is_array($item['permission']) ? auth()->user()?->hasAnyPermission($item['permission']) : auth()->user()?->can($item['permission'])))
                @if(isset($item['children']))
                    <div class="relative flex flex-col" x-data="{ hover: false }"
                        @mouseenter="if($store.sidebar.collapsed) hover = true"
                        @mouseleave="if($store.sidebar.collapsed) hover = false">
                        <button
                            @click="$store.sidebar.collapsed ? ($store.sidebar.toggle(), openMenus.push('{{ $item['label'] }}')) : (openMenus.includes('{{ $item['label'] }}') ? openMenus = openMenus.filter(m => m !== '{{ $item['label'] }}') : openMenus.push('{{ $item['label'] }}'))"
                            class="sidebar-link {{ collect($item['children'])->contains(fn($c) => request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $c['route']) . '*')) ? 'active' : '' }}"
                            :class="$store.sidebar.collapsed ? 'justify-center px-0 mx-2 gap-0' : 'justify-between text-left'"
                            :title="$store.sidebar.collapsed ? '{{ addslashes($item['label']) }}' : null">
                            <x-icon name="{{ $item['icon'] }}" class="icon" />
                            <span x-show="!$store.sidebar.collapsed" class="label truncate flex-1">{{ $item['label'] }}</span>
                            <x-icon x-show="!$store.sidebar.collapsed" name="chevron-down"
                                class="w-3.5 h-3.5 transition-transform duration-200 flex-shrink-0"
                                x-bind:class="openMenus.includes('{{ $item['label'] }}') ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="openMenus.includes('{{ $item['label'] }}') && !$store.sidebar.collapsed" x-collapse
                            class="ml-4 space-y-0.5">
                            @foreach($item['children'] as $child)
                                @if(!isset($child['permission']) || !$child['permission'] || auth()->user()?->can($child['permission']))
                                    <a href="{{ route($child['route']) }}"
                                        class="sidebar-link {{ request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $child['route']) . '*') ? 'active' : '' }}"
                                        wire:navigate>
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-40 mr-2 flex-shrink-0"></span>
                                        {{ $child['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <!-- Dropdown Menu for collapsed sidebar -->
                        <div x-show="$store.sidebar.collapsed && hover" x-transition.opacity
                            class="absolute left-full top-0 ml-2 w-[260px] bg-white dark:bg-gray-900 shadow-xl rounded-[3px] border border-gray-100 dark:border-gray-800 py-2 z-50 flex flex-col space-y-1 px-2"
                            style="display: none;" x-cloak>
                            <div
                                class="px-3 py-1 mb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">
                                {{ $item['label'] }}
                            </div>
                            @foreach($item['children'] as $child)
                                @if(!isset($child['permission']) || !$child['permission'] || auth()->user()?->can($child['permission']))
                                    <a href="{{ route($child['route']) }}"
                                        class="sidebar-link {{ request()->routeIs(rtrim($child['route'], '.index') . '*') ? 'active' : '' }}"
                                        wire:navigate>
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-40 mr-2 flex-shrink-0"></span>
                                        {{ $child['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Single menu item -->
                    <a href="{{ route($item['route']) }}"
                        class="sidebar-link {{ request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $item['route']) . '*') ? 'active' : '' }}"
                        :class="$store.sidebar.collapsed ? 'justify-center px-0 mx-2 gap-0' : ''"
                        :title="$store.sidebar.collapsed ? '{{ addslashes($item['label']) }}' : null" wire:navigate>
                        <x-icon name="{{ $item['icon'] }}" class="icon" />
                        <span x-show="!$store.sidebar.collapsed" class="label truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endif
        @endforeach
    </nav>

    <!-- Online Users section -->
    <div class="px-4 py-3 border-t sidebar-border bg-slate-50/50 dark:bg-black/10 flex-shrink-0"
        x-show="!$store.sidebar.collapsed">
        <p class="text-xs font-semibold uppercase tracking-widest sidebar-muted mb-3">{{ __('Online Status') }}</p>
        <div class="flex items-center justify-between text-xs mb-2">
            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span>{{ __('Staff') }}</span>
            </div>
            <span
                class="px-2 py-0.5 rounded-[3px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 font-bold text-[10px]">{{ $onlineStaff }}</span>
        </div>
        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                <span>{{ __('Clients') }}</span>
            </div>
            <span
                class="px-2 py-0.5 rounded-[3px] bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 font-bold text-[10px]">{{ $onlineClients }}</span>
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
                <p class="sidebar-muted text-xs">{{ __('Management Suite') }}</p>
            </div>
        </div>
        <button @click="$store.sidebar.close()"
            class="text-slate-400 hover:text-white hover:bg-black/10 rounded-[3px] dark:hover:bg-white/10 p-1">
            <x-icon name="x-mark" class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation links (mobile) -->
    <nav class="flex-1 py-4 overflow-y-auto flex flex-col"
        x-data="{ openMobileMenus: @js(collect($navItems)->filter(fn($i) => isset($i['children']) && collect($i['children'])->contains(fn($c) => request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $c['route']) . '*')))->map(fn($i) => $i['label'])->values()) }">
        @foreach($navItems as $item)
            @if(!$item['permission'] || (is_array($item['permission']) ? auth()->user()?->hasAnyPermission($item['permission']) : auth()->user()?->can($item['permission'])))
                @if(isset($item['children']))
                    <div class="flex flex-col">
                        <button
                            @click="openMobileMenus.includes('{{ $item['label'] }}') ? openMobileMenus = openMobileMenus.filter(m => m !== '{{ $item['label'] }}') : openMobileMenus.push('{{ $item['label'] }}')"
                            class="sidebar-link text-left flex items-center justify-between {{ collect($item['children'])->contains(fn($c) => request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $c['route']) . '*')) ? 'active' : '' }}">
                            <div class="flex items-center gap-3">
                                <x-icon name="{{ $item['icon'] }}" class="icon w-5 h-5 flex-shrink-0" />
                                <span>{{ $item['label'] }}</span>
                            </div>
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200"
                                x-bind:class="openMobileMenus.includes('{{ $item['label'] }}') ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="openMobileMenus.includes('{{ $item['label'] }}')" x-collapse class="ml-4 space-y-0.5 mt-1">
                            @foreach($item['children'] as $child)
                                @if(!isset($child['permission']) || !$child['permission'] || auth()->user()?->can($child['permission']))
                                    <a href="{{ route($child['route']) }}"
                                        class="sidebar-link {{ request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $child['route']) . '*') ? 'active' : '' }}"
                                        @click="$store.sidebar.close()" wire:navigate>
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-40 mr-2 flex-shrink-0"></span>
                                        {{ $child['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($item['route']) }}"
                        class="sidebar-link {{ request()->routeIs(\Illuminate\Support\Str::replaceLast('.index', '', $item['route']) . '*') ? 'active' : '' }}"
                        @click="$store.sidebar.close()" wire:navigate>
                        <x-icon name="{{ $item['icon'] }}" class="icon w-5 h-5" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endif
        @endforeach
    </nav>

    <!-- Online Users section (mobile) -->
    <div class="px-4 py-3 border-t sidebar-border bg-slate-50/50 dark:bg-black/10 flex-shrink-0">
        <p class="text-xs font-semibold uppercase tracking-widest sidebar-muted mb-3">{{ __('Online Status') }}</p>
        <div class="flex items-center justify-between text-xs mb-2">
            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span>{{ __('Staff') }}</span>
            </div>
            <span
                class="px-2 py-0.5 rounded-[3px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 font-bold text-[10px]">{{ $onlineStaff }}</span>
        </div>
        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                <span>{{ __('Clients') }}</span>
            </div>
            <span
                class="px-2 py-0.5 rounded-[3px] bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 font-bold text-[10px]">{{ $onlineClients }}</span>
        </div>
    </div>


</aside>