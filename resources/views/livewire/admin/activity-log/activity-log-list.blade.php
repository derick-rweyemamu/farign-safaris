<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Activity Log') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $logs->total() }} total records</p>
        </div>
        <div class="flex items-center gap-3">
            @if(count($selectedRows) > 0)
                <button wire:click="confirmBulkDelete"
                    class="btn-danger text-sm py-2">
                    <x-icon name="trash" class="w-4 h-4" />
                    Delete Selected ({{ count($selectedRows) }})
                </button>
            @endif
            @can('print activities')
                <button wire:click="openReportModal" class="btn-secondary text-sm py-2">
                    <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            <button wire:click="clearFilters" class="btn-secondary text-sm py-2">
                <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Clear Filters') }}</button>
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Activity Log Report') }}" :show="$showReportModal" />

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Controls -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 !w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="20">20 / page</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Action filter -->
                <select wire:model.live="action" class="form-input text-xs py-1.5 px-2 !w-36">
                    <option value="">{{ __('All Actions') }}</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}">{{ $act }}</option>
                    @endforeach
                </select>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="dateFrom" class="form-input text-xs py-1.5 px-2 !w-32"
                        title="{{ __('From date') }}">
                    <span class="text-gray-400 text-xs">{{ __('to') }}</span>
                    <input type="date" wire:model.live="dateTo" class="form-input text-xs py-1.5 px-2 !w-32"
                        title="{{ __('To date') }}">
                </div>
            </div>

            <!-- Right side - Search -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search logs...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="form-checkbox h-4 w-4 focus:ring-brand-primary rounded border-gray-300" style="color: var(--brand-primary);">
                        </th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('IP Address') }}</th>
                        <th>{{ __('Time') }}</th>
                        @can('delete activities')
                            <th class="text-right">
                                <div class="flex justify-end">{{ __('Actions') }}</div>
                            </th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr wire:key="log-{{ $log->id }}">
                            <td class="text-center">
                                <input type="checkbox" wire:model.live="selectedRows" value="{{ $log->id }}"
                                    class="form-checkbox h-4 w-4 focus:ring-brand-primary rounded border-gray-300" style="color: var(--brand-primary);">
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar-placeholder gradient-blue w-8 h-8 text-xs">
                                        {{ $log->user?->initials ?? 'SY' }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">
                                            {{ $log->user?->name ?? 'System' }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $log->user?->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'created' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'updated' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        'deleted' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                        'login' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
                                        'logout' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
                                    ];
                                    $actionType = explode('.', $log->action)[1] ?? 'default';
                                    $colorClass = $actionColors[$actionType] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300';
                                @endphp
                                <span class="badge {{ $colorClass }}">{{ $log->action }}</span>
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $log->description }}
                            </td>
                            <td class="">
                                <code
                                    class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded">
                                                                                                            {{ $log->ip_address ?? '—' }}
                                                                                                        </code>
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                <span title="{{ $log->created_at?->format('Y-m-d H:i:s') ?? '—' }}">
                                    {{ $log->created_at?->diffForHumans() ?? '—' }}
                                </span>
                            </td>
                            @can('delete activities')
                                <td class="text-right">
                                    <button wire:click="confirmDelete({{ $log->id }})"
                                        class="p-1.5 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Delete record') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <x-icon name="clipboard-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500">{{ __('No activity recorded') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Log Record?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this activity record? This cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteLog" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Delete Confirmation Modal -->
    @if($showBulkDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showBulkDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Selected Records?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedRows) }} selected activity records? This action cannot be undone.
                </p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showBulkDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteSelected" class="btn-danger">{{ __('Delete All Selected') }}</button>
                </div>
            </div>
        </div>
    @endif

</div>