<div>
    <!-- Trigger Button -->
    <button wire:click="openQuoteModal"
       class="w-full py-4 px-6 bg-brand-primary hover:bg-brand-primary-hover text-white dark:text-gray-950 text-center font-bold text-lg rounded-[3px] transition-all duration-300 shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 hover:-translate-y-0.5 flex items-center justify-center cursor-pointer">{{ __('Request a Quote') }}<x-icon name="arrow-right" class="w-5 h-5 ml-2" />
    </button>

    <!-- Modal Backdrop & Window -->
    @if($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
                <div class="relative bg-white dark:bg-gray-900 rounded-[3px] shadow-2xl max-w-3xl w-full max-h-[95vh] overflow-y-auto custom-scrollbar border border-gray-100 dark:border-gray-800 animate-scale-up">
                <!-- Close Button -->
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <x-icon name="x-mark" class="w-6 h-6" />
                </button>

                @if($success)
                    <!-- Success View -->
                    <div class="p-8 text-center space-y-6">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto">
                            <x-icon name="check" class="w-10 h-10" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-1xl font-bold text-gray-900 dark:text-white">{{ __('Quote Request Received!') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Your safari quote request has been successfully submitted. Our team will contact you shortly with your custom proposal.') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-[3px] inline-block">
                            <span class="text-xs font-semibold text-gray-400 uppercase block tracking-wider">{{ __('Proposal Reference') }}</span>
                            <span class="text-lg font-mono font-bold text-gray-900 dark:text-white">{{ $referenceNumber }}</span>
                        </div>
                        <button wire:click="$set('showModal', false)"
                                class="w-full py-3 px-6 bg-gray-900 hover:bg-gray-800 dark:bg-brand-primary dark:hover:bg-brand-primary-hover dark:text-gray-950 text-white font-semibold rounded-[3px] transition-colors">{{ __('Close Window') }}</button>
                    </div>
                @else
                    <!-- Form View -->
                    <div class="p-6 md:p-8 space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Request a Quote') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Tour:') }}<span class="font-bold text-brand-primary">{{ $tour->name }}</span></p>
                        </div>

                        <form wire:submit.prevent="submit" class="space-y-4">
                            @if(!auth()->check())<!-- Contact Details for Guests -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Full Name') }}</label>
                                        <input type="text" wire:model="name" placeholder="{{ __('John Doe') }}"
                                               class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Email Address') }}</label>
                                            <input type="email" wire:model="email" placeholder="john@example.com"
                                                   class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Phone Number') }}</label>
                                            <input type="text" wire:model="phone" placeholder="+255 123 456"
                                                   class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                            @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Travel Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Start Date') }}</label>
                                    <input type="date" wire:model.live="start_date"
                                           class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                    @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('End Date') }}</label>
                                    <input type="date" wire:model="end_date" readonly disabled
                                           class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-100 dark:bg-gray-800 p-2.5 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-[10px] text-gray-400 mt-1 block">{{__('Automatically set for')}} {{ $tour->duration_days }} {{__('days')}}.</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{__('Adults')}} (18+)</label>
                                    <input type="number" wire:model.live="adults" min="1" max="100"
                                           class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                    @error('adults') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Children') }}</label>
                                    <input type="number" wire:model.live="children" min="0" max="100"
                                           class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                    @error('children') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            @if(count($available_extra_activities) > 0)
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">{{ __('Optional Extras (Per Person)') }}</label>
                                        <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                            @foreach($available_extra_activities as $extra)
                                                <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-[3px] bg-white dark:bg-gray-850 cursor-pointer hover:border-brand-primary transition-colors">
                                                    <input type="checkbox" wire:model.live="selected_extra_activities" value="{{ $extra['id'] }}"
                                                           class="w-4 h-4 text-brand-primary border-gray-300 dark:border-gray-600 rounded focus:ring-brand-primary">
                                                    <div class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ $extra['name'] }}
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                            +{{ $currency_symbol }}{{ number_format($extra['price'], 2) }} <span class="text-xs font-normal text-gray-500">/ {{__('adult')}}</span>
                                                        </div>
                                                        <div class="text-xs font-bold text-brand-primary">
                                                            +{{ $currency_symbol }}{{ number_format($extra['price'] * 0.5, 2) }} <span class="text-[10px] font-normal text-brand-primary/80">/ {{__('child')}}</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Estimated Budget') }}</label>
                                    <input type="text" wire:model="estimated_budget" placeholder="e.g. $5,000"
                                           class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                    @error('estimated_budget') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Additional Information') }}</label>
                                    <textarea wire:model="additional_information" rows="3" placeholder="{{ __('Any special requests, dietary requirements, or preferences...') }}"
                                              class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white resize-none"></textarea>
                                    @error('additional_information') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Pricing Estimation Block -->
                            @php
                                $calculatedChildPrice = $this->calculatedChildPrice;
                                $calculatedAdultPrice = $this->calculatedAdultPrice;
                            @endphp
                            <div class="bg-brand-light/50 dark:bg-brand-light/20 border border-brand-light dark:border-gray-800 p-4 rounded-[3px] space-y-2">
                                <div class="flex justify-between items-center text-xs font-medium text-gray-600 dark:text-gray-400">
                                    <span>Adults ({{ $adults }} × {{ $currency_symbol }}{{ number_format($calculatedAdultPrice, 2) }}):</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $currency_symbol }}{{ number_format($calculatedAdultPrice * $adults, 2) }}</span>
                                </div>
                                @if($children > 0)
                                    <div class="flex justify-between items-center text-xs font-medium text-gray-600 dark:text-gray-400">
                                        <span>Children ({{ $children }} × {{ $currency_symbol }}{{ number_format($calculatedChildPrice, 2) }}):</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $currency_symbol }}{{ number_format($calculatedChildPrice * $children, 2) }}</span>
                                    </div>
                                @endif
                                @if(count($selected_extra_activities) > 0)
                                    @php
                                        $adultExtrasTotal = 0;
                                        $childExtrasTotal = 0;
                                        foreach($available_extra_activities as $extra) {
                                            if(in_array($extra['id'], $selected_extra_activities)) {
                                                $adultExtrasTotal += ($extra['price'] * $adults);
                                                $childExtrasTotal += (($extra['price'] * 0.5) * $children);
                                            }
                                        }
                                        $extrasTotal = $adultExtrasTotal + $childExtrasTotal;
                                    @endphp
                                    <div class="flex justify-between items-center text-xs font-medium text-gray-600 dark:text-gray-400">
                                        <span>{{ __('Optional Extras (Adults):') }}</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $currency_symbol }}{{ number_format($adultExtrasTotal, 2) }}</span>
                                    </div>
                                    @if($children > 0)
                                        <div class="flex justify-between items-center text-xs font-medium text-gray-600 dark:text-gray-400">
                                            <span>{{ __('Optional Extras (Children):') }}</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $currency_symbol }}{{ number_format($childExtrasTotal, 2) }}</span>
                                        </div>
                                    @endif
                                @endif
                                <div class="border-t border-brand-primary/20 dark:border-gray-800 pt-2 flex justify-between items-center text-sm">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ __('Estimated Quote:') }}</span>
                                    <span class="font-black text-brand-primary text-lg">
                                        {{ $currency_symbol }}{{ number_format(($calculatedAdultPrice * $adults) + ($calculatedChildPrice * $children) + ($extrasTotal ?? 0), 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" wire:loading.attr="disabled"
                                    class="w-full py-3 px-6 bg-brand-primary hover:bg-brand-primary-hover text-white dark:text-gray-950 font-bold rounded-[3px] transition-colors flex items-center justify-center gap-2 cursor-pointer relative overflow-hidden">
                                <span wire:loading.remove>{{ __('Submit Quote Request') }}</span>
                                <div wire:loading>
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white dark:text-gray-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
