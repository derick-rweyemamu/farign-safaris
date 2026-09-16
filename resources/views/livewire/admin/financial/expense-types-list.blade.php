<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Expense Types') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $expenseTypes->total() }} total categories</p>
        </div>
        @can('create expense type')
            <button wire:click="openCreate" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('New Category') }}</button>
        @endcan
    </div>

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
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search categories...') }}"
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
                            <button wire:click="sort('name')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Name
                                @if($sortBy === 'name') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-center">{{ __('Usage') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseTypes as $type)
                        <tr wire:key="type-{{ $type->id }}" class="animate-fade-in">
                            <td class="font-medium text-gray-900 dark:text-white">
                                {{ $type->name }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ Str::limit($type->description, 50) ?: '-' }}
                            </td>
                            <td class="text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $type->invoice_expenses_count + $type->general_expenses_count }} uses
                                </span>
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit expense type')
                                        <button wire:click="openEdit({{ $type->id }})">
                                            <x-icon name="pencil-square" class="w-4 h-4 text-blue-500" />
                                            <span>{{ __('Edit Category') }}</span>
                                        </button>
                                    @endcan
                                    @can('delete expense type')
                                        @if($type->invoice_expenses_count == 0 && $type->general_expenses_count == 0)
                                            <button wire:click="confirmDelete({{ $type->id }})" class="!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-900/20">
                                                <x-icon name="trash" class="w-4 h-4" />
                                                <span>{{ __('Delete Category') }}</span>
                                            </button>
                                        @endif
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <x-icon name="clipboard-document-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No expense types found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenseTypes->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $expenseTypes->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-md max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Category' : 'New Category' }}
                    </h3>
                    <button @click="$wire.set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name *</label>
                        <input type="text" wire:model="name" class="form-input w-full text-xs"
                            placeholder="{{ __('e.g. Travel, Meals, Utilities') }}">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Description') }}</label>
                        <textarea wire:model="description" rows="3" class="form-input w-full text-xs resize-none"
                            placeholder="{{ __('Category details...') }}"></textarea>
                        @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="save" class="btn-primary" wire:loading.attr="disabled">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ __('Save Category') }}</span>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Category?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this expense category?') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button wire:click="deleteExpenseType" class="btn-danger" title="{{ __('Delete Expense Type') }}">
                            <x-icon name="trash" class="w-4 h-4" />{{ __('Delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
