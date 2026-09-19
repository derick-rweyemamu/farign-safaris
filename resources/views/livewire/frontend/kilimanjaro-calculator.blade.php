@php
    $imageUrl = null;
    if (!empty($customImage)) {
        $trimmed = trim($customImage);
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://') || str_starts_with($trimmed, '//') || str_starts_with($trimmed, 'data:image/')) {
            $imageUrl = $trimmed;
        } else {
            $imageUrl = asset(ltrim($trimmed, '/\\'));
        }
    }

    if (empty($imageUrl)) {
        $imageUrl = ' ';
    }
@endphp

<div class="max-w-7xl mx-auto my-20 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-stretch">

        {{-- CALCULATOR CONTAINER --}}
        {{-- On mobile/tablet: order-1 (TOP) | On desktop (lg): order-2 (RIGHT) --}}
        <div
            class="order-1 lg:order-2 lg:col-span-7 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8 shadow-xl flex flex-col justify-between transition-all duration-300">
            <div>
                <!-- Calculator Header -->
                <div class="text-left mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                    <span class="inline-flex items-center">{{ __('Summit Estimator') }}</span>
                    <h2 class="text-2xl md:text-3xl font-bold font-brand text-[#2E1F12] dark:text-white m-0">
                        {{ $customTitle ?: 'Kilimanjaro Success Rate Calculator' }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 m-0">
                        {{ $customSubtitle ?: 'Estimate your summit success probability based on acclimatization and route profiles.' }}
                    </p>
                </div>

                <!-- Step Progress Tracker -->
                <div
                    class="flex items-center justify-between max-w-md mb-8 bg-gray-50 dark:bg-gray-800/40 p-2.5 rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $step >= 1 ? 'bg-[#fbb158] text-[#2E1F12] shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">{{__('1')}}</span>
                        <span
                            class="text-xs font-bold {{ $step >= 1 ? 'text-[#2E1F12] dark:text-white' : 'text-gray-400' }}">{{ __('Package') }}</span>
                    </div>
                    <div class="flex-1 h-0.5 mx-3 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $step >= 2 ? 'bg-[#fbb158] text-[#2E1F12] shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">{{__('2')}}</span>
                        <span
                            class="text-xs font-bold {{ $step >= 2 ? 'text-[#2E1F12] dark:text-white' : 'text-gray-400' }}">{{ __('Profile') }}</span>
                    </div>
                    <div class="flex-1 h-0.5 mx-3 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $step >= 3 ? 'bg-[#fbb158] text-[#2E1F12] shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">{{__('3')}}</span>
                        <span
                            class="text-xs font-bold {{ $step >= 3 ? 'text-[#2E1F12] dark:text-white' : 'text-gray-400' }}">{{ __('Result') }}</span>
                    </div>
                </div>

                <!-- Step 1: Tour Selection & Duration -->
                @if($step === 1)
                    <div class="space-y-6">
                        <div>
                            <label
                                class="block text-sm font-bold text-[#2E1F12] dark:text-white uppercase tracking-wider mb-3">{{__('1. Select Climb Package')}}</label>
                            @if($tours->isNotEmpty())<select wire:model.live="selectedTourId"
                                    class="form-input w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-700 py-3 px-4 rounded-xl focus:ring-2 focus:ring-[#fbb158] focus:border-[#fbb158] shadow-sm text-sm">
                                    @foreach($tours as $tour)
                                        <option value="{{ $tour->id }}">{{ $tour->name }} ({{ $tour->duration_days }}
                                            {{__('Days')}})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <p class="text-sm text-red-500">
                                    {{ __('No climbing tours found in the database. Please add Hiking tours
                                                                                                                                            first.') }}
                                </p>
                            @endif
                        </div>

                        <div
                            class="bg-[#FAF6EF]/50 dark:bg-gray-800/30 rounded-xl p-5 border border-[#fbb158]/20 dark:border-gray-800">
                            <label
                                class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('Climb
                                                                                    Duration (Days)') }}</label>
                            <p class="text-3xl font-extrabold text-[#fbb158] m-0 font-mono">{{ $durationDays }}
                                {{__('Days')}}
                            </p>
                            <span
                                class="text-[12px] text-gray-500 dark:text-gray-400 block mt-1">{{ __('This duration is pulled dynamically from the selected tour package for optimal acclimatization mapping.') }}</span>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="button" wire:click="$set('step', 2)" @disabled(!$selectedTourId)
                                class="bg-[#fbb158] hover:bg-[#e09f4f] text-[#2E1F12] px-6 py-3.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 cursor-pointer border-none shadow-md disabled:opacity-50">{{ __('Next Step') }}<x-icon
                                    name="chevron-right" class="w-4 h-4 text-[#2E1F12]" />
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Step 2: Climber Profile selection -->
                @if($step === 2)
                    <div class="space-y-6">
                        <div>
                            <label
                                class="block text-sm font-bold text-[#2E1F12] dark:text-white uppercase tracking-wider mb-3">{{__('2. Age Bracket')}}</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['youth' => 'Under 20', 'young-adult' => '20 - 35', 'adult' => '36 - 55', 'senior' => 'Over 55'] as $key => $label)
                                    <label
                                        class="border rounded-xl p-3 text-center cursor-pointer transition-all hover:bg-[#FAF6EF]/30 dark:hover:bg-[#FAF6EF]/5 {{ $climberAge === $key ? 'border-[#fbb158] bg-[#FAF6EF]/60 dark:bg-[#FAF6EF]/10 text-[#fbb158] font-bold shadow-sm' : 'border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300' }}">
                                        <input type="radio" wire:model.live="climberAge" value="{{ $key }}" class="sr-only">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-[#2E1F12] dark:text-white uppercase tracking-wider mb-3">{{__('3. Fitness Level')}}</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['beginner' => 'Beginner (occasional walk)', 'moderate' => 'Moderate (active weekly)', 'active' => 'Active (workout 3x week)', 'athletic' => 'Athletic (intensive training)'] as $key => $label)
                                    <label
                                        class="border rounded-xl p-3 text-center cursor-pointer transition-all hover:bg-[#FAF6EF]/30 dark:hover:bg-[#FAF6EF]/5 {{ $fitnessLevel === $key ? 'border-[#fbb158] bg-[#FAF6EF]/60 dark:bg-[#FAF6EF]/10 text-[#fbb158] font-bold shadow-sm' : 'border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-300' }}">
                                        <input type="radio" wire:model.live="fitnessLevel" value="{{ $key }}" class="sr-only">
                                        <span class="text-xs leading-snug block">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div
                            class="bg-[#FAF6EF]/40 dark:bg-[#FAF6EF]/5 border border-gray-150 dark:border-gray-800 rounded-xl p-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model="preAcclimatized"
                                    class="w-5 h-5 text-[#fbb158] rounded border-gray-300 focus:ring-[#fbb158]">
                                <div>
                                    <span
                                        class="block text-sm font-semibold text-[#2E1F12] dark:text-white">{{ __('I plan to pre-acclimatize') }}</span>
                                    <span
                                        class="block text-xs text-gray-500 dark:text-gray-400">{{__('e.g. Climbing Mount Meru or spending time above 3,000m directly before starting Kilimanjaro. (+5% success boost)')}}</span>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-between pt-4">
                            <button type="button" wire:click="$set('step', 1)"
                                class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 px-5 py-3 rounded-xl font-semibold text-sm transition-all flex items-center gap-2 cursor-pointer border-none">
                                <x-icon name="chevron-left" class="w-4 h-4 text-gray-500" />{{ __('Back') }}</button>
                            <button type="button" wire:click="calculate"
                                class="bg-[#fbb158] hover:bg-[#e09f4f] text-[#2E1F12] px-6 py-3.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 cursor-pointer border-none shadow-md">{{ __('Calculate Success Rate') }}<x-icon
                                    name="bolt" class="w-4 h-4 text-[#2E1F12] fill-current" />
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Step 3: Success Score & Personal Advice -->
                @if($step === 3)
                    <div class="space-y-6">
                        <div
                            class="text-center bg-[#FAF6EF]/40 dark:bg-[#FAF6EF]/5 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 flex flex-col items-center">
                            <!-- Circular Success Dial -->
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="72" cy="72" r="64" class="stroke-current text-gray-200 dark:text-gray-800"
                                        stroke-width="8" fill="transparent" />
                                    <circle cx="72" cy="72" r="64"
                                        class="stroke-current text-[#fbb158] transition-all duration-1000" stroke-width="10"
                                        fill="transparent" stroke-dasharray="402"
                                        stroke-dashoffset="{{ 402 - (402 * $successRate / 100) }}" />
                                </svg>
                                <div class="absolute flex flex-col items-center">
                                    <span
                                        class="text-4xl font-extrabold font-mono text-[#2E1F12] dark:text-white">{{ $successRate }}%</span>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ __('Probability') }}</span>
                                </div>
                            </div>

                            <div class="mt-5">
                                <h4 class="text-lg font-bold text-[#2E1F12] dark:text-white m-0">
                                    {{ \App\Models\Tour::find($selectedTourId)?->name }}
                                </h4>
                                <p class="text-xs text-[#fbb158] font-semibold uppercase tracking-wider mt-1.5 m-0">
                                    {{ __('Summit Success Rate') }}
                                </p>
                            </div>
                        </div>

                        <!-- Custom Advice Callout -->
                        <div class="border-l-4 border-[#fbb158] bg-[#FAF6EF]/20 dark:bg-gray-800/40 p-4 rounded-r-xl">
                            <h5 class="text-xs font-bold uppercase tracking-wider text-[#2E1F12] dark:text-white mb-2">
                                {{ __('Our Climb Expert\'s Assessment:') }}
                            </h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed m-0">{{ $this->advice }}</p>
                        </div>

                        <!-- Lead Capture Section -->
                        @if(!$leadSubmitted)
                            <div
                                class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-5 border border-gray-150 dark:border-gray-800 mt-6">
                                <h4 class="text-sm font-bold text-[#2E1F12] dark:text-white uppercase tracking-wider mb-2">
                                    {{ __('Get a Personalized Proposal') }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    {{ __('Enter your details below to receive a custom itinerary and free planning support from our local experts.') }}
                                </p>

                                <form wire:submit.prevent="submitLead" class="space-y-3.5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{__('Your Name')}}
                                                *</label>
                                            <input type="text" wire:model="name" class="form-input text-sm py-2 rounded-lg"
                                                placeholder="{{ __('e.g. John Doe') }}">
                                            @error('name') <span
                                            class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{__('Email Address')}}
                                                *</label>
                                            <input type="email" wire:model="email" class="form-input text-sm py-2 rounded-lg"
                                                placeholder="e.g. john@example.com">
                                            @error('email') <span
                                            class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone Number (optional)') }}</label>
                                        <input type="text" wire:model="phone" class="form-input text-sm py-2 rounded-lg"
                                            placeholder="e.g. +1 555-0100">
                                        @error('phone') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-[#fbb158] hover:bg-[#e09f4f] text-[#2E1F12] py-3 rounded-xl font-bold text-sm transition-all cursor-pointer border-none shadow-md">{{ __('Send Me My Free Proposal') }}</button>
                                </form>
                            </div>
                        @else
                            <div
                                class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-xl p-5 text-center">
                                <x-icon name="check-circle" class="w-10 h-10 text-emerald-500 mx-auto mb-2" />
                                <h4
                                    class="text-sm font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider m-0">
                                    {{ __('Proposal Request Received') }}
                                </h4>
                                <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1 mb-0">{{__('Our local climbing experts have received your calculation detail. We will reach out to you within 24 hours with a custom itinerary.')}}</p>
                            </div>
                        @endif

                        <div class="flex justify-start pt-4">
                            <button type="button" wire:click="restart"
                                class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 px-5 py-3 rounded-xl font-semibold text-sm transition-all flex items-center gap-2 cursor-pointer border-none">
                                <x-icon name="arrow-path" class="w-4 h-4 text-gray-500" />{{ __('Start Over') }}</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- KILIMANJARO IMAGE CONTAINER --}}
        {{-- On mobile/tablet: order-2 (BOTTOM) | On desktop (lg): order-1 (LEFT) --}}
        <div
            class="order-2 lg:order-1 lg:col-span-5 relative group overflow-hidden rounded-2xl min-h-[380px] lg:min-h-[500px] shadow-xl border border-gray-100 dark:border-gray-800 flex flex-col justify-end">
            <img src="{{ $imageUrl }}" alt="{{ $customHeading ?: __('Mount Kilimanjaro Summit Climb') }}"
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

            {{-- Dark Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#140c05] via-[#140c05]/60 to-transparent"></div>

            {{-- Content on Image --}}
            <div class="relative z-10 p-6 md:p-8 text-white space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md tracking-widest shadow-md">
                    {{ $customBadge ?: __('Mount Kilimanjaro (5,895m)') }}
                </span>

                <h3 class="text-2xl md:text-3xl font-bold brand-secondary m-1">
                    {{ $customHeading ?: __('Conquer the Roof of Africa') }}
                </h3>

                <p class="text-xs md:text-sm text-gray-200/90 leading-relaxed m-0">
                    {{ $customDescription ?: __('Choosing the right route duration and acclimatization strategy can raise your summit success rate from 60% to over 95%.') }}
                </p>

                <div class="pt-2 grid grid-cols-2 gap-3 border-t border-white/15">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                        <span class="block text-[10px] text-gray-300 uppercase tracking-wider font-semibold">{{ __('Highest Point') }}</span>
                        <span class="text-sm font-bold text-[#fbb158]">{{ __('Uhuru Peak') }}</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                        <span class="block text-[10px] text-gray-300 uppercase tracking-wider font-semibold">{{ __('Optimal Duration') }}</span>
                        <span class="text-sm font-bold text-[#fbb158]">{{__('5 - 10 Days')}}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>