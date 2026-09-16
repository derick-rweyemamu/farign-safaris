<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Invoice Expenses') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $invoices_with_expenses->total() }} invoices
                with expenses</p>
        </div>
        @can('create invoice expense')
            <button wire:click="openCreate" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('New Expenses') }}</button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>
            </div>
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by invoice or type...') }}"
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
                            <button wire:click="sort('invoice_number')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Invoice #
                                @if($sortBy === 'invoice_number') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Client') }}</th>
                        <th class="">{{ __('Invoice Total') }}</th>
                        <th class="">
                            <button wire:click="sort('amount')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider justify-end">
                                Total Expenses
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
                    @forelse($invoices_with_expenses as $inv)
                        <tr wire:key="inv-{{ $inv->id }}" class="animate-fade-in">
                            <td class="font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $inv->invoice_number }}
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $inv->client->name }}</div>
                            </td>
                            <td class=" font-medium text-gray-900 dark:text-white">
                                {{ $inv->currency_symbol }}{{ number_format($inv->total, 2) }}
                            </td>
                            <td class=" font-bold text-red-600 dark:text-red-400">
                                {{ $inv->currency_symbol }}{{ number_format($inv->expenses_sum_amount, 2) }}
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    <button wire:click="openView({{ $inv->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                        title="{{ __('View expenses') }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    @can('edit invoice expense')
                                    <button wire:click="openView({{ $inv->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit expenses') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete invoice expense')
                                    <button wire:click="confirmDeleteInvoiceExpenses({{ $inv->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Delete all expenses for this invoice') }}">
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
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No invoices with expenses
                                    found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices_with_expenses->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $invoices_with_expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="currency-dollar" class="w-5 h-5 text-indigo-500" />
                        {{ $editingId ? 'Edit Invoice Expense' : 'Add Invoice Expenses' }}
                    </h3>
                    <button @click="$wire.set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <!-- Invoice Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Select Invoice
                            *</label>
                        <select wire:model.live="invoice_id" class="form-input w-full text-xs" {{ $editingId ? 'disabled' : '' }}>
                            <option value="">{{ __('Select an invoice...') }}</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->invoice_number }} - {{ $inv->client->name }}</option>
                            @endforeach
                        </select>
                        @error('invoice_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    @if($invoice_id)
                        <div
                            class="bg-indigo-50/50 dark:bg-indigo-900/10 p-3 rounded-[3px] border border-indigo-100 dark:border-indigo-800/30 flex justify-between items-center text-xs text-indigo-800 dark:text-indigo-300">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="information-circle" class="w-4 h-4" />{{ __('Expenses will be recorded in') }}<strong>{{ $currency_code }} ({{ $currency_symbol }})</strong>{{ __('to
                                match the invoice.') }}</span>
                            @if($exchange_rate != 1.0)
                                <span class="opacity-75">Exchange Rate: {{ number_format($exchange_rate, 4) }}</span>
                            @endif
                        </div>

                        <!-- Repeater for Expenses -->
                        <div class="space-y-3 mt-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('Expenses') }}</h4>
                                <button type="button" wire:click="addItem"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1">
                                    <x-icon name="plus" class="w-3 h-3" />{{ __('Add Row') }}</button>
                            </div>

                            @foreach($items as $index => $item)
                                <div
                                    class="bg-gray-50 dark:bg-gray-800/30 p-3 rounded-[3px] border border-gray-100 dark:border-gray-800 relative group animate-fade-in">
                                    @if(count($items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity"
                                            title="{{ __('Remove row') }}">
                                            <x-icon name="x-mark" class="w-3 h-3" />
                                        </button>
                                    @endif

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                        <div class="sm:col-span-5">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Expense
                                                Type *</label>
                                            <select wire:model="items.{{ $index }}.expense_type_id"
                                                class="form-input w-full text-xs">
                                                <option value="">{{ __('Select type...') }}</option>
                                                @foreach($expenseTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.expense_type_id') <span
                                            class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Amount
                                                ({{ $currency_symbol }}) *</label>
                                            <input type="number" step="0.01" wire:model="items.{{ $index }}.amount"
                                                class="form-input w-full text-xs" placeholder="0.00">
                                            @error('items.' . $index . '.amount') <span
                                            class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Notes') }}</label>
                                            <input type="text" wire:model="items.{{ $index }}.notes"
                                                class="form-input w-full text-xs" placeholder="{{ __('Optional details...') }}">
                                            @error('items.' . $index . '.notes') <span
                                            class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-[3px]">
                            <x-icon name="document-text"
                                class="w-10 h-10 text-gray-300 dark:text-gray-700 mx-auto mb-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Please select an invoice first') }}</p>
                        </div>
                    @endif
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="save" class="btn-primary" wire:loading.attr="disabled" {{ !$invoice_id ? 'disabled' : '' }}>
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ __('Save Expense(s)') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- View Details Modal -->
    @if($showViewDetailsModal && $viewingInvoice)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showViewDetailsModal', false)">
            <div class="modal-box max-w-4xl flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Expenses for Invoice: {{ $viewingInvoice->invoice_number }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Client: {{ $viewingInvoice->client->name }}</p>
                    </div>
                    <button @click="$wire.set('showViewDetailsModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 dark:bg-gray-900/50">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-400">{{ __('Expense Type') }}</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-400">{{ __('Notes') }}</th>
                                <th class="px-4 py-3 text-right font-bold text-gray-600 dark:text-gray-400">{{ __('Amount') }}</th>
                                <th class="px-4 py-3 text-right font-bold text-gray-600 dark:text-gray-400">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($viewingInvoice->expenses as $item)
                                <tr class="hover:bg-white dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-4 py-4">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $item->expenseType->name }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-gray-500">
                                        {{ $item->notes ?: '—' }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        {{ $item->currency_symbol }}{{ number_format($item->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            @can('edit invoice expense')
                                                <button wire:click="openEdit({{ $item->id }})"
                                                    class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors"
                                                    title="{{ __('Edit') }}">
                                                    <x-icon name="pencil-square" class="w-4 h-4" />
                                                </button>
                                            @endcan
                                            @can('delete invoice expense')
                                                <button wire:click="confirmDelete({{ $item->id }})"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="{{ __('Delete') }}">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                            <tr>
                                <td colspan="2" class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ __('Total Expenses') }}</td>
                                <td class="px-4 py-3 text-right font-extrabold text-red-600 dark:text-red-400">
                                    {{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->expenses->sum('amount'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showViewDetailsModal', false)" class="btn-secondary">{{ __('Close') }}</button>
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
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this invoice expense? This cannot be undone.') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button wire:click="deleteExpense" class="btn-danger" title="{{ __('Delete Invoice Expense') }}">
                            <x-icon name="trash" class="w-4 h-4" />{{ __('Delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Invoice Expenses Modal -->
    @if($showDeleteInvoiceModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteInvoiceModal', false)">
            <div class="modal-box max-w-sm">
                <div class="p-5 text-center">
                    <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="trash" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete All Expenses?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('This will permanently delete ALL expenses recorded for this invoice. This action cannot be undone.') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showDeleteInvoiceModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button wire:click="deleteInvoiceExpenses" class="btn-danger" title="{{ __('Delete Invoice Expense') }}">
                            <x-icon name="trash" class="w-4 h-4" />{{ __('Delete All') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
