<div>
    <!-- Trigger Button -->
    <button wire:click="openModal"
        class="inline-block bg-[#72b02a] hover:bg-[#619524] active:bg-[#527d1e] text-white text-xs font-bold py-2.5 px-4 rounded-[3px] shadow-sm transition-all duration-150 transform active:scale-95 cursor-pointer">{{ __('Join Group') }}</button>

    <!-- Modal Backdrop & Window -->
    @if($showModal)
        <template x-teleport="body">
            <div
                class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
                <div
                    class="relative bg-white dark:bg-gray-900 rounded-[3px] shadow-2xl max-w-lg w-full max-h-[95vh] overflow-y-auto border border-gray-100 dark:border-gray-800 animate-scale-up">
                    <!-- Close Button -->
                    <button wire:click="$set('showModal', false)"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>

                    @if($success)
                        <!-- Success View -->
                        <div class="p-8 text-center space-y-6">
                            <div
                                class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto">
                                <x-icon name="check" class="w-10 h-10" />
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ __('Booking Request Received!') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{__('Your request to join the group tour has been successfully received. A consultant will review your details and contact you shortly with your custom proposal.')}}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-[3px] inline-block">
                                <span class="text-xs font-semibold text-gray-400 uppercase block tracking-wider">{{ __('Proposal
                                            Reference') }}</span>
                                <span
                                    class="text-lg font-mono font-bold text-gray-900 dark:text-white">{{ $referenceNumber }}</span>
                            </div>
                            <button wire:click="$set('showModal', false)"
                                class="w-full py-3 px-6 bg-gray-900 hover:bg-gray-800 dark:bg-brand-primary dark:hover:bg-brand-primary-hover dark:text-gray-950 text-white font-semibold rounded-[3px] transition-colors">{{ __('Close Window') }}</button>
                        </div>
                    @else
                        <!-- Form View -->
                        <div class="p-6 md:p-8 space-y-6">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Join Group Tour') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Tour Name:') }}<span
                                        class="font-bold text-brand-primary">{{ $groupTour->title }}</span>
                                </p>
                                @if($groupTour->start_date)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Departure:') }}<span
                                            class="font-semibold text-gray-800 dark:text-gray-200">{{ $groupTour->start_date->format('F d, Y') }}</span>
                                        @if($groupTour->end_date)
                                            {{__('to')}}<span
                                                class="font-semibold text-gray-800 dark:text-gray-200">{{ $groupTour->end_date->format('F d, Y') }}</span>
                                        @endif
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Price:') }}<span
                                        class="font-semibold text-brand-primary">${{ number_format($groupTour->price, 0) }} {{__('per person')}}</span>
                                </p>
                            </div>

                            <form wire:submit.prevent="submit" class="space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Full Name -->
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Full
                                                    Name') }}</label>
                                        <input type="text" wire:model="name" placeholder="{{ __('John Doe') }}"
                                            class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Email & Phone Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Email
                                                        Address') }}</label>
                                            <input type="email" wire:model="email" placeholder="john@example.com"
                                                class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Phone
                                                        Number') }}</label>
                                            <input type="text" wire:model="phone" placeholder="+255 123 456"
                                                class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                            @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Country of Origin  and Number of Seats to Book Grid-->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Number of Seats -->
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Number
                                                        of Seats to Book') }}</label>
                                            <input type="number" wire:model.live="seats" min="1"
                                                max="{{ $groupTour->available_seats ?? 100 }}"
                                                class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                            @error('seats') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Country
                                                        of Origin') }}</label>
                                            <select wire:model="country_id"
                                                class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                                <option value="">{{ __('Select your country') }}</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('country_id') <span
                                            class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Additional Details -->
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Additional
                                                    Information') }}</label>
                                        <textarea wire:model="additional_information" rows="3"
                                            placeholder="{{ __('Tell us if you are traveling with companions, have dietary needs, or special requirements...') }}"
                                            class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white resize-none"></textarea>
                                        @error('additional_information') <span
                                        class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Pricing Estimation Block -->
                                <div
                                    class="bg-brand-light/50 dark:bg-brand-light/20 border border-brand-light dark:border-gray-800 p-4 rounded-[3px] space-y-2">
                                    <div
                                        class="flex justify-between items-center text-xs font-medium text-gray-600 dark:text-gray-400">
                                        <span>Seats Requested ({{ $seats }} ×
                                            ${{ number_format($groupTour->price, 2) }}):</span>
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">${{ number_format($groupTour->price * $seats, 2) }}</span>
                                    </div>
                                    <div
                                        class="border-t border-brand-primary/20 dark:border-gray-800 pt-2 flex justify-between items-center text-sm">
                                        <span
                                            class="font-bold text-gray-700 dark:text-gray-300">{{ __('Total Price Quote:') }}</span>
                                        <span class="font-black text-brand-primary text-lg">
                                            ${{ number_format($groupTour->price * $seats, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" wire:loading.attr="disabled"
                                    class="w-full py-3 px-6 bg-[#72b02a] hover:bg-[#619524] active:bg-[#527d1e] text-white font-bold rounded-[3px] transition-colors flex items-center justify-center gap-2 cursor-pointer relative overflow-hidden">
                                    <span wire:loading.remove>{{ __('Request to Join Group') }}</span>
                                    <div wire:loading>
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span>{{ __('Processing...') }}</span>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </template>
    @endif
</div>