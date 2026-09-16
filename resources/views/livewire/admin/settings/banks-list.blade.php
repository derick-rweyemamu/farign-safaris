<div class="p-6">
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Bank Management') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage company banks and global bank list for employees.') }}</p>
        </div>
        @can('create banks')
            <button wire:click="openCreate" class="btn-primary">
                <x-icon name="plus-circle" class="w-4 h-4" />{{ __('Add Bank') }}</button>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-96 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search banks, branches or accounts...') }}" 
                class="form-input text-xs py-1.5 !pl-8 w-full">
            <x-icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-medium">{{ __('Bank Name') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Branch') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Account Number') }}</th>
                        <th class="px-6 py-4 font-medium">{{ __('Employees Linked') }}</th>
                        <th class="px-6 py-4 font-medium text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-4 font-medium text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($banks as $bank)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $bank->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $bank->branch ?? '—' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600 dark:text-gray-400">
                                {{ $bank->account_number ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded text-xs font-medium">
                                    {{ $bank->employees_count }} 
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @can('edit banks')
                                    <button wire:click="toggleStatus({{ $bank->id }})" 
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $bank->status ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $bank->status ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $bank->status ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $bank->status ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcan
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-admin.action-dropdown>
                                    @can('edit banks')
                                        <button wire:click="openEdit({{ $bank->id }})" title="{{ __('Edit') }}" class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete banks')
                                        <button wire:click="confirmDelete({{ $bank->id }})" title="{{ __('Delete') }}" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                    <x-icon name="building-library" class="w-12 h-12 mb-3 opacity-20" />
                                    <p class="text-sm">{{ __('No banks found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($banks->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $banks->links() }}
            </div>
        @endif
    </div>

    {{-- Form Modal --}}
    <x-admin.modal wire:model="showModal" title="{{ $editingId ? 'Edit Bank' : 'Add Bank' }}">
        <form wire:submit="save" class="space-y-4 p-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Bank Name') }}<span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" class="form-input" placeholder="{{ __('e.g. CRDB Bank') }}" required>
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Branch') }}<span class="text-gray-400 font-normal">(Optional)</span></label>
                <input type="text" wire:model="branch" class="form-input" placeholder="{{ __('e.g. Main Branch') }}">
                @error('branch') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Account Number') }}<span class="text-gray-400 font-normal">(Optional)</span></label>
                <input type="text" wire:model="account_number" class="form-input font-mono" placeholder="{{ __('Enter account number if this is a company bank') }}">
                @error('account_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" wire:model="status" id="status" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Active') }}</label>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 mt-4">
                <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-primary">
                    <span wire:loading.remove wire:target="save">{{ __('Save Bank') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </x-admin.modal>

    {{-- Delete Modal --}}
    <x-admin.modal wire:model="showDeleteModal" title="{{ __('Delete Bank') }}">
        <div class="p-5">
            <div class="flex items-center gap-4 text-red-600 mb-4 bg-red-50 dark:bg-red-900/20 p-4 rounded">
                <x-icon name="exclamation-triangle" class="w-8 h-8 flex-shrink-0" />
                <p class="text-sm font-medium">{{ __('Are you sure you want to delete this bank? This action cannot be undone and may fail if employees are linked to it.') }}</p>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                <button type="button" wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded text-sm font-medium hover:bg-red-700 transition-colors">{{ __('Yes, Delete') }}</button>
            </div>
        </div>
    </x-admin.modal>
</div>
