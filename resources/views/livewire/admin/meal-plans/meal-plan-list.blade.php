<div class="space-y-4 animate-fade-in">


    <!-- Styles moved to app.css for easier management -->

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Meal Plans') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $mealPlans->total() }} total meal plans</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openReportModal" class="btn-secondary">
                <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @can('create meal plans')
                <button wire:click="create" class="btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('Add Meal Plan') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Meal Plans Report') }}" :show="$showReportModal" />

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Type filter -->
                <select wire:model.live="typeFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="Breakfast">{{ __('Breakfast') }}</option>
                    <option value="Lunch">{{ __('Lunch') }}</option>
                    <option value="Dinner">{{ __('Dinner') }}</option>
                    <option value="Half Board">{{ __('Half Board') }}</option>
                    <option value="Full Board">{{ __('Full Board') }}</option>
                    <option value="All Inclusive">{{ __('All Inclusive') }}</option>
                </select>

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Search -->
            <div class="sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name, description...') }}"
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
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Default Amount') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mealPlans as $mealPlan)
                        <tr wire:key="meal-plan-{{ $mealPlan->id }}" class="animate-fade-in">
                            <td class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $mealPlan->name }}
                            </td>
                            <td>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    {{ $mealPlan->type }}
                                </span>
                            </td>
                            <td class="text-sm font-semibold text-gray-900 dark:text-white font-mono">
                                {{ $mealPlan->price ? '$' . number_format($mealPlan->price, 2) : '—' }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $mealPlan->slug }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {!! strip_tags($mealPlan->description) ?: '—' !!}
                            </td>
                            <td>
                                @can('edit meal plans')
                                    <button wire:click="toggleActive({{ $mealPlan->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mealPlan->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $mealPlan->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mealPlan->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $mealPlan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcan
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit meal plans')
                                        <button wire:click="edit({{ $mealPlan->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete meal plans')
                                        <button wire:click="confirmDelete({{ $mealPlan->id }})"
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
                            <td colspan="7" class="py-16 text-center">
                                <x-icon name="clipboard-list"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('No meal plans found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $mealPlans->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-3xl flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Meal Plan' : 'Add New Meal Plan' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <x-admin.locale-tabs
                            :locales="$locales"
                            :activeLocaleTab="$activeLocaleTab"
                            namePlaceholder="e.g. Standard Breakfast"
                            descPlaceholder="Meal plan description..." />

                        {{-- Slug --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Slug') }} *</label>
                            <input type="text" wire:model="slug" class="form-input" placeholder="{{ __('e.g. standard-breakfast') }}">
                            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type & Amount row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Meal Plan
                                    Type *</label>
                                <select wire:model="type" class="form-input">
                                    <option value="Breakfast">{{ __('Breakfast') }}</option>
                                    <option value="Lunch">{{ __('Lunch') }}</option>
                                    <option value="Dinner">{{ __('Dinner') }}</option>
                                    <option value="All Inclusive">{{ __('All Inclusive') }}</option>
                                </select>
                                @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Default
                                        Amount') }}</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-mono text-sm">$</span>
                                    <input type="number" wire:model="price" step="0.01" min="0"
                                        class="form-input !pl-8 text-xs font-mono" placeholder="0.00">
                                </div>
                                @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="pt-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model="is_active"
                                    class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active and visible') }}</span>
                            </label>
                        </div>
                    </div>

                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Meal Plan' : 'Create Meal Plan' }}</span>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Meal Plan?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ __('Are you sure you want to delete this meal plan? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteMealPlan" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>