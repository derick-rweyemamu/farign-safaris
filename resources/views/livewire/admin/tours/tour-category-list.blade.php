<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Tour Categories') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $categories->total() }} total categories</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create tour categories')
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Category') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Tour Categories Report') }}" :show="$showReportModal" />

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

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" selected>{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
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
                        <th class="w-16">{{ __('Image') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Sort Order') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr wire:key="category-{{ $category->id }}" class="animate-fade-in">
                            <td>
                                <div
                                    class="w-10 h-10 rounded-[3px] overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    @if($category->image)<img src="{{ asset('storage/' . $category->image) }}" alt=""
                                            class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $category->name }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $category->slug }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $category->description ?: '—' }}
                            </td>
                            <td class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
                                <span class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-xs">{{ $category->sort_order }}</span>
                            </td>
                            <td>
                                @can('edit tour categories')
                                <button wire:click="toggleActive({{ $category->id }})"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </button>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @endcan
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit tour categories')
                                    <button wire:click="edit({{ $category->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete tour categories')
                                    <button wire:click="confirmDelete({{ $category->id }})"
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
                            <td colspan="6" class="py-16 text-center">
                                <x-icon name="clipboard-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No categories found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-lg max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Category' : 'Add New Category' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">

                        {{-- Locale Tabs --}}
                        <div>
                            <div class="flex border-b border-gray-200 dark:border-gray-700 mb-3 gap-1">
                                @foreach($locales as $locale)
                                    <button type="button"
                                        wire:click="$set('activeLocaleTab', '{{ $locale }}')"
                                        class="px-3 py-1.5 text-xs font-semibold uppercase rounded-t transition-colors
                                            {{ $activeLocaleTab === $locale
                                                ? 'bg-indigo-600 text-white'
                                                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                        {{ strtoupper($locale) }}
                                        @if($locale === 'en')<span class="ml-0.5 text-[9px]">*</span>@endif
                                    </button>
                                @endforeach
                            </div>

                            @foreach($locales as $locale)
                                <div @class(['hidden' => $activeLocaleTab !== $locale]) wire:key="locale-tab-{{ $locale }}">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ __('Name') }} ({{ strtoupper($locale) }})
                                                @if($locale === 'en')<span class="text-red-500 ml-1">*</span>@endif
                                            </label>
                                            <input type="text"
                                                wire:model.live="names.{{ $locale }}"
                                                class="form-input"
                                                placeholder="{{ $locale === 'en' ? 'e.g. Budget Safari' : 'Translation in ' . strtoupper($locale) }}">
                                            @if($locale === 'en')
                                                @error('names.en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                {{ __('Description') }} ({{ strtoupper($locale) }})
                                            </label>
                                            <textarea wire:model="descriptions.{{ $locale }}"
                                                class="form-input min-h-[80px]"
                                                placeholder="{{ $locale === 'en' ? 'Category description...' : 'Description in ' . strtoupper($locale) }}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Slug --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug') }} *</label>
                                @if($isEdit)
                                    <label class="flex items-center gap-1.5 cursor-pointer group">
                                        <input type="checkbox" wire:model.live="shouldUpdateSlug"
                                            class="w-3.5 h-3.5 text-indigo-650 rounded border-gray-300 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700">
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 group-hover:text-gray-600 transition-colors">{{ __('Update from name') }}</span>
                                    </label>
                                @endif
                            </div>
                            <input type="text" wire:model.live="slug" class="form-input" placeholder="budget-safari">
                            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Status & Image -->
                        <div class="flex flex-col sm:flex-row gap-5">
                            <div class="flex-1">
                                <x-admin.image-upload
                                    :image="$image"
                                    :existing="$existingImage"
                                    label="{{ __('Category Image') }}"
                                    id="category_image_input"
                                    model="image"

                                    clearAction="$set('image', null); $set('existingImage', '')" 
                                />
                            </div>
                            <div class="flex-1 flex flex-col gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Sort Priority') }}</label>
                                    <input type="number" wire:model="sort_order" class="form-input" placeholder="0">
                                    <p class="text-[10px] text-gray-400 mt-1">{{ __('Lower numbers appear first (e.g. 1, 2, 3)') }}</p>
                                    @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" wire:model="is_active"
                                            class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active and visible') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Category' : 'Create Category' }}</span>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Category?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this category? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteCategory" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
