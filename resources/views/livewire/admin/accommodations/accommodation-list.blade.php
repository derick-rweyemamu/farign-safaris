<div class="space-y-4 animate-fade-in">
    

    <!-- Styles moved to app.css for easier management -->

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Accommodations') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $accommodations->total() }} total accommodations</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openReportModal" class="btn-secondary">
                <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @can('create accommodations')
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Accommodation') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Accommodations Report') }}" :show="$showReportModal" />

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

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Search -->
            <div class="sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name, description, city...') }}"
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
                        <th>{{ __('City') }}</th>
                        <th>{{ __('Pricing Rates & Room Types') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accommodations as $accommodation)
                        <tr wire:key="accommodation-{{ $accommodation->id }}" class="animate-fade-in">
                            <td>
                                <div class="w-10 h-10 rounded-[3px] overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    @if($accommodation->featured_image)
                                        <img src="{{ asset('storage/' . $accommodation->featured_image) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $accommodation->name }}
                            </td>
                            <td class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                                {{ $accommodation->city?->name ?: '—' }}
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1.5 max-w-lg">
                                    @forelse($accommodation->rates as $rate)
                                        <span class="inline-flex flex-col px-2 py-1 rounded bg-amber-50 dark:bg-amber-900/20 text-[11px] font-medium border border-amber-200/50 dark:border-amber-800/40">
                                            <span class="text-gray-900 dark:text-gray-100 font-bold capitalize">
                                                {{ $rate->room_type }} ({{ $rate->class }}) <span class="text-[10px] font-normal text-gray-500 dark:text-gray-400 normal-case ml-1">· {{ $rate->season ?? 'N/A' }}</span>
                                            </span>
                                            <span class="text-gray-500 dark:text-gray-400">
                                                {{ __('STO:')}} {{ $currency_symbol }}{{ number_format($rate->sto, 2) }} | {{ __('RACK:')}} {{ $currency_symbol }}{{ number_format($rate->rack, 2) }}
                                            </span>
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">{{ __('No rates defined') }}</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @can('edit accommodations')
                                    <button wire:click="toggleActive({{ $accommodation->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $accommodation->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $accommodation->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $accommodation->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $accommodation->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcan
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit accommodations')
                                        <button wire:click="edit({{ $accommodation->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete accommodations')
                                        <button wire:click="confirmDelete({{ $accommodation->id }})"
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
                                <x-icon name="clipboard-list" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No accommodations found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $accommodations->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Accommodation' : 'Add New Accommodation' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <!-- Country, City, Name & Slug row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name *</label>
                                <input type="text" wire:model.live="name" class="form-input" placeholder="{{ __('e.g. Serengeti Serena Lodge') }}">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Slug *</label>
                                <input type="text" wire:model="slug" class="form-input" placeholder="{{ __('e.g. serengeti-serena-lodge') }}">
                                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Country *
                                </label>
                                <select wire:model.live="selectedCountryId" class="form-input">
                                    <option value="">— Select Country —</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    City *
                                </label>
                                <select wire:model="city_id" class="form-input" @disabled(!$selectedCountryId)>
                                    <option value="">{{ $selectedCountryId ? '— Select City —' : '— Select a country first —' }}</option>
                                    @foreach($filteredCities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @if($selectedCountryId && $filteredCities->isEmpty())<p class="text-amber-500 text-xs mt-1">{{ __('No cities for this country.') }}</p>
                                @endif
                                @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                        </div>

                        <!-- Description (Quill Text Editor integration) -->
                        <x-admin.rich-text wire:model="description" label="{{ __('Description') }}" />

                        <!-- Status & Featured Image -->
                        <div class="flex flex-col sm:flex-row gap-5 pt-2">
                            <div class="flex-1">
                                <x-admin.image-upload 
                                    :image="$featured_image" 
                                    :existing="$existingFeaturedImage" 
                                    label="{{ __('Featured Image') }}" 
                                    id="featured_image_input" 
                                    model="featured_image" 
                                />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active and visible') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Rates Repeating Section -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Pricing Rates & Room Types') }}</h4>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($rates as $index => $rate)
                                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-2 items-center bg-gray-50 dark:bg-gray-800/40 p-3 rounded border border-gray-100 dark:border-gray-800 animate-fade-in" wire:key="rate-row-{{ $index }}">
                                        <!-- Room Type -->
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">{{ __('Room Type') }}</label>
                                            <select wire:model="rates.{{ $index }}.room_type" class="form-input text-xs py-1.5 px-2">
                                                <option value="single">{{ __('Single') }}</option>
                                                <option value="double">{{ __('Double') }}</option>
                                                <option value="triple">{{ __('Triple') }}</option>
                                                <option value="family">{{ __('Family') }}</option>
                                            </select>
                                        </div>
                                        <!-- Season -->
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">{{ __('Season') }}</label>
                                            <select wire:model="rates.{{ $index }}.season" class="form-input text-xs py-1.5 px-2">
                                                <option value="peak season">{{ __('Peak Season') }}</option>
                                                <option value="high season">{{ __('High Season') }}</option>
                                                <option value="low season">{{ __('Low Season') }}</option>
                                            </select>
                                        </div>
                                        <!-- Standard Class -->
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">{{ __('Class') }}</label>
                                            <select wire:model="rates.{{ $index }}.class" class="form-input text-xs py-1.5 px-2">
                                                <option value="budget">{{ __('Budget') }}</option>
                                                <option value="mid-range">{{ __('Mid-range') }}</option>
                                                <option value="luxury">{{ __('Luxury') }}</option>
                                            </select>
                                        </div>
                                        <!-- STO Price -->
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">STO Price ({{ $currency_symbol }})</label>
                                            <input type="number" step="0.01" wire:model="rates.{{ $index }}.sto" class="form-input text-xs py-1.5 px-2" placeholder="e.g. 120.00">
                                            @error('rates.' . $index . '.sto') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <!-- RACK Price -->
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">RACK Price ({{ $currency_symbol }})</label>
                                            <input type="number" step="0.01" wire:model="rates.{{ $index }}.rack" class="form-input text-xs py-1.5 px-2" placeholder="e.g. 180.00">
                                            @error('rates.' . $index . '.rack') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <!-- Remove Button -->
                                        <div class="flex justify-end sm:pt-4">
                                            <button type="button" wire:click="removeRate({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-colors animate-fade-in"  title="{{ __('Delete Accommodation') }}">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-3 flex justify-end">
                                <button type="button" wire:click="addRate" class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                    <x-icon name="plus" class="w-3.5 h-3.5" />{{ __('Add Rate Row') }}</button>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4 pb-2">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Search Engine Optimization') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Meta Title') }}<span class="text-gray-400 font-normal normal-case">(Max 60 chars)</span></label>
                                    <input type="text" wire:model="seo_title" class="form-input" placeholder="{{ __('e.g. Best Accommodations') }}" maxlength="60">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Meta Description') }}<span class="text-gray-400 font-normal normal-case">(Max 160 chars)</span></label>
                                    <textarea wire:model="seo_description" rows="2" class="form-textarea w-full resize-none" placeholder="{{ __('A compelling summary of the accommodation...') }}" maxlength="160"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery (Media) -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                            <x-admin.gallery-upload 
                                :gallery="$gallery" 
                                :existing="$existingGallery" 
                                label="{{ __('Accommodation Gallery') }}" 
                                id="accommodation_gallery_input" 
                                model="gallery" 
                            />
                        </div>
                    </div>

                    <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Accommodation' : 'Create Accommodation' }}</span>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Accommodation?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this accommodation? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteAccommodation" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>


