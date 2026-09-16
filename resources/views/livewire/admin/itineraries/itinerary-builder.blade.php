<div class="space-y-4 animate-fade-in" x-data="{ activeTab: @entangle('activeTab') }">

    <!-- Styles moved to app.css for easier management -->

    <!-- Header -->
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-card p-4 rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('itineraries.index') }}"
                    class="text-gray-400 hover:text-indigo-600 transition-colors">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Builder: {{ $itinerary->title }}</h2>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-7">Ref: #{{ $itinerary->reference_number }}
                &bull; Total: {{ $itinerary->currency }} {{ number_format($itinerary->total_price, 2) }}</p>
        </div>
        <div class="flex items-center gap-2">
            @can('edit itineraries')
                <select wire:model.live="itinerary.status" class="form-input text-sm py-2">
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="sent">{{ __('Sent') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            @else
                <span
                    class="inline-flex items-center px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm font-semibold capitalize text-gray-700 dark:text-gray-300">
                    Status: {{ $itinerary->status }}
                </span>
            @endcan
            @can('view itineraries')
                <a href="{{ route('cms.pdf.itinerary', $itinerary) }}" target="_blank"
                    class="btn-primary flex items-center gap-2 whitespace-nowrap">
                    <x-icon name="arrow-down-tray" class="w-4 h-4" />{{ __('Export PDF') }}</a>
            @endcan
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-800">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="switchTab('details')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'details' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <div class="flex items-center gap-2">
                    <x-icon name="information-circle" class="w-5 h-5" />{{ __('Trip Details') }}</div>
            </button>
            <button wire:click="switchTab('days')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'days' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <div class="flex items-center gap-2">
                    <x-icon name="map" class="w-5 h-5" />{{ __('Day by Day') }}</div>
            </button>
            <button wire:click="switchTab('pricing')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'pricing' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <div class="flex items-center gap-2">
                    <x-icon name="banknotes" class="w-5 h-5" />{{ __('Pricing &amp; Terms') }}</div>
            </button>
        </nav>
    </div>

    <div class="mt-4">
        <!-- TAB 1: Trip Details -->
        <div x-show="activeTab === 'details'"
            class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-6 space-y-6">
            <h3
                class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">{{ __('General Information') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Link to Inventory
                        Product (Optional)') }}</label>
                    <select wire:model.live="itinerary.product_id" class="form-input">
                        <option value="">{{ __('Custom Package') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Package Title
                        *</label>
                    <input type="text" wire:model.live.debounce.500ms="itinerary.title" class="form-input"
                        placeholder="{{ __('e.g. 10 Days Tanzania Safari') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Link to CRM Client
                        (Optional)') }}</label>
                    <select wire:model.live="itinerary.client_id" class="form-input">
                        <option value="">{{ __('Custom Client / Not Listed') }}</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Client Name') }}</label>
                    <input type="text" wire:model.live.debounce.500ms="itinerary.client_name" class="form-input"
                        placeholder="{{ __('e.g. Ashley Jing') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Client Email
                        (Optional)') }}</label>
                    <input type="email" wire:model.live.debounce.500ms="itinerary.client_email" class="form-input"
                        placeholder="client@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Duration
                        (Days)') }}</label>
                    <input type="number" wire:model.live.debounce.500ms="itinerary.duration_days" class="form-input"
                        placeholder="e.g. 7" min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Reference
                        Number') }}</label>
                    <input type="text" wire:model.live.debounce.500ms="itinerary.reference_number"
                        class="form-input bg-gray-50 dark:bg-gray-900 cursor-not-allowed" readonly>
                </div>
            </div>

            <h3
                class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 pt-4">{{ __('Dates &amp; Audience') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Start Date') }}</label>
                    <input type="date" wire:model.live="itinerary.start_date" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('End Date') }}</label>
                    <input type="date" wire:model.live="itinerary.end_date" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Adults') }}</label>
                    <input type="number" min="1" wire:model.live="itinerary.adults" class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Children') }}</label>
                    <input type="number" min="0" wire:model.live="itinerary.children" class="form-input">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <p class="text-xs text-gray-500 italic"><x-icon name="check-circle"
                        class="w-3 h-3 inline text-green-500" />{{ __('Auto-saved') }}</p>
            </div>
        </div>

        <!-- TAB 2: Day by Day -->
        <div x-show="activeTab === 'days'" class="space-y-6">
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
                            @forelse($itinerary->days as $day)
                                <div class="group relative flex flex-col p-3 rounded-[3px] border {{ $activeDayId === $day->id ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20 shadow-sm' : 'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700' }} transition-all cursor-pointer"
                                    wire:click="editDay({{ $day->id }})">

                                    <div class="flex items-center justify-between mb-1">
                                        <span
                                            class="text-xs font-bold uppercase tracking-wider {{ $activeDayId === $day->id ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500' }}">
                                            Day {{ $loop->iteration }}
                                        </span>
                                        <div
                                            class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click.stop="deleteDay({{ $day->id }})"
                                                class="p-1 text-gray-400 hover:text-red-600 transition-colors"
                                                title="{{ __('Delete') }}">
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

                <!-- Right Column: Day Edit Form -->
                <div class="flex-1">
                    @if($activeDayId)
                        <div
                            class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-5 space-y-5">
                            <div
                                class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Edit Day Details') }}</h3>
                                <button wire:click="saveDay" class="btn-primary text-sm">
                                    <x-icon name="check" class="w-4 h-4 mr-1" wire:loading.remove wire:target="saveDay" />
                                    <x-icon name="arrow-path" class="w-4 h-4 mr-1 animate-spin" wire:loading
                                        wire:target="saveDay" />{{ __('Save Day') }}</button>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="w-[100px] shrink-0">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Day Number') }}</label>
                                    <div class="flex items-stretch border border-gray-300 dark:border-gray-700 rounded-[3px] overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 bg-white dark:bg-gray-900 shadow-sm">
                                        <div class="bg-[#F4F5FB] dark:bg-gray-800 px-2 flex items-center justify-center">
                                            <span class="text-xs font-medium text-indigo-700 dark:text-indigo-400 font-bold tracking-wide">{{ __('Day') }}</span>
                                        </div>
                                        <div class="flex-1 border-l border-gray-200 dark:border-gray-700 relative">
                                            <input type="number" wire:model="dayForm.day"
                                                class="w-full text-center border-0 py-2 focus:ring-0 text-sm font-medium text-gray-700 dark:text-gray-300 bg-transparent px-1"
                                                placeholder="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Day Heading / Title') }}</label>
                                    <input type="text" wire:model="dayForm.location" class="form-input"
                                        placeholder="{{ __('e.g. Day 1 Adventure in Tarangire National Park') }}">
                                    <p class="mt-1 text-xs text-gray-400">{{ __('Short heading that describes the purpose of this day.') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Date
                                        (Optional)') }}</label>
                                    <input type="date" wire:model="dayForm.date" class="form-input">
                                </div>
                            </div>

                            {{-- ── LOCATION & ACCOMMODATION SECTION ── --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                {{-- ── Day Destination Block ── --}}
                                <div class="bg-gray-50/70 dark:bg-gray-800/40 rounded-[3px] p-4 border border-gray-200/80 dark:border-gray-700/80 space-y-4">
                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                                        <x-icon name="map-pin" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />{{ __('Day Destination Location Filter') }}</h4>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Country') }}</label>
                                            <select wire:model.live="selectedCountryId" class="form-input text-xs py-2">
                                                <option value="">— Select Country —</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
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

                                    {{-- Destination Select (below Country and City) --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Destination Landmark / Park
                                            @if($selectedCityId && $filteredDestinations->isNotEmpty())<span class="text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-full px-2 py-0.5 font-medium ml-1">
                                                    {{ $filteredDestinations->count() }} available
                                                </span>
                                            @endif
                                        </label>
                                        <select wire:model.live="dayForm.destination_id" class="form-input text-xs py-2" @disabled(!$selectedCityId)>
                                            <option value="">— {{ $selectedCityId ? 'Select Destination' : 'Select city first' }} —</option>
                                            @foreach($filteredDestinations as $dest)
                                                <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($selectedCityId && $filteredDestinations->isEmpty())<p class="text-xs text-amber-500 mt-1">{{ __('No destinations in this city.') }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- ── Night Accommodation Block ── --}}
                                <div class="bg-gray-50/70 dark:bg-gray-800/40 rounded-[3px] p-4 border border-gray-200/80 dark:border-gray-700/80 space-y-4">
                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                                        <x-icon name="home" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />{{ __('Night Accommodation Filter') }}</h4>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Accommodation Country') }}</label>
                                            <select wire:model.live="selectedAccCountryId" class="form-input text-xs py-2">
                                                <option value="">— All Countries —</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
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
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                Accommodation Property
                                                @if(($selectedAccCityId || $selectedAccCountryId) && $filteredAccommodations->isNotEmpty())<span class="text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 rounded-full px-2 py-0.5 font-medium ml-1">
                                                        {{ $filteredAccommodations->count() }} available
                                                    </span>
                                                @endif
                                            </label>
                                            <select wire:model.live="dayForm.accommodation_id" class="form-input text-xs py-2" @disabled(!$selectedAccCountryId && !$selectedAccCityId)>
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
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Accommodation Cost') }}</label>
                                            <input type="number" step="0.01" wire:model="dayForm.accommodation_amount" class="form-input text-xs py-2" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @if(!empty($accommodationRates))
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2 bg-indigo-50/50 dark:bg-indigo-900/10 p-3 border border-indigo-100 dark:border-indigo-900/50 rounded-[3px]">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-indigo-700 dark:text-indigo-400 mb-1.5">{{ __('Room
                                            Type') }}</label>
                                        <select wire:model.live="dayForm.room_type" class="form-input text-sm">
                                            <option value="">-- Select --</option>
                                            @foreach(collect($accommodationRates)->pluck('room_type')->filter()->unique() as $rt)
                                                <option value="{{ $rt }}">{{ $rt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-indigo-700 dark:text-indigo-400 mb-1.5">{{ __('Season') }}</label>
                                        <select wire:model.live="dayForm.season" class="form-input text-sm">
                                            <option value="">-- Select --</option>
                                            @foreach(collect($accommodationRates)->pluck('season')->filter()->unique() as $season)
                                                <option value="{{ $season }}">{{ $season }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-indigo-700 dark:text-indigo-400 mb-1.5">{{ __('Class') }}</label>
                                        <select wire:model.live="dayForm.class" class="form-input text-sm">
                                            <option value="">-- Select --</option>
                                            @foreach(collect($accommodationRates)->pluck('class')->filter()->unique() as $class)
                                                <option value="{{ $class }}">{{ $class }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <p class="text-xs text-indigo-500 mt-1 pl-1">{{ __('Select Room, Season, and Class to auto-fill the
                                    accommodation cost.') }}</p>
                            @endif




                            <!-- Logistics: Meals & Transports -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">

                                <!-- Meal Plan -->
                                <div
                                    class="bg-amber-50/40 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 rounded-[3px] p-4">
                                    <h4
                                        class="text-sm font-bold text-amber-800 dark:text-amber-500 mb-4 flex items-center gap-2">
                                        <x-icon name="cake" class="w-4 h-4" />{{ __('Meal Plan Options') }}<span class="text-xs font-normal text-amber-600/70 dark:text-amber-400/70">(select one)</span>
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-1">
                                        @foreach($mealPlans as $plan)
                                            @php $isMealSelected = (string)$dayForm['meal_plan_id'] === (string)$plan->id; @endphp
                                            <div class="flex items-center justify-between p-2 rounded-[3px] border transition-colors {{ $isMealSelected ? 'bg-amber-100/60 border-amber-300 dark:bg-amber-900/30 dark:border-amber-700/60' : 'bg-white border-gray-200 hover:bg-amber-50/50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-amber-900/10' }}">
                                                <label class="flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                                                    <input type="radio" wire:model.live="dayForm.meal_plan_id"
                                                        name="meal_plan_radio"
                                                        value="{{ $plan->id }}"
                                                        class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 shrink-0">
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-medium truncate block {{ $isMealSelected ? 'text-amber-800 dark:text-amber-300' : 'text-gray-700 dark:text-gray-300' }}">
                                                            {{ $plan->name }}
                                                        </span>
                                                        @if($plan->type)<span class="text-xs text-gray-400 dark:text-gray-500">{{ $plan->type }}</span>
                                                        @endif
                                                    </div>
                                                </label>

                                                @if($isMealSelected)
                                                    <div class="flex items-center shrink-0 ml-2">
                                                        <div class="relative flex items-center">
                                                            <span class="absolute left-2 text-gray-500 dark:text-gray-400 text-xs font-medium">$</span>
                                                            <input type="number" step="0.01" wire:model="dayForm.meal_plan_amount"
                                                                class="w-20 pl-5 pr-1 py-1 text-xs border border-amber-400 rounded-[3px] focus:ring-1 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-900 dark:border-amber-500/50 dark:text-white shadow-sm"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($mealPlans->isEmpty())<p class="text-xs text-gray-400 col-span-2 py-2">{{ __('No meal plans configured.') }}</p>
                                        @endif
                                    </div>
                                    @if($dayForm['meal_plan_id'])
                                        <button type="button" wire:click="clearMealPlan"
                                            class="mt-2 text-xs text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-200 flex items-center gap-1">
                                            <x-icon name="x-mark" class="w-3 h-3" />{{ __('Clear meal plan') }}</button>
                                    @endif
                                </div>

                                <!-- Transports -->
                                <div
                                    class="bg-blue-50/40 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-[3px] p-4">
                                    <h4
                                        class="text-sm font-bold text-blue-800 dark:text-blue-500 mb-4 flex items-center gap-2">
                                        <x-icon name="truck" class="w-4 h-4" />{{ __('Safari / Transport') }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-1">
                                        @foreach($transports as $transport)
                                            @php $isTransSelected = in_array($transport->id, $dayForm['selected_transports'] ?? []); @endphp
                                            <div class="flex items-center justify-between p-2 rounded-[3px] border transition-colors {{ $isTransSelected ? 'bg-blue-100/60 border-blue-300 dark:bg-blue-900/30 dark:border-blue-700/60' : 'bg-white border-gray-200 hover:bg-blue-50/50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-blue-900/10' }}">
                                                <label class="flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                                                    <input type="checkbox" wire:model.live="dayForm.selected_transports"
                                                        value="{{ $transport->id }}"
                                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 shrink-0">
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-medium truncate block {{ $isTransSelected ? 'text-blue-800 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">
                                                            {{ $transport->name }}
                                                        </span>
                                                        @if($transport->type)<span class="text-xs text-gray-400 dark:text-gray-500">{{ $transport->type }}</span>
                                                        @endif
                                                    </div>
                                                </label>

                                                @if($isTransSelected)
                                                    <div class="flex items-center shrink-0 ml-2">
                                                        <div class="relative flex items-center">
                                                            <span class="absolute left-2 text-gray-500 dark:text-gray-400 text-xs font-medium">$</span>
                                                            <input type="number" step="0.01"
                                                                wire:model="dayForm.transport_amounts.{{ $transport->id }}"
                                                                class="w-20 pl-5 pr-1 py-1 text-xs border border-blue-400 rounded-[3px] focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:border-blue-500/50 dark:text-white shadow-sm"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($transports->isEmpty())<p class="text-xs text-gray-400 col-span-2 py-2">{{ __('No transports configured.') }}</p>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <!-- Included Activities -->
                            <div x-data="{ selected: @js($dayForm['selected_activities'] ?? []) }">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Included
                                    Activities') }}</label>
                                <!-- Hidden input to sync back to Livewire on save -->
                                <template x-for="id in selected" :key="id">
                                    <input type="checkbox" name="_inc_act[]" x-bind:value="id" checked
                                        wire:model="dayForm.selected_activities" class="sr-only">
                                </template>
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-1 max-h-60 overflow-y-auto pr-1">
                                    @foreach($activities as $activity)
                                        <label
                                            x-on:click.prevent="selected.includes({{ $activity->id }}) ? selected.splice(selected.indexOf({{ $activity->id }}), 1) : selected.push({{ $activity->id }}); $wire.set('dayForm.selected_activities', selected)"
                                            :class="selected.includes({{ $activity->id }}) ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/50 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'"
                                            class="flex items-center justify-between gap-3 cursor-pointer p-3 rounded-[3px] border transition-all duration-200 select-none">
                                            <span class="text-xs font-bold">{{ $activity->name }}</span>
                                            <div class="w-4.5 h-4.5 rounded-full border flex items-center justify-center transition-all duration-200"
                                                :class="selected.includes({{ $activity->id }}) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900'">
                                                <x-icon name="check" class="w-2.5 h-2.5"
                                                    x-show="selected.includes({{ $activity->id }})" style="display:none" />
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Additional Activities -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <x-icon name="plus-circle" class="w-4 h-4 text-amber-500" />{{ __('Additional / Optional Activities') }}<span class="text-xs font-normal text-gray-400">(visitors pay extra)</span>
                                </label>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-3 bg-gray-50/40 dark:bg-gray-800/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-60 overflow-y-auto">
                                    @foreach($activities as $activity)
                                        @php $isAddSelected = in_array($activity->id, $dayForm['additional_activities'] ?? []); @endphp
                                        <div class="flex items-center justify-between p-2 rounded-[3px] border transition-colors {{ $isAddSelected ? 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800/50' : 'bg-white border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-750' }}">
                                            <label class="flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                                                <input type="checkbox" wire:model.live="dayForm.additional_activities"
                                                    value="{{ $activity->id }}"
                                                    class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 shrink-0">
                                                <span class="text-xs font-medium truncate {{ $isAddSelected ? 'text-amber-800 dark:text-amber-300' : 'text-gray-700 dark:text-gray-300' }}">
                                                    {{ $activity->name }}
                                                </span>
                                            </label>

                                            @if($isAddSelected)
                                                <div class="flex items-center shrink-0 ml-2">
                                                    <div class="relative flex items-center">
                                                        <span class="absolute left-2 text-gray-500 dark:text-gray-400 text-xs font-medium">$</span>
                                                        <input type="number" step="0.01"
                                                            wire:model="dayForm.additional_activity_amounts.{{ $activity->id }}"
                                                            class="w-20 pl-5 pr-1 py-1 text-xs border border-amber-400 rounded-[3px] focus:ring-1 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-900 dark:border-amber-500/50 dark:text-white shadow-sm"
                                                            placeholder="0.00">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($activities->isEmpty())<p class="text-xs text-gray-400 col-span-3 py-2">{{ __('No activities configured.') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Animals -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Wildlife /
                                    Animals Expected') }}</label>
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-[3px] p-3 bg-gray-50/50 dark:bg-gray-800/30 grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                    @foreach($allAnimals as $animal)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="dayForm.animals" value="{{ $animal->id }}"
                                                class="text-indigo-600 rounded">
                                            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $animal->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Adult
                                        Price
                                        Override') }}</label>
                                    <input type="number" step="0.01" wire:model="dayForm.adult_price" class="form-input"
                                        placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Child
                                        Price
                                        Override') }}</label>
                                    <input type="number" step="0.01" wire:model="dayForm.child_price" class="form-input"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Day
                                    Description') }}</label>
                                <x-admin.rich-text wire:model="dayForm.description" label="" />
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <x-admin.image-upload :image="$dayForm['featured_image']"
                                    :existing="$dayForm['existing_image']" label="{{ __('Day Featured Image') }}"
                                    id="day_featured_image_input" model="dayForm.featured_image"
                                    clearAction="$set('dayForm.featured_image', null); $set('dayForm.existing_image', null)" />
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                                <button wire:click="saveDay" class="btn-primary">
                                    <x-icon name="check" class="w-4 h-4 mr-1" wire:loading.remove wire:target="saveDay" />
                                    <x-icon name="arrow-path" class="w-4 h-4 mr-1 animate-spin" wire:loading
                                        wire:target="saveDay" />{{ __('Save Day') }}</button>
                            </div>
                        </div>
                    @else
                        <div
                            class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-[3px] text-center">
                            <x-icon name="cursor-arrow-rays" class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" />
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Select a day on the left to edit
                                it') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('or click') }}<strong>+</strong>{{ __('to add a
                                new
                                day') }}</p>
                        </div>
                    @endif
                </div>
            </div>{{-- end flex-row --}}
        </div>{{-- end TAB 2 --}}

        <!-- TAB 3: Pricing & Terms -->
        <div x-show="activeTab === 'pricing'"
            class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-6 space-y-8">
            <div>
                <h3
                    class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-6">{{ __('Quote Breakdown') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Currency') }}</label>
                        <select wire:model.live="itinerary.currency" class="form-input">
                            @foreach($currencies as $cur)
                                <option value="{{ $cur->code }}">{{ $cur->code }} ({{ $cur->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Total
                            Calculated
                            Price') }}</label>
                        <div
                            class="form-input bg-gray-50 dark:bg-gray-900 font-bold text-lg text-indigo-600 dark:text-indigo-400 flex items-center h-[38px]">
                            {{ $itinerary->currency }} {{ number_format($itinerary->total_price, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Included items (Simple string array management using Alpine) -->
                <div x-data="{ 
                    newItem: '', 
                    items: @entangle('included'),
                    addItem() {
                        if(this.newItem.trim() !== '') {
                            let temp = Array.isArray(this.items) ? [...this.items] : [];
                            temp.push(this.newItem.trim());
                            this.items = temp;
                            this.newItem = '';
                        }
                    },
                    removeItem(index) {
                        let temp = [...this.items];
                        temp.splice(index, 1);
                        this.items = temp;
                    }
                }">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">{{ __('Included in Quote') }}</h3>
                    @can('edit itineraries')
                        <div class="flex gap-2 mb-3">
                            <input type="text" x-model="newItem" @keydown.enter.prevent="addItem" class="form-input text-sm"
                                placeholder="{{ __('e.g. Park entry fees') }}">
                            <button type="button" @click="addItem"
                                class="btn-secondary text-sm px-3 whitespace-nowrap">{{ __('Add') }}</button>
                        </div>
                    @endcan
                    <ul class="space-y-2">
                        <template x-for="(item, index) in items" :key="index">
                            <li
                                class="flex items-center justify-between bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded p-2">
                                <div class="flex items-center gap-2">
                                    <x-icon name="check-circle" class="w-4 h-4 text-green-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="item"></span>
                                </div>
                                @can('edit itineraries')
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-400 hover:text-red-600"><x-icon name="x-mark"
                                            class="w-4 h-4" /></button>
                                @endcan
                            </li>
                        </template>
                        <li x-show="!items || items.length === 0" class="text-sm text-gray-500 italic py-2">{{ __('No items
                            added
                            yet.') }}</li>
                    </ul>
                </div>

                <!-- Excluded items -->
                <div x-data="{ 
                    newItem: '', 
                    items: @entangle('excluded'),
                    addItem() {
                        if(this.newItem.trim() !== '') {
                            let temp = Array.isArray(this.items) ? [...this.items] : [];
                            temp.push(this.newItem.trim());
                            this.items = temp;
                            this.newItem = '';
                        }
                    },
                    removeItem(index) {
                        let temp = [...this.items];
                        temp.splice(index, 1);
                        this.items = temp;
                    }
                }">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">{{ __('Excluded from Quote') }}</h3>
                    @can('edit itineraries')
                        <div class="flex gap-2 mb-3">
                            <input type="text" x-model="newItem" @keydown.enter.prevent="addItem" class="form-input text-sm"
                                placeholder="{{ __('e.g. International flights') }}">
                            <button type="button" @click="addItem"
                                class="btn-secondary text-sm px-3 whitespace-nowrap">{{ __('Add') }}</button>
                        </div>
                    @endcan
                    <ul class="space-y-2">
                        <template x-for="(item, index) in items" :key="index">
                            <li
                                class="flex items-center justify-between bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded p-2">
                                <div class="flex items-center gap-2">
                                    <x-icon name="x-circle" class="w-4 h-4 text-red-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="item"></span>
                                </div>
                                @can('edit itineraries')
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-400 hover:text-red-600"><x-icon name="x-mark"
                                            class="w-4 h-4" /></button>
                                @endcan
                            </li>
                        </template>
                        <li x-show="!items || items.length === 0" class="text-sm text-gray-500 italic py-2">{{ __('No items
                            added
                            yet.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs text-gray-500 italic"><x-icon name="check-circle"
                        class="w-3 h-3 inline text-green-500" />{{ __('Auto-saved') }}</p>
            </div>
        </div>

    </div>

</div>