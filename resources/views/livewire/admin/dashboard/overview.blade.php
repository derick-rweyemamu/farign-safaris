<div class="space-y-4 animate-fade-in">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Welcome back,') }}
                {{ auth()->user()?->name ?? ' ' }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                {{ __('Here is what is happening in your dashboard today.') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-400">{{ now()->format('l, F j, Y') }}</span>
            <button wire:click="$refresh" class="btn-secondary text-sm py-2 px-3" wire:loading.attr="disabled">
                <x-icon name="arrow-path" class="w-4 h-4"
                    wire:loading.class="animate-spin" />{{ __('Refresh') }}</button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        @foreach($stats as $index => $stat)
            <div class="stat-card animate-fade-in-up" style="animation-delay: {{ $index * 75 }}ms">
                <!-- Background decoration -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 {{ $stat['bg'] }} rounded-full -translate-y-8 translate-x-8 opacity-60">
                </div>

                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['value'] }}</p>
                        <div class="flex items-center gap-1 mt-2">
                            @if($stat['positive'] === true)
                                <x-icon name="trending-up" class="w-3.5 h-3.5 text-emerald-500" />
                                <span
                                    class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $stat['change']
                                                                                                                                                            }}</span>
                            @elseif($stat['positive'] === false)
                                <x-icon name="trending-down" class="w-3.5 h-3.5 text-red-500" />
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $stat['change'] }}</span>
                            @else
                                <span class="text-xs text-gray-400">{{ $stat['change'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div
                        class="{{ $stat['gradient'] }} w-12 h-12 rounded-[3px] flex items-center justify-center flex-shrink-0 shadow-lg">
                        <x-icon name="{{ $stat['icon'] }}" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        <!-- Chart -->
        <div
            class="xl:col-span-2 bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 animate-fade-in-up delay-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Clients Growth') }}</h3>
                    <p class="text-sm text-gray-400">{{ __('New clients per month') }}</p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-[3px] gradient-blue inline-block"></span>{{ __('Clients') }}</span>
                </div>
            </div>

            <!-- Bar Chart for clients  -->
            <div class="chart-container" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
                @foreach($chartData as $data)
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <span class="text-xs text-gray-400 font-medium">{{ $data['clients'] }}</span>
                        <div class="chart-bar gradient-blue w-full"
                            x-bind:style="loaded ? 'height: {{ max(4, $data['clients'] > 0 ? ($data['clients'] / max(array_column($chartData, 'clients'))) * 200 + 20 : 8) }}px' : 'height: 4px'"
                            title="{{ $data['month'] }}: {{ $data['clients'] }} clients">
                        </div>
                        <span class="text-xs text-gray-400">{{ $data['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Clients -->
        <div
            class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 animate-fade-in-up delay-250">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Recent Clients') }}</h3>
                <a href="{{ route('clients.index') }}"
                    class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline"
                    wire:navigate>{{ __('View all') }}</a>
            </div>

            <div class="space-y-4">
                @forelse($recentClients as $user)
                    <div class="flex items-center gap-3">
                        <div
                            class="avatar-placeholder gradient-{{ ['blue', 'green', 'purple', 'orange', 'red'][array_search($user, $recentClients) % 5] }} w-9 h-9 text-xs">
                            {{ $user['initials'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $user['name'] }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">{{ $user['email'] }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span
                                class="badge
                                                {{ $user['status'] === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ ucfirst($user['status']) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <x-icon name="users" class="w-10 h-10 text-gray-300 dark:text-gray-700 mx-auto mb-2" />
                        <p class="text-sm text-gray-400">{{ __('No users yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    @can('view activities')
        <div
            class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 animate-fade-in-up delay-300">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Recent Activity') }}</h3>
                <a href="{{ route('activity-log.index') }}"
                    class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline"
                    wire:navigate>{{ __('View all') }}</a>
            </div>

            @forelse($recentActivity as $activity)
                <div
                    class="flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                    <div class="avatar-placeholder gradient-blue w-9 h-9 text-xs flex-shrink-0">
                        {{ $activity['initials'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $activity['user'] }}</span>
                            <span
                                class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 text-xs">{{
                    $activity['action'] }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $activity['description'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                        @if($activity['ip'])
                            <p class="text-xs text-gray-300 dark:text-gray-600 mt-0.5">{{ $activity['ip'] }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12">
                    <x-icon name="clipboard-list" class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-3" />
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No activity recorded yet') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('Actions will appear here as users interact with the system') }}
                    </p>
                </div>
            @endforelse
        </div>
    @endcan

</div>