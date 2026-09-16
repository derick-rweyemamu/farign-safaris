<div class="space-y-4 animate-fade-in pb-20">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.tours.index') }}"
                    class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">{{ __('Tours') }}</a>
                <x-icon name="chevron-right" class="w-3 h-3 text-gray-400" />
                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ __('Itinerary Builder') }}</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                {{ $tour->name }}
            </h2>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <x-icon name="clock" class="w-4 h-4 text-gray-400" />
                {{ $tour->duration_days }} Days / {{ $tour->duration_nights }} Nights
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Left Column: Days List -->
        <div class="w-full lg:w-1/3 xl:w-1/4 space-y-4">
            <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Itinerary Days') }}</h3>
                    <button wire:click="addDay"
                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-[3px] transition-colors tooltip"
                        data-tip="Add Day">
                        <x-icon name="plus" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($tour->itineraries as $day)
                        <div class="group relative flex flex-col p-3 rounded-[3px] border {{ $activeDayId === $day->id ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20 shadow-sm' : 'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700' }} transition-all cursor-pointer"
                            wire:click="editDay({{ $day->id }})">

                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider {{ $activeDayId === $day->id ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500' }}">
                                    Day {{ $loop->iteration }}
                                </span>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="deleteDay({{ $day->id }})"
                                        class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="{{ __('Delete') }}">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $day->title }}
                            </h4>
                            @if($day->location)<p class="text-xs text-gray-500 mt-1 flex items-center gap-1 truncate">
                                    <x-icon name="map-pin" class="w-3 h-3 flex-shrink-0" />
                                    {{ $day->location }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div
                                class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <x-icon name="calendar-days" class="w-6 h-6 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No days added yet.') }}</p>
                            <button wire:click="addDay"
                                class="text-indigo-600 hover:text-indigo-700 text-sm font-medium mt-2">
                                + Add First Day
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Editor -->
        <div class="w-full lg:w-2/3 xl:w-3/4">
            @if($activeDayId)
                <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800"
                    wire:key="editor-{{ $activeDayId }}">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Day {{ $dayForm['day_number'] }}
                        </h3>
                        <button wire:click="saveDay" class="btn-primary py-1.5 px-4 text-sm">
                            <span wire:loading.remove wire:target="saveDay">{{ __('Save Day') }}</span>
                            <span wire:loading wire:target="saveDay">{{ __('Saving...') }}</span>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Day Details -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="w-[100px] shrink-0">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Day
                                    Number') }}</label>
                                <div
                                    class="flex items-stretch border border-gray-300 dark:border-gray-700 rounded-[3px] overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 bg-white dark:bg-gray-900 shadow-sm">
                                    <div class="bg-[#F4F5FB] dark:bg-gray-800 px-2 flex items-center justify-center">
                                        <span
                                            class="text-xs font-medium text-indigo-700 dark:text-indigo-400 font-bold tracking-wide">{{ __('Day') }}</span>
                                    </div>
                                    <div class="flex-1 border-l border-gray-200 dark:border-gray-700 relative">
                                        <input type="number" wire:model="dayForm.day_number" min="1"
                                            class="w-full text-center border-0 py-2 focus:ring-0 text-sm font-medium text-gray-700 dark:text-gray-300 bg-transparent px-1"
                                            placeholder="1">
                                    </div>
                                </div>
                                @error('dayForm.day_number') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Day Heading
                                    / Title') }}</label>
                                <input type="text" wire:model="dayForm.title" class="form-input"
                                    placeholder="{{ __('e.g. Arrival at Arusha') }}">
                                <p class="text-xs text-gray-400 mt-1">{{ __('Short heading that describes the purpose of this day.') }}</p>
                                @error('dayForm.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Description with CKEditor -->
                        <div class="w-full">
                            <x-admin.rich-text wire:model="dayForm.description" label="{{ __('Description') }}" />
                            @error('dayForm.description') <span
                            class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Featured Image -->
                        <div class="w-full">
                            <x-admin.image-upload :image="$dayForm['featured_image'] ?? null"
                                :existing="$dayForm['existing_image'] ?? null" label="{{ __('Featured Image') }}"
                                id="day_featured_image_input" model="dayForm.featured_image"
                                clearAction="$set('dayForm.featured_image', null); $set('dayForm.existing_image', null)" />
                        </div>

                        {{-- ── LOCATION & ACCOMMODATION SECTION ── --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- ── Day Destination Block ── --}}
                            <div class="bg-gray-50/70 dark:bg-gray-800/40 rounded-[3px] p-4 border border-gray-200/80 dark:border-gray-700/80 space-y-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2.5">
                                    <x-icon name="map-pin" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />{{ __('Day Destination Location') }}</h4>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Destination Country --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Country') }}</label>
                                        <select wire:model.live="selectedCountryId" class="form-input text-xs py-2">
                                            <option value="">— Select Country —</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Destination City --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('City / Region') }}</label>
                                        <select wire:model.live="selectedCityId" class="form-input text-xs py-2" @disabled(!$selectedCountryId)>
                                            <option value="">— {{ $selectedCountryId ? 'Select City' : 'Select country first' }} —</option>
                                            @foreach($filteredCities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Destination Landmark / Park --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Destination Landmark / Park') }}</label>
                                    <select wire:model="dayForm.location" class="form-input text-xs py-2" @disabled(!$selectedCityId)>
                                        <option value="">— {{ $selectedCityId ? 'Select Destination' : 'Select city first' }} —</option>
                                        @foreach($filteredDestinations as $dest)
                                            <option value="{{ $dest->name }}">{{ $dest->name }}</option>
                                        @endforeach
                                    </select>
                                    @if($selectedCityId && $filteredDestinations->isEmpty())<p class="text-xs text-amber-500 mt-1">{{ __('No destinations in this city.') }}</p>
                                    @endif
                                    @error('dayForm.location') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- ── Night Accommodation Block ── --}}
                            <div class="bg-gray-50/70 dark:bg-gray-800/40 rounded-[3px] p-4 border border-gray-200/80 dark:border-gray-700/80 space-y-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2.5">
                                    <x-icon name="home" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />{{ __('Night Accommodation') }}</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Accommodation Country --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Accommodation Country') }}</label>
                                        <select wire:model.live="selectedAccCountryId" class="form-input text-xs py-2">
                                            <option value="">— All Countries —</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Accommodation City --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Accommodation City') }}</label>
                                        <select wire:model.live="selectedAccCityId" class="form-input text-xs py-2">
                                            <option value="">— {{ $selectedAccCountryId ? 'All Cities in Country' : 'All Cities' }} —</option>
                                            @foreach($filteredAccCities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Accommodation Property --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Accommodation Property
                                            @if(($selectedAccCityId || $selectedAccCountryId) && $filteredAccommodations->isNotEmpty())<span class="text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-full px-2 py-0.5 font-medium ml-1">
                                                    {{ $filteredAccommodations->count() }} available
                                                </span>
                                            @endif
                                        </label>
                                        <select wire:model="dayForm.accommodation_id" class="form-input text-xs py-2" @disabled(!$selectedAccCountryId && !$selectedAccCityId)>
                                            <option value="">
                                                — {{ ($selectedAccCountryId || $selectedAccCityId) ? 'No Accommodation (Day Trip / Flight)' : 'Select country or city first' }} —
                                            </option>
                                            @foreach($filteredAccommodations as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->city?->name ?? 'N/A' }})</option>
                                            @endforeach
                                        </select>
                                        @if(($selectedAccCountryId || $selectedAccCityId) && $filteredAccommodations->isEmpty())<p class="text-xs text-amber-500 mt-1">{{ __('No accommodations in this location.') }}</p>
                                        @endif
                                    </div>

                                    {{-- Meal Plan --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Meal Plan') }}</label>
                                        <select wire:model="dayForm.meal_plan_id" class="form-input text-xs py-2">
                                            <option value="">{{ __('No Meal Plan') }}</option>
                                            @foreach($mealPlans as $mp)
                                                <option value="{{ $mp->id }}">{{ $mp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Safari / Transport — full width --}}
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                <x-icon name="truck" class="w-4 h-4 text-blue-500" />{{ __('Safari / Transport') }}</label>
                            <div
                                class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-3 bg-gray-50/40 dark:bg-gray-800/20 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-44 overflow-y-auto">
                                @foreach($transports as $trans)
                                    <label
                                        class="flex items-center gap-2 cursor-pointer p-2 rounded-[3px] hover:bg-white dark:hover:bg-gray-800 transition-colors">
                                        <input type="checkbox" wire:model="dayForm.selected_transports" value="{{ $trans->id }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                                        <span
                                            class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $trans->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Included Activities — full width --}}
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                <x-icon name="bolt" class="w-4 h-4 text-indigo-500" />{{ __('Included Activities') }}</label>
                            <div
                                class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-3 bg-gray-50/40 dark:bg-gray-800/20 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-44 overflow-y-auto">
                                @foreach($availableActivities as $act)
                                    <label
                                        class="flex items-center gap-2 cursor-pointer p-2 rounded-[3px] hover:bg-white dark:hover:bg-gray-800 transition-colors">
                                        <input type="checkbox" wire:model="dayForm.selected_activities" value="{{ $act->id }}"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                        <span
                                            class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $act->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Additional / Optional Activities — full width --}}
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                <x-icon name="plus-circle" class="w-4 h-4 text-amber-500" />{{ __('Additional / Optional
                                Activities') }}<span class="text-xs font-normal text-gray-400">(visitors pay extra)</span>
                            </label>
                            <div
                                class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-3 bg-gray-50/40 dark:bg-gray-800/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-60 overflow-y-auto">
                                @foreach($availableActivities as $act)
                                    @php
                                        $isSelected = in_array($act->id, $dayForm['additional_activities'] ?? []);
                                    @endphp
                                    <div class="flex items-center justify-between p-2 rounded-[3px] border transition-colors {{ $isSelected ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/50' : 'bg-white border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-750' }}">
                                        <label class="flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                                            <input type="checkbox" wire:model.live="dayForm.additional_activities" value="{{ $act->id }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 shrink-0">
                                            <span class="text-xs font-medium truncate {{ $isSelected ? 'text-blue-800 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">
                                                {{ $act->name }}
                                            </span>
                                        </label>
                                        
                                        @if($isSelected)
                                            <div class="flex items-center shrink-0 ml-2">
                                                <div class="relative flex items-center">
                                                    <span class="absolute left-2 text-gray-500 dark:text-gray-400 text-xs font-medium">{{ $currency_symbol ?? '$' }}</span>
                                                    <input type="number" step="0.01" wire:model="dayForm.activity_amounts.{{ $act->id }}"
                                                        class="w-20 pl-5 pr-1 py-1 text-xs border border-blue-400 rounded-[3px] focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:border-blue-500/50 dark:text-white shadow-sm"
                                                        placeholder="0.00">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Wildlife / Animals — full width --}}
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                <x-icon name="eye" class="w-4 h-4 text-emerald-500" />{{ __('Wildlife / Animals Expected') }}</label>
                            <div
                                class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-3 bg-gray-50/40 dark:bg-gray-800/20 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 max-h-44 overflow-y-auto">
                                @foreach($allAnimals as $animal)
                                    <label
                                        class="flex items-center gap-2 cursor-pointer p-2 rounded-[3px] hover:bg-white dark:hover:bg-gray-800 transition-colors">
                                        <input type="checkbox" wire:model="dayForm.animals" value="{{ $animal->id }}"
                                            class="w-4 h-4 text-emerald-600 border-gray-300 dark:border-gray-600 rounded focus:ring-emerald-500">
                                        <span
                                            class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $animal->name }}</span>
                                    </label>
                                @endforeach
                                @if($allAnimals->isEmpty())<p class="text-xs text-gray-400 col-span-5">{{ __('No animals configured.') }}</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-800 border-dashed rounded-[3px] flex flex-col items-center justify-center p-12 text-center h-[600px]">
                    <div
                        class="w-16 h-16 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-sm mb-4">
                        <x-icon name="hand-raised" class="w-8 h-8 text-indigo-400" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Select a day to edit') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">{{ __('Click on any day from the left sidebar to edit its details, or add a new day to get started.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>