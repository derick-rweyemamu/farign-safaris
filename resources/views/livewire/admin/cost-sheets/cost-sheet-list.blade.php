<div class="space-y-4 animate-fade-in">

    {{-- ─────────────────────────────────────── Header ─────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Cost Sheets') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $costSheets->total() }} total cost sheets</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create cost sheets')
                <a href="{{ route('admin.cost-sheets.create') }}" class="btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('New Cost Sheet') }}</a>
            @endcan
        </div>
    </div>

    {{-- ─────────────────────────────────────── Filters ────────────────────── --}}
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                </select>
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="finalized">{{ __('Finalized') }}</option>
                </select>
            </div>
            <div class="sm:w-80 relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by title, reference, destination...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────── Table ──────────────────────── --}}
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Destination') }}</th>
                        <th>{{ __('Season') }}</th>
                        <th>{{ __('Guests') }}</th>
                        <th>{{ __('Days') }}</th>
                        <th>{{ __('Grand Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costSheets as $cs)
                                                                    <tr wire:key="cs-{{ $cs->id }}" class="animate-fade-in">
                                                                        <td class="font-mono text-xs text-indigo-600 dark:text-indigo-400 font-bold">
                                                                            {{ $cs->reference_number }}
                                                                        </td>
                                                                        <td class="font-semibold text-gray-800 dark:text-gray-100 max-w-[180px] truncate">
                                                                            {{ $cs->title }}
                                                                        </td>
                                                                        <td class="text-sm text-gray-600 dark:text-gray-300">
                                                                            {{ $cs->destination?->name ?? '—' }}
                                                                        </td>
                                                                        <td>
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                                    {{ $cs->season === 'high'
                        ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                        : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                                                                {{ ucfirst($cs->season) }} Season
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-sm text-gray-600 dark:text-gray-300">
                                                                            {{ $cs->num_adults }}A / {{ $cs->num_children }}C
                                                                        </td>
                                                                        <td class="text-sm text-gray-600 dark:text-gray-300">
                                                                            {{ $cs->duration_days }} day{{ $cs->duration_days != 1 ? 's' : '' }}
                                                                        </td>
                                                                        <td class="font-bold text-gray-900 dark:text-white font-mono">
                                                                            $ {{ number_format($cs->grand_total, 2) }}
                                                                        </td>
                                                                        <td>
                                                                            @can('finalize cost sheets')
                                                                                <button wire:click="toggleStatus({{ $cs->id }})"
                                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cs->status_badge_class }} cursor-pointer">
                                                                                    {{ ucfirst($cs->status) }}
                                                                                </button>
                                                                            @else
                                                                                <span
                                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cs->status_badge_class }}">
                                                                                    {{ ucfirst($cs->status) }}
                                                                                </span>
                                                                            @endcan
                                                                        </td>
                                                                        <td>
                                                                            <x-admin.action-dropdown>
                                                                                <a href="{{ route('cms.excel.cost-sheet', $cs->id) }}" target="_blank"
                                                                                    class="p-1.5 rounded-[3px] text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                                                                                    title="{{ __('Download Excel') }}">
                                                                                    <x-icon name="table-cells" class="w-4 h-4" />
                                                                                </a>
                                                                                @can('create cost sheets')
                                                                                    <button wire:click="duplicate({{ $cs->id }})"
                                                                                        class="p-2 rounded-[3px] text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                                                                        title="{{ __('Duplicate') }}">
                                                                                        <x-icon name="document-duplicate" class="w-4 h-4" />
                                                                                    </button>
                                                                                @endcan
                                                                                @can('edit cost sheets')
                                                                                    <a href="{{ route('admin.cost-sheets.edit', $cs->id) }}"
                                                                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                                                                        title="{{ __('Edit') }}">
                                                                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                                                                    </a>
                                                                                @endcan
                                                                                @can('delete cost sheets')
                                                                                    <button wire:click="confirmDelete({{ $cs->id }})"
                                                                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                                                                        title="{{ __('Delete') }}">
                                                                                        <x-icon name="trash" class="w-4 h-4" />
                                                                                    </button>
                                                                                @endcan
                                                                            </x-admin.action-dropdown>
                                                                        </td>
                                                                    </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-16 text-center">
                                <x-icon name="calculator" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('No cost sheets found') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">
                                    {{ __('Create your first cost sheet to start calculating safari costs.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $costSheets->links() }}
        </div>
    </div>

    {{-- ─────────────────────────────────────── Delete Modal ───────────────── --}}
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Cost Sheet?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ __('This will permanently delete the cost sheet and all its line items. This action cannot be undone.') }}
                </p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteCostSheet" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif

</div>