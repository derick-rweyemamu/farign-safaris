{{-- Login View --}}
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
            @endif
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-6">{{ __('Welcome to') }}<br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-amber-200 to-indigo-300">
                    {{ $generalSetting?->company_name ?? config('app.name') }}
                </span>
            </h1>
            <p class="text-lg text-slate-200/90 leading-relaxed font-light">
                {{ __('A company, which is operating solely as a tour company. It deals with Mountain climbing, adventure Safaris, tour operations, and cultural tourism.') }}
            </p>
        </div>

        <!-- Glassmorphism overlay patterns -->
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30 pointer-events-none">
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 relative">
        <!-- Mobile Background Elements -->
        <div class="absolute inset-0 lg:hidden overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-md relative z-10">

            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <div
                    class="w-16 h-16 mx-auto mb-4 rounded-[3px] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <img src="{{ asset('storage/' . $generalSetting->favicon) }}" alt="icon"
                        class="max-h-full max-w-full object-contain">
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $generalSetting?->company_name ?? config('app.name') }}
                </h1>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Sign In') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">{{ __('Enter your email and password to access your
                    dashboard.') }}</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div
                    class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-[3px] text-emerald-600 dark:text-emerald-400 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login" class="space-y-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Email
                        Address') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-icon name="at-symbol" class="h-5 w-5 text-slate-400" />
                        </div>
                        <input type="email" id="email" wire:model="email"
                            class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-[3px] text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="name@example.com" autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ $message }}
                    </p> @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Password') }}</label>
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity"
                            wire:navigate>{{ __('Forgot
                            password?') }}</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-icon name="lock-closed" class="h-5 w-5 text-slate-400" />
                        </div>
                        <input type="password" id="password" wire:model="password"
                            class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-[3px] text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm"
                            placeholder="••••••••" autocomplete="current-password">
                    </div>
                    @error('password')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">
                            {{ $message }}
                    </p> @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center justify-center w-5 h-5">
                            <input type="checkbox" wire:model="remember"
                                class="peer appearance-none w-5 h-5 border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800 checked:bg-indigo-600 checked:border-indigo-600 focus:ring-2 focus:ring-indigo-500/30 transition-all cursor-pointer">
                            <x-icon name="check"
                                class="absolute w-3.5 h-3.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity"
                                stroke-width="3" />
                        </div>
                        <span
                            class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">{{ __('Remember
                            me for 30 days') }}</span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary w-full justify-center !py-3.5 !text-base gap-2"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                    <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
                    <span wire:loading.remove>{{ __('Sign In') }}</span>
                    <span wire:loading>{{ __('Authenticating...') }}</span>

                    <x-icon wire:loading.remove name="arrow-right" class="w-5 h-5" />
                </button>
            </form>

            <!-- Register link -->
            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Don\'t have an account?') }}<a
                        href="{{ route('register.client') }}"
                        class="text-[var(--color-primary-500)] hover:opacity-80 font-semibold transition-opacity"
                        wire:navigate>{{ __('Create one now') }}</a>
                </p>
            </div>

            <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-8">
                &copy; {{ date('Y') }} {{ $generalSetting?->company_name ?? config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</div>