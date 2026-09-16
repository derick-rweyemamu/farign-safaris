<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Countries Management') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $countries->total() }} total countries</p>
        </div>
        @can('create countries')
        <button wire:click="create" class="btn-primary" id="btn-create-country">
            <x-icon name="plus" class="w-4 h-4" />{{ __('Add Country') }}</button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page and Status (small widths) -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by...') }}"
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
                        <th class="w-16">{{ __('Flag') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Phone Code') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        <tr wire:key="country-{{ $country->id }}" class="animate-fade-in">
                            <td>
                                <div
                                    class="w-10 h-7 rounded border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                    @if($country->flag)<img src="{{ asset('storage/flags/' . $country->flag) }}" alt="{{ $country->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[10px] text-gray-400 uppercase font-bold">{{ $country->code }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $country->name }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400 uppercase">
                                {{ $country->code }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $country->phone_code ?? '—' }}
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit countries')
                                    <button wire:click="edit({{ $country->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit country') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete countries')
                                    <button wire:click="confirmDelete({{ $country->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Delete country') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <x-icon name="clipboard-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No countries found') }}</p>
                                @if($search)
                                    <p class="text-xs text-gray-400 mt-1">{{ __('Try adjusting your search') }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800" wire:key="pagination">
            {{ $countries->links() }}
        </div>
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-md max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $isEdit ? 'Edit Country' : 'Add New Country' }}
                        </h3>
                        <p class="text-sm text-gray-400 mt-0.5">
                            {{ $isEdit ? 'Update country details' : 'Add a new country to the database' }}
                        </p>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Form -->
                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Country
                                    Name
                                    *</label>
                                <input type="text" wire:model="name" class="form-input" placeholder="{{ __('e.g. United States') }}"
                                    id="input-name">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <!-- Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">ISO
                                        Code (3
                                        chars) *</label>
                                    <input type="text" wire:model="code" class="form-input uppercase" placeholder="{{ __('USA') }}"
                                        maxlength="3">
                                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Phone Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Phone
                                        Code') }}</label>
                                    <input type="text" wire:model="phone_code" class="form-input" placeholder="+1">
                                    @error('phone_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Flag -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Flag
                                    Filename') }}</label>
                                <input type="text" wire:model="flag" class="form-input" placeholder="{{ __('us.png') }}">
                                <p class="text-[10px] text-gray-400 mt-1">{{ __('Images should be located in storage/flags/') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Country' : 'Add Country' }}</span>
                            <span wire:loading>{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Country?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('This action cannot be undone. All cities linked to this country may also be affected.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteCountry" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif

</div>
