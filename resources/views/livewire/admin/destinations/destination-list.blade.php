<div class="space-y-4 animate-fade-in">
    

    <!-- Styles moved to app.css for easier management -->

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Destinations') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $destinations->total() }} total destinations</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openReportModal" class="btn-secondary">
                <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @can('create destinations')
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Destination') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Destinations Report') }}" :show="$showReportModal" />

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
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $destination)
                        <tr wire:key="destination-{{ $destination->id }}" class="animate-fade-in">
                            <td>
                                <div class="w-10 h-10 rounded-[3px] overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    @if($destination->featured_image)
                                        <img src="{{ asset('storage/' . $destination->featured_image) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $destination->name }}
                            </td>
                            <td class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                                {{ $destination->city?->name ?: '—' }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $destination->slug }}
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {!! strip_tags($destination->description) ?: '—' !!}
                            </td>
                            <td>
                                @can('edit destinations')
                                    <button wire:click="toggleActive({{ $destination->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $destination->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $destination->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $destination->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $destination->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcan
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit destinations')
                                        <button wire:click="edit({{ $destination->id }})">
                                            <x-icon name="pencil-square" class="w-4 h-4 text-blue-500" />
                                            <span>{{ __('Edit Settings') }}</span>
                                        </button>
                                        <a href="{{ route('admin.destinations.attractions', $destination->id) }}" class="flex items-center w-full px-4 py-2 text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors gap-2 text-gray-700 dark:text-gray-300" wire:navigate>
                                            <x-icon name="map" class="w-4 h-4 text-indigo-500" />
                                            <span>{{ __('Manage Attractions') }}</span>
                                        </a>
                                    @endcan
                                    @can('delete destinations')
                                        <button wire:click="confirmDelete({{ $destination->id }})" class="!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-900/20">
                                            <x-icon name="trash" class="w-4 h-4" />
                                            <span>{{ __('Delete') }}</span>
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <x-icon name="clipboard-list" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No destinations found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $destinations->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Destination' : 'Add New Destination' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden" x-data="{ selectedActivities: @entangle('selectedActivities'), selectedAnimals: @entangle('selectedAnimals') }">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <!-- Country, City, Name & Slug row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name *</label>
                                <input type="text" wire:model.live="name" class="form-input" placeholder="{{ __('e.g. Serengeti National Park') }}">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Slug *</label>
                                <input type="text" wire:model="slug" class="form-input" placeholder="{{ __('e.g. serengeti-national-park') }}">
                                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Tagline') }}</label>
                                <input type="text" wire:model="tagline" class="form-input" placeholder="{{ __('e.g. The Endless Plains') }}">
                                @error('tagline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                            <div class="flex-1 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active and visible') }}</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Display on Homepage (Featured)') }}</label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" wire:model="is_featured" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Show this destination in the Iconic Destinations block') }}</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Display on Destinations Page') }}</label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" wire:model="show_on_page" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Show this destination on the main destinations directory page') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4 pb-2">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Search Engine Optimization') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Meta Title') }}<span class="text-gray-400 font-normal normal-case">(Max 60 chars)</span></label>
                                    <input type="text" wire:model="seo_title" class="form-input" placeholder="{{ __('e.g. Serengeti National Park Tours') }}" maxlength="60">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Meta Description') }}<span class="text-gray-400 font-normal normal-case">(Max 160 chars)</span></label>
                                    <textarea wire:model="seo_description" rows="2" class="form-textarea w-full resize-none" placeholder="{{ __('A compelling summary of the destination...') }}" maxlength="160"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Fees Section -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('Destination Fees') }}</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">{{ __('All fees are in the system currency. Leave as 0.00 if not applicable.') }}</p>

                            <div class="space-y-4">

                                <!-- Concession Fees -->
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-[3px] p-4 border border-gray-100 dark:border-gray-800">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3 flex items-center gap-2">
                                        <x-icon name="ticket" class="w-4 h-4" />{{ __('Concession Fees') }}</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- High Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('High Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="concession_fee_adult_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('concession_fee_adult_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="concession_fee_child_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('concession_fee_child_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Low Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('Low Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="concession_fee_adult_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('concession_fee_adult_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="concession_fee_child_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('concession_fee_child_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Crater Fees -->
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-[3px] p-4 border border-gray-100 dark:border-gray-800">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-3 flex items-center gap-2">
                                        <x-icon name="globe-alt" class="w-4 h-4" />{{ __('Crater Fees') }}</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- High Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('High Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="crater_fee_adult_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('crater_fee_adult_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="crater_fee_child_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('crater_fee_child_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Low Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('Low Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="crater_fee_adult_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('crater_fee_adult_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="crater_fee_child_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('crater_fee_child_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Park Fees -->
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-[3px] p-4 border border-gray-100 dark:border-gray-800">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-3 flex items-center gap-2">
                                        <x-icon name="map" class="w-4 h-4" />{{ __('Park Fees') }}</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- High Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('High Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="park_fee_adult_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('park_fee_adult_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="park_fee_child_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('park_fee_child_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Low Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('Low Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="park_fee_adult_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('park_fee_adult_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="park_fee_child_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('park_fee_child_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pass Fees -->
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-[3px] p-4 border border-gray-100 dark:border-gray-800">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-3 flex items-center gap-2">
                                        <x-icon name="identification" class="w-4 h-4" />{{ __('Pass Fees') }}</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- High Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('High Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="pass_fee_adult_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('pass_fee_adult_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="pass_fee_child_high_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('pass_fee_child_high_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Low Season Group -->
                                        <div class="space-y-2">
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-1">{{ __('Low Season') }}</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Adult') }}</label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model.live="pass_fee_adult_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('pass_fee_adult_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-gray-500 mb-1">{{ __('Child') }}<span class="text-indigo-400 font-normal">(= 50% of adult)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-mono">$</span>
                                                        <input type="number" wire:model="pass_fee_child_low_season" step="0.01" min="0" class="form-input !pl-6 text-xs font-mono" placeholder="0.00">
                                                    </div>
                                                    @error('pass_fee_child_low_season') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Activities Selection -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('Activities') }}</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ __('Select all activities available at this destination.') }}</p>
                            <div class="border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-4 bg-gray-50/20 dark:bg-gray-950/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @forelse($allActivities as $activity)
                                    <label class="flex items-center justify-between gap-3 cursor-pointer p-3 rounded-[3px] border transition-all duration-200 select-none"
                                        :class="(selectedActivities || []).some(a => a == {{ $activity->id }}) ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/50 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-605 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'">
                                        <input type="checkbox" x-model="selectedActivities" value="{{ $activity->id }}" class="sr-only">
                                        <span class="text-xs font-bold">{{ $activity->name }}</span>
                                        <div class="w-4.5 h-4.5 rounded-full border flex items-center justify-center transition-all duration-200"
                                            :class="(selectedActivities || []).some(a => a == {{ $activity->id }}) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'border-gray-305 dark:border-gray-705 bg-white dark:bg-gray-900'">
                                            <x-icon name="check" class="w-2.5 h-2.5 stroke-3" x-show="(selectedActivities || []).some(a => a == {{ $activity->id }})" />
                                        </div>
                                    </label>
                                @empty
                                    <div class="col-span-full py-4 text-center text-xs text-gray-400 italic">{{ __('No activities available. Create activities first in the Activities CRUD.') }}</div>
                                @endforelse
                            </div>
                            @error('selectedActivities') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Animals Selection -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('Animals') }}</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ __('Select animals that can be spotted at this destination.') }}</p>
                            <div class="border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-4 bg-gray-50/20 dark:bg-gray-950/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @forelse($allAnimals as $animal)
                                    <label class="flex items-center justify-between gap-3 cursor-pointer p-3 rounded-[3px] border transition-all duration-200 select-none"
                                        :class="(selectedAnimals || []).some(a => a == {{ $animal->id }}) ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-605 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'">
                                        <input type="checkbox" x-model="selectedAnimals" value="{{ $animal->id }}" class="sr-only">
                                        <span class="text-xs font-bold">{{ $animal->name }}</span>
                                        <div class="w-4.5 h-4.5 rounded-full border flex items-center justify-center transition-all duration-200"
                                            :class="(selectedAnimals || []).some(a => a == {{ $animal->id }}) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'border-gray-305 dark:border-gray-705 bg-white dark:bg-gray-900'">
                                            <x-icon name="check" class="w-2.5 h-2.5 stroke-3" x-show="(selectedAnimals || []).some(a => a == {{ $animal->id }})" />
                                        </div>
                                    </label>
                                @empty
                                    <div class="col-span-full py-4 text-center text-xs text-gray-400 italic">{{ __('No animals available. Create animals first in the Animals CRUD.') }}</div>
                                @endforelse
                            </div>
                            @error('selectedAnimals') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Gallery (Media) -->
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                            <x-admin.gallery-upload 
                                :gallery="$gallery" 
                                :existing="$existingGallery" 
                                label="{{ __('Destination Gallery') }}" 
                                id="destination_gallery_input" 
                                model="gallery" 
                            />
                        </div>
                    </div>

                    <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Destination' : 'Create Destination' }}</span>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Destination?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this destination? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteDestination" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>


