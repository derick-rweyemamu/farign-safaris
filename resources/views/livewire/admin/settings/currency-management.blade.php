<div class="space-y-4 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Currencies') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Manage your system currencies and exchange rates') }}</p>
        </div>
        @can('add currency details')
        <button wire:click="openCreateModal" class="btn-primary">
            <x-icon name="plus" class="w-4 h-4 mr-2" />{{ __('Add Currency') }}</button>
        @endcan
    </div>

    <!-- Stats/Overview (Optional but nice) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-card p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-[3px] bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <x-icon name="currency-dollar" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total
                        Currencies') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ \App\Models\Currency::count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-card p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-[3px] bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <x-icon name="check-circle" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Active') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\Currency::active()->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div
            class="bg-card p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm text-xs text-gray-500 italic flex items-center justify-center">{{ __('Rates are relative to your Base Currency.') }}</div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Symbol') }}</th>
                    <th>{{ __('Exchange Rate') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">
                        <div class="flex justify-end">{{ __('Actions') }}</div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($currencies as $currency)
                    <tr>
                        <td class="font-bold text-indigo-600 dark:text-indigo-400">{{ $currency->code }}</td>
                        <td>{{ $currency->name }}</td>
                        <td><span
                                class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded font-mono">{{ $currency->symbol }}</span>
                        </td>
                        <td>{{ number_format($currency->exchange_rate, 4) }}</td>
                        <td>
                            @can('edit currency details')
                            <button wire:click="toggleStatus({{ $currency->id }})"
                                class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $currency->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $currency->is_active ? 'Active' : 'Inactive' }}
                            </button>
                            @else
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $currency->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $currency->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @endcan
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('edit currency details')
                                <button wire:click="edit({{ $currency->id }})"
                                    class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                    title="{{ __('Edit currency') }}">
                                    <x-icon name="pencil-square" class="w-4 h-4" />
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">{{ __('No currencies found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800">
            {{ $currencies->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal-box max-w-md">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingCurrency ? 'Edit Currency' : 'Add New Currency' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('Code') }}</label>
                            <input type="text" wire:model="code" class="form-input" placeholder="{{ __('USD') }}">
                            @error('code') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('Symbol') }}</label>
                            <input type="text" wire:model="symbol" class="form-input" placeholder="$">
                            @error('symbol') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label
                            class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('Name') }}</label>
                        <input type="text" wire:model="name" class="form-input" placeholder="{{ __('US Dollar') }}">
                        @error('name') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('Exchange
                                Rate') }}</label>
                            <input type="number" step="0.000001" wire:model="exchange_rate" class="form-input">
                            @error('exchange_rate') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label
                                class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('Decimals') }}</label>
                            <input type="number" wire:model="decimal_places" class="form-input">
                            @error('decimal_places') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Active and available for
                            use') }}</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $editingCurrency ? 'Update Currency' : 'Create Currency' }}</span>
                            <span wire:loading>{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
