@props(['title' => __('Dashboard')])

@php
    $recentTours = collect();
    $recentLeads = collect();
    $notificationsCount = 0;

    if (auth()->user()?->can('view itineraries')) {
        $recentTours = \App\Models\Itinerary::where('status', 'draft')->latest()->take(5)->get();
    }
    
    if (auth()->user()?->can('view leads')) {
        $recentLeads = \App\Models\Lead::where('status', 'new')->latest()->take(5)->get();
    }
    
    $notificationsCount = $recentTours->count() + $recentLeads->count();
@endphp
<header class="topbar flex-shrink-0">
    <!-- Mobile menu button -->
    <button
        class="lg:hidden p-2 rounded-[3px] text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition-colors mr-2"
        @click="$store.sidebar.toggleMobile()">
        <x-icon name="bars-3" class="w-5 h-5" />
    </button>

    <!-- Sidebar collapse button (desktop) -->
    <button
        class="hidden lg:flex p-2 rounded-[3px] text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition-colors"
        @click="$store.sidebar.toggle()">
        <x-icon name="bars-3-bottom-left" class="w-5 h-5" />
    </button>

    <!-- Page title -->
    <div class="flex-1 ml-3 flex items-center gap-3">
        <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ $title }}</h1>
    </div>

    <!-- Right section -->
    <div class="flex items-center gap-2">
        @php
            $adminLocale = session('locale', config('app.locale', 'en'));
            $supportedLocales = config('locales.supported', []);
            $currentAdminLocaleData = $supportedLocales[$adminLocale] ?? ['label' => 'ENGLISH', 'flag_img' => 'gb.png'];
        @endphp
        <!-- Admin Content Language Switcher -->
        <div class="relative" x-data="{ adminLangOpen: false }" @click.away="adminLangOpen = false">
            <button type="button" @click="adminLangOpen = !adminLangOpen"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] border border-brand-primary/30 bg-brand-primary/10 text-gray-800 dark:text-gray-100 hover:bg-brand-primary/20 text-xs font-bold uppercase transition-all shadow-sm"
                title="{{ __('Active Content Editing Language') }}">
                @if(isset($currentAdminLocaleData['flag_img']))
                    <img src="{{ asset('storage/flags/' . $currentAdminLocaleData['flag_img']) }}" alt="{{ $adminLocale }}" class="w-4 h-3 rounded-[2px] object-cover">
                @endif
                <span class="text-brand-primary font-bold">{{ strtoupper($adminLocale) }}</span>
                <span class="hidden sm:inline font-normal text-gray-600 dark:text-gray-300 text-[11px]">({{ $currentAdminLocaleData['label'] }})</span>
                <x-icon name="chevron-down" class="w-3 h-3 text-gray-400 ml-0.5" />
            </button>

            <!-- Dropdown -->
            <div x-show="adminLangOpen" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute right-0 top-full mt-2 min-w-[170px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-[4px] shadow-xl z-50 overflow-hidden"
                style="display: none;">
                <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800 text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ __('Content Editing Language') }}</div>
                @foreach($supportedLocales as $code => $localeData)
                    <a href="{{ route('locale.switch', ['locale' => $code]) }}"
                        class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold uppercase transition-all {{ $code === $adminLocale ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        @if(isset($localeData['flag_img']))
                            <img src="{{ asset('storage/flags/' . $localeData['flag_img']) }}" alt="{{ $localeData['label'] }}" class="w-4 h-3 rounded-[2px] object-cover shrink-0">
                        @endif
                        <span>{{ $localeData['label'] ?? strtoupper($code) }} ({{ strtoupper($code) }})</span>
                        @if($code === $adminLocale)
                            <x-icon name="check" class="w-3.5 h-3.5 text-brand-primary ml-auto shrink-0" />
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Dark mode toggle -->
        <button
            @click="$store.theme.toggle()"
            class="p-2 rounded-[3px] text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition-colors">
            <x-icon x-show="!$store.theme.dark" name="moon" class="w-5 h-5" />
            <x-icon x-show="$store.theme.dark" name="sun" class="w-5 h-5" style="display:none" />
        </button>

        @if(auth()->user()?->can('view itineraries') || auth()->user()?->can('view leads'))<!-- Notifications -->
        <div class="relative" x-data="{ open: false, activeTab: '{{ auth()->user()?->can('view itineraries') ? 'tours' : 'leads' }}' }">
            <button
                @click="open = !open"
                class="relative p-2 rounded-[3px] text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition-colors">
                <x-icon name="bell" class="w-5 h-5" />
                @if($notificationsCount > 0)
                    <span class="notification-dot"></span>
                @endif
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 class="dropdown-menu right-0 top-full mt-2 w-80"
                 style="display:none;">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-100">{{ __('Notifications') }}</h3>
                        <span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $notificationsCount }} new</span>
                    </div>
                    
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200 dark:border-gray-700">
                        @can('view itineraries')
                        <button @click="activeTab = 'tours'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'tours', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tours' }" class="flex-1 pb-2 text-xs font-medium text-center border-b-2 transition-colors">
                            Requested Tours ({{ $recentTours->count() }})
                        </button>
                        @endcan
                        @can('view leads')
                        <button @click="activeTab = 'leads'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'leads', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'leads' }" class="flex-1 pb-2 text-xs font-medium text-center border-b-2 transition-colors">
                            Leads ({{ $recentLeads->count() }})
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="py-1 max-h-96 overflow-y-auto">
                    <!-- Tours Tab Content -->
                    @can('view itineraries')
                    <div x-show="activeTab === 'tours'">
                        @forelse($recentTours as $tour)
                            <a href="{{ route('itineraries.index') }}" class="dropdown-item border-b border-gray-50 dark:border-gray-800 last:border-0" wire:navigate>
                                <div class="gradient-green w-8 h-8 rounded-[3px] flex items-center justify-center flex-shrink-0">
                                    <x-icon name="globe" class="w-4 h-4 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $tour->title ?? 'Untitled Tour' }}</p>
                                    <p class="text-xs text-gray-400">{{ $tour->created_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500">{{ __('No requested tours') }}</div>
                        @endforelse
                    </div>
                    @endcan

                    <!-- Leads Tab Content -->
                    @can('view leads')
                    <div x-show="activeTab === 'leads'" style="display: none;">
                        @forelse($recentLeads as $lead)
                            <a href="{{ route('crm.leads') }}" class="dropdown-item border-b border-gray-50 dark:border-gray-800 last:border-0" wire:navigate>
                                <div class="gradient-orange w-8 h-8 rounded-[3px] flex items-center justify-center flex-shrink-0">
                                    <x-icon name="funnel" class="w-4 h-4 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $lead->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $lead->created_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500">{{ __('No recent leads') }}</div>
                        @endforelse
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @endif

        <!-- User menu -->
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="flex items-center gap-2 p-1.5 rounded-[3px] hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <img src="{{ auth()->user()?->avatar_url }}" alt="{{ auth()->user()?->name }}" class="w-8 h-8 rounded-[3px] object-cover border border-gray-200 dark:border-gray-700">
                <div class="hidden md:block text-left">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 leading-tight">{{ auth()->user()?->name ?? ' ' }}</p>
                    <p class="text-xs text-gray-400 leading-tight"> {{ auth()->user()?->getRoleNames()->first() ?? ' ' }} </p>
                </div>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 hidden md:block" />
            </button>

            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 class="dropdown-menu right-0 top-full mt-2"
                 style="display:none;">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100">{{ auth()->user()?->name ?? ' ' }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()?->email ?? ' ' }}</p>
                </div>
                <div class="">
                    <a href="{{ route('settings.profile') }}" class="dropdown-item" wire:navigate>
                        <x-icon name="user" class="w-4 h-4" />{{ __('My Profile') }}</a>
                    @if(auth()->user()?->employee)<a href="{{ route('hr.activities.index') }}" class="dropdown-item" wire:navigate>
                            <x-icon name="pencil-square" class="w-4 h-4" />{{ __('Daily Activities') }}</a>
                    @endif
                </div>
                <div class="border-t border-gray-100 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item w-full">
                            <x-icon name="arrow-right-on-rectangle" class="w-4 h-4" />{{ __('Sign out') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

