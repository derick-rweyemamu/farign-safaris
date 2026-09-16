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
            @if($generalSetting?->favicon)<div
                    class="w-24 h-24 mx-auto mb-8 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-2xl p-3 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                    <img src="{{ asset('storage/' . $generalSetting->favicon) }}" alt="icon"
                        class="max-h-full max-w-full object-contain">
                </div>
            @else
                <div
                    class="w-24 h-24 mx-auto mb-8 rounded-[3px] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl shadow-indigo-500/40">
                    <x-icon name="shield-check" class="w-12 h-12 text-white" />
                </div>
            @endif
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4">{{ __('Secure Your Account') }}</h1>
            <p class="text-lg text-indigo-100/80 font-light">{{ __('Two-Factor Authentication is required to access the dashboard.') }}</p>
        </div>
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30 pointer-events-none">
        </div>
    </div>

    <!-- Right Side: Setup Form -->
    <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 relative">
        <div class="w-full max-w-md relative z-10">

            <div class="text-center lg:text-left mb-8">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">{{ __('Two-Factor Setup') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('Please follow the steps to configure your security.') }}</p>
            </div>

            @if($step === 1)
                <!-- Step 1: Email Confirmation -->
                <form wire:submit="confirmEmail" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-slate-700 dark:text-slate-300">{{ __('Confirm Email Address') }}</label>
                        <div class="mt-2 relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-icon name="envelope" class="h-5 w-5 text-slate-400" />
                            </div>
                            <input id="email" wire:model="email" type="email" required
                                class="block w-full rounded-[3px] py-3 pl-10 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit"
                            class="btn-primary w-full justify-center !py-3.5 !text-base gap-2"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmEmail">{{ __('Continue') }}</span>
                            <span wire:loading wire:target="confirmEmail">{{ __('Verifying...') }}</span>
                        </button>
                    </div>
                </form>
            @elseif($step === 2)
                <!-- Step 2: Choose Method -->
                <div class="space-y-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ __('Choose your preferred two-factor
                        authentication method:') }}</p>

                    <button wire:click="selectType('app')"
                        class="w-full flex items-center gap-4 p-4 rounded-[3px] bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-all text-left group shadow-sm">
                        <div
                            class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-[3px] flex items-center justify-center shrink-0">
                            <x-icon name="device-phone-mobile" class="w-5 h-5" />
                        </div>
                        <div>
                            <h4
                                class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ __('Authenticator App') }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Use an app like Google Authenticator or
                                Authy to generate verification codes.') }}</p>
                        </div>
                    </button>

                    <button wire:click="selectType('email')"
                        class="w-full flex items-center gap-4 p-4 rounded-[3px] bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-all text-left group shadow-sm">
                        <div
                            class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-[3px] flex items-center justify-center shrink-0">
                            <x-icon name="envelope" class="w-5 h-5" />
                        </div>
                        <div>
                            <h4
                                class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ __('Email OTP') }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('Receive a one-time password via your
                                registered email address.') }}</p>
                        </div>
                    </button>
                </div>
            @elseif($step === 3)
                <!-- Step 3: Setup & Verify -->
                <div class="space-y-6">
                    <button wire:click="backToTypeSelection"
                        class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity flex items-center gap-1 mb-4">
                        <x-icon name="arrow-left" class="w-4 h-4" />{{ __('Back to methods') }}</button>

                    @if($setupType === 'app')
                        <div class="flex flex-col items-center text-center space-y-4">
                            <div class="bg-white p-3 rounded-[3px] shadow-sm border border-slate-200 inline-block">
                                {!! $twoFactorQrCodeUrl !!}
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Scan this QR code using your authenticator app, then enter the generated 6-digit code below.') }}</p>
                            <div class="bg-slate-100 dark:bg-slate-800 rounded px-3 py-1 text-xs font-mono text-slate-800 dark:text-slate-200">
                                {{ $twoFactorSetupSecret }}
                            </div>
                            
                            @if(!empty($recoveryCodes))
                            <div class="w-full mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-[3px] text-left">
                                <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-500 mb-2">{{ __('Recovery Codes') }}</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-600 mb-3">{{ __('Store these recovery codes securely. You can use them to recover access to your account if your authenticator device is lost.') }}</p>
                                <div class="grid grid-cols-2 gap-2 text-xs font-mono">
                                    @foreach($recoveryCodes as $code)
                                        <div class="bg-white dark:bg-slate-800 p-2 rounded border border-amber-100 dark:border-amber-900/50 text-center tracking-wider text-slate-900 dark:text-slate-200">
                                            {{ $code }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    @elseif($setupType === 'email')
                        <div class="text-center space-y-2">
                            <div
                                class="mx-auto w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-full flex items-center justify-center mb-4">
                                <x-icon name="envelope" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('We\'ve sent a 6-digit verification code to your email.') }}</p>
                        </div>
                    @endif

                    <form wire:submit="verify" class="mt-6 space-y-6">
                        <div>
                            <label for="code" class="sr-only">{{ __('Verification Code') }}</label>
                            <input id="code" wire:model="twoFactorCode" type="text" inputmode="numeric" required
                                class="w-full text-center tracking-[0.5em] text-2xl py-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-[3px] text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm font-mono"
                                placeholder="••••••" maxlength="6" autofocus>
                            @error('twoFactorCode')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400 text-center font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="btn-primary w-full justify-center !py-3.5 !text-base gap-2"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="verify">{{ __('Verify & Enable 2FA') }}</span>
                            <span wire:loading wire:target="verify">{{ __('Verifying...') }}</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
