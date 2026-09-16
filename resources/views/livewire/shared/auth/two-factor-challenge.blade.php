@php
    $generalSetting = app(\App\Settings\GeneralSetting::class);
    $authImg = $generalSetting?->auth_image;
@endphp

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-slate-50 dark:bg-slate-900">

    <!-- Left Side: Branding / Visuals -->
    <div class="hidden lg:flex relative overflow-hidden bg-slate-950 justify-center items-center p-12">
        @if($authImg)
            <img src="{{ asset('storage/' . $authImg) }}" alt="Authentication Background"
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/30"></div>
        @else
            <!-- Abstract Animated Gradients Fallback -->
            <div
                class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[100px] animate-pulse">
            </div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-amber-500/20 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"
                style="animation-delay: 2s;"></div>
            <div class="absolute top-[40%] left-[60%] w-72 h-72 bg-purple-600/20 rounded-full mix-blend-screen filter blur-[80px] animate-pulse"
                style="animation-delay: 4s;"></div>
        @endif

        <div class="relative z-10 text-center max-w-lg">
            @if($generalSetting?->favicon)
                <div
                    class="w-24 h-24 mx-auto mb-8 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-2xl p-3 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                    <img src="{{ asset('storage/' . $generalSetting->favicon) }}" alt="icon"
                        class="max-h-full max-w-full object-contain">
                </div>
            @else
                <div
                    class="w-24 h-24 mx-auto mb-8 rounded-[3px] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl shadow-indigo-500/40">
                    <x-icon name="lock-closed" class="w-12 h-12 text-white" />
                </div>
            @endif
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4">{{ __('Two-Factor Authentication') }}
            </h1>
            <p class="text-lg text-indigo-100/80 font-light">
                {{ __('Secure your account with a secondary verification step.') }}
            </p>
        </div>
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30 pointer-events-none">
        </div>
    </div>

    <!-- Right Side: Challenge Form -->
    <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 relative">
        <div class="w-full max-w-md relative z-10">

            <div class="text-center lg:text-left mb-8">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">
                    @if($useRecoveryCode) Recovery Code Verification @else Security Verification @endif
                </h2>
                @if($useRecoveryCode)
                    <p class="text-slate-500 dark:text-slate-400 text-sm">
                        {{ __('Please enter one of your emergency recovery codes.') }}
                    </p>
                @elseif($type === 'app')
                    <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('Please open your Authenticator App and enter the
                                                    6-digit code.') }}</p>
                @else
                    <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('We have sent a 6-digit verification code to your
                                                    email.') }}</p>
                @endif
            </div>

            @if(session('status'))
                <div
                    class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-[3px] text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center gap-3">
                    <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="verify" class="space-y-6">
                <!-- Code Input -->
                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        @if($useRecoveryCode) Recovery Code @else Authentication Code @endif
                    </label>
                    <input type="text" id="code" wire:model="code"
                        class="w-full text-center tracking-[0.5em] text-2xl py-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-[3px] text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm"
                        placeholder="{{ $useRecoveryCode ? 'A1B2-C3D4' : '••••••' }}" autofocus
                        autocomplete="one-time-code" maxlength="{{ $useRecoveryCode ? 9 : 6 }}">
                    @error('code')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-2 font-medium text-center">
                            {{ $message }}
                    </p> @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary w-full justify-center !py-3.5 !text-base gap-2"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                    <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
                    <span wire:loading.remove>{{ __('Verify Identity') }}</span>
                    <span wire:loading>{{ __('Verifying...') }}</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center flex flex-col gap-3">
                @if($type === 'email')
                    <button wire:click="resendCode" type="button"
                        class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity"
                        wire:loading.attr="disabled">{{ __('Didn\'t receive the code? Resend') }}</button>
                @elseif($type === 'app' && !$useRecoveryCode)
                    <button wire:click="toggleRecovery" type="button"
                        class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity"
                        wire:loading.attr="disabled">{{ __('Use a recovery code') }}</button>
                    <button wire:click="fallbackToEmail" type="button"
                        class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity"
                        wire:loading.attr="disabled">{{ __('Can\'t access your app? Send code to email') }}</button>
                @elseif($useRecoveryCode)
                    <button wire:click="toggleRecovery" type="button"
                        class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity"
                        wire:loading.attr="disabled">{{ __('Use an authentication code') }}</button>
                @endif
                <a href="{{ route('login') }}"
                    class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity"
                    wire:navigate>{{ __('Return to Login') }}</a>
            </div>

        </div>
    </div>
</div>