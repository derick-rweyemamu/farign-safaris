@php
    $generalSetting = app(\App\Settings\GeneralSetting::class);
@endphp

<div
    class="min-h-screen flex items-center justify-center p-3 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-lg">
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 mx-auto mb-4 rounded-[3px] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                <img src="{{ asset('storage/' . $generalSetting->favicon) }}" alt="icon"
                    class="max-h-full max-w-full object-contain">
            </div>
            <h1 class="text-3xl font-bold text-white">{{ __('Client Registration') }}</h1>
            <p class="text-indigo-300 text-sm mt-1">{{ __('Register to manage your bookings and profile') }}</p>
        </div>

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[3px] p-8 shadow-2xl">
            <form wire:submit="register" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Salutation') }}</label>
                        <select wire:model="salutation"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm appearance-none">
                            <option value="" class="bg-slate-900 text-white">{{ __('Select') }}</option>
                            @foreach($salutationOptions as $value => $label)
                                <option value="{{ $value }}" class="bg-slate-900 text-white">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('salutation') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Full Name') }}</label>
                        <input type="text" wire:model="name" id="reg-name"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                            placeholder="{{ __('John Doe') }}" autofocus>
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Email Address') }}</label>
                        <input type="email" wire:model="email" id="reg-email"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                            placeholder="john@example.com">
                        @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Phone Number') }}</label>
                        <input type="text" wire:model="phone" id="reg-phone"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                            placeholder="+1 555 000 0000">
                        @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Address') }}</label>
                        <input type="text" wire:model="address_line1" id="reg-address"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                            placeholder="{{ __('123 Main St') }}">
                        @error('address_line1') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Password') }}</label>
                        <input type="password" wire:model="password" id="reg-password"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                            placeholder="{{ __('Min 8 characters') }}">
                        @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-indigo-200 mb-1.5">{{ __('Confirm Password') }}</label>
                        <input type="password" wire:model="password_confirmation" id="reg-confirm"
                            class="w-full bg-white/5 border border-white/10 rounded-[3px] px-4 py-3 text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm"
                            placeholder="{{ __('Repeat password') }}">
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full justify-center !py-3.5 !text-base gap-2 mt-4"
                    wire:loading.attr="disabled">
                    <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
                    <span wire:loading.remove>{{ __('Create Client Account') }}</span>
                    <span wire:loading>{{ __('Creating...') }}</span>
                </button>
            </form>

            <p class="text-center text-sm text-indigo-300 mt-6">{{ __('Already have an account?') }}<a href="{{ route('login') }}"
                    class="text-[var(--color-primary-500)] hover:text-white font-semibold transition-colors"
                    wire:navigate>{{ __('Sign in') }}</a>
            </p>
        </div>
    </div>
</div>