<div x-data="{ open: false }" @open-planning-modal.window="open = true">
    <template x-teleport="body">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
            <div @click.away="open = false" x-show="open" x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95" class="relative bg-white dark:bg-gray-900 rounded-[3px] shadow-2xl max-w-xl w-full max-h-[95vh] overflow-y-auto custom-scrollbar border border-gray-100 dark:border-gray-800">
                
                <!-- Close Button -->
                <button @click="open = false" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors focus:outline-none z-10">
                    <x-icon name="x-mark" class="w-6 h-6" />
                </button>

                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-850/50">
                    <h3 class="text-xl font-bold font-brand text-gray-900 dark:text-white flex items-center">
                        <x-icon name="map" class="w-5 h-5 mr-2 text-brand-primary" />{{ __('Start Planning Your Safari') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Tell us about your trip and we\'ll get back to you with a custom proposal.') }}</p>
                </div>

                <div class="p-6">
                    <form class="request-form space-y-4" @submit="setTimeout(() => { if ($el.querySelector('.btn-primary').innerText !== 'Sending...') open = false }, 1000)">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Email Address') }}</label>
                            <input type="email" name="email" placeholder="john@example.com" required
                                class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Trip Type') }}</label>
                            <select name="trip_type" required class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                                <option value="">{{ __('Select a trip type') }}</option>
                                @foreach(\App\Models\TourType::where('is_active', true)->get() as $type)
                                    <option value="{{ $type->name }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Arrival Date') }}</label>
                            <input type="date" name="arrival_date" required min="{{ date('Y-m-d') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Adults') }}</label>
                                <input type="number" name="adults" min="1" max="100" value="1" required
                                    class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">{{ __('Children') }}</label>
                                <input type="number" name="children" min="0" max="100" value="0" required
                                    class="w-full border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-850 p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary dark:text-white">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button type="submit" class="btn-primary w-full py-4 bg-brand-primary hover:bg-brand-primary-hover text-white dark:text-gray-950 font-bold text-base rounded-[3px] transition-all duration-300 shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 hover:-translate-y-0.5 flex items-center justify-center">{{ __('Submit Request') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </template>
</div>
