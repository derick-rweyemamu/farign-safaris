<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('General Expenses') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $expenses->total() }} total expenses</p>
        </div>
        <div class="flex items-center gap-3">
            @can('view expense reports')
            <button wire:click="openReportModal" class="btn-secondary">
                <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            @can('create expense')
            <button wire:click="openCreate" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('New Expense') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Expenses Report') }}" :show="$showReportModal">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Expense Type') }}</label>
            <select wire:model="typeFilter" class="form-input text-sm">
                <option value="">{{ __('All Types') }}</option>
                @foreach($expenseTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </x-admin.report-modal>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page and Status (small widths) -->
            <div class="flex items-center gap-3">
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <select wire:model.live="typeFilter" class="form-input text-xs py-1.5 px-2 w-40">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($expenseTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by reference or notes...') }}"
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
                        <th>
                            <button wire:click="sort('expense_date')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Date
                                @if($sortBy === 'expense_date') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Reference') }}</th>
                        <th>
                            <button wire:click="sort('amount')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Amount
                                @if($sortBy === 'amount') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr wire:key="exp-{{ $exp->id }}" class="animate-fade-in">
                            <td class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $exp->expense_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $exp->expenseType->name }}
                                </div>
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $exp->reference_no ?: '-' }}
                            </td>
                            <td class="font-medium text-gray-900 dark:text-white">
                                {{ $exp->currency_symbol }}{{ number_format($exp->amount, 2) }}
                                @if($exp->exchange_rate != 1.0)
                                    <div class="text-[10px] text-gray-400 font-normal">Base:
                                        ${{ number_format($exp->base_amount, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit expense')
                                    <button wire:click="openEdit({{ $exp->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit expense') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete expense')
                                    <button wire:click="confirmDelete({{ $exp->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Delete expense') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <x-icon name="clipboard-document-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No general expenses found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-lg max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Expense' : 'New Expense' }}
                    </h3>
                    <button @click="$wire.set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date *</label>
                        <input type="date" wire:model="expense_date" class="form-input w-full text-xs">
                        @error('expense_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Expense Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Expense Type
                            *</label>
                        <select wire:model="expense_type_id" class="form-input w-full text-xs">
                            <option value="">{{ __('Select type...') }}</option>
                            @foreach($expenseTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('expense_type_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Currency -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Currency
                                *</label>
                            <select wire:model.live="currency_code" class="form-input w-full text-xs">
                                <option value="">{{ __('Select currency...') }}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}">{{ $currency->code }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Amount
                                *</label>
                            <input type="number" step="0.01" wire:model.live.debounce.300ms="amount"
                                class="form-input w-full text-xs" placeholder="0.00">
                            @error('amount') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($exchange_rate != 1.0)
                        <div
                            class="bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-[3px] border border-indigo-100 dark:border-indigo-800">
                            <div class="flex justify-between items-center text-xs text-indigo-800 dark:text-indigo-300">
                                <span>{{ __('Exchange Rate:') }}<strong>{{ number_format($exchange_rate, 6) }}</strong></span>
                                <span>{{ __('Base Equivalent:') }}<strong>${{ number_format($base_amount, 2) }}</strong></span>
                            </div>
                        </div>
                    @endif

                    <!-- Reference No -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Reference / Receipt
                            No') }}</label>
                        <input type="text" wire:model="reference_no" class="form-input w-full text-xs"
                            placeholder="{{ __('e.g. REC-1029') }}">
                        @error('reference_no') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Notes') }}</label>
                        <textarea wire:model="notes" rows="3" class="form-input w-full text-xs resize-none"
                            placeholder="{{ __('Expense details...') }}"></textarea>
                        @error('notes') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="save" class="btn-primary" wire:loading.attr="disabled">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ __('Save Expense') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm">
                <div class="p-5 text-center">
                    <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="trash" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Expense?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this expense? This cannot be undone.') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button wire:click="deleteExpense" class="btn-danger" title="{{ __('Delete Expense') }}">
                            <x-icon name="trash" class="w-4 h-4" />{{ __('Delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
