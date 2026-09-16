<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Group Tours') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $tours->total() }} total tours</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Group Tour') }}</button>
        </div>
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

                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>

                <select wire:model.live="categoryFilter" class="form-input text-xs py-1.5 px-2 w-48">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by title...') }}"
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
                        <th class="w-16">{{ __('Image') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Dates') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Seats') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $tour)
                        <tr wire:key="tour-{{ $tour->id }}" class="animate-fade-in">
                            <td>
                                <div
                                    class="w-10 h-10 rounded-[3px] overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    @if($tour->featured_image)
                                        <img src="{{ asset('storage/' . $tour->featured_image) }}" alt=""
                                            class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $tour->title }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $tour->category?->name ?? '—' }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                @if($tour->start_date && $tour->end_date)
                                    {{ $tour->start_date->format('M d, Y') }} - {{ $tour->end_date->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $tour->price ? '$' . number_format($tour->price, 2) : '—' }}
                            </td>
                            <td class="text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $tour->available_seats }}</span>
                                <span class="text-gray-500">{{ __('left') }}</span>
                                <div class="text-xs text-gray-400">
                                    ({{ $tour->booked_seats ?? 0 }}/{{ $tour->total_seats ?? 0 }})</div>
                            </td>
                            <td>
                                <button wire:click="toggleActive({{ $tour->id }})"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tour->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $tour->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    <button wire:click="edit({{ $tour->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="confirmDelete({{ $tour->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Delete') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <x-icon name="clipboard-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No tours found') }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $tours->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Tour' : 'Add New Tour' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category
                                    *</label>
                                <select wire:model="group_category_id" class="form-input">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('group_category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Status') }}</label>
                                <label class="flex items-center gap-3 cursor-pointer mt-2">
                                    <input type="checkbox" wire:model="is_active"
                                        class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active and visible') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Title
                                    *</label>
                                <input type="text" wire:model.live="title" class="form-input"
                                    placeholder="{{ __('Kilimanjaro Trek') }}">
                                @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Slug
                                    *</label>
                                <input type="text" wire:model="slug" class="form-input"
                                    placeholder="{{ __('kilimanjaro-trek') }}">
                                @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Description') }}</label>
                            <textarea wire:model="description" class="form-input min-h-[100px]"
                                placeholder="{{ __('Tour description...') }}"></textarea>
                            @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Start Date') }}</label>
                                <input type="date" wire:model="start_date" class="form-input">
                                @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('End Date') }}</label>
                                <input type="date" wire:model="end_date" class="form-input">
                                @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Price') }}</label>
                                <input type="number" step="0.01" wire:model="price" class="form-input" placeholder="0.00">
                                @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Total Seats') }}</label>
                                <input type="number" wire:model="total_seats" class="form-input" placeholder="10">
                                @error('total_seats')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Booked Seats') }}</label>
                                <input type="number" wire:model="booked_seats" class="form-input" placeholder="0">
                                @error('booked_seats')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <x-admin.image-upload :image="$image" :existing="$existingImage" label="{{ __('Featured Image') }}"
                            id="featured_image_input" model="image" recommended="JPG, PNG or WEBP. Max 2MB" />
                    </div>

                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Tour' : 'Create Tour' }}</span>
                            <span wire:loading>{{ __('Processing...') }}</span>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Tour?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ __('Are you sure you want to delete this tour? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteTour" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>