<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-slate-50 dark:bg-slate-900">
    <!-- Left Side: Branding / Visuals -->
    <div class="hidden lg:flex relative overflow-hidden bg-slate-900 justify-center items-center p-12">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-amber-500/20 rounded-full mix-blend-screen filter blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-[40%] left-[60%] w-72 h-72 bg-purple-600/20 rounded-full mix-blend-screen filter blur-[80px] animate-pulse" style="animation-delay: 4s;"></div>

        <div class="relative z-10 text-center max-w-lg">
            <div class="w-24 h-24 mx-auto mb-8 rounded-[3px] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl shadow-indigo-500/40 transform -rotate-6 hover:rotate-0 transition-transform duration-500">
                <img src="{{ Storage::url(app(\App\Settings\GeneralSetting::class)->favicon) }}" alt="icon">
            </div>
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-6">{{ __('Forgot Password') }}</h1>
            <p class="text-lg text-indigo-100/80 leading-relaxed font-light">{{ __('Don\'t worry, we\'ll help you get back to managing your adventures.') }}</p>
        </div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30 pointer-events-none"></div>
    </div>

    <!-- Right Side: Form -->
    <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 relative">
        <div class="w-full max-w-md relative z-10">
            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Reset Password') }}</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">{{ __('Enter your email address and we will send you a password reset link.') }}</p>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-[3px] text-emerald-600 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="sendResetLink" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Email Address') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-icon name="at-symbol" class="h-5 w-5 text-slate-400" />
                        </div>
                        <input type="email" id="email" wire:model="email" class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-[3px] text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm" placeholder="name@example.com" autofocus required>
                    </div>
                    @error('email') <p class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary w-full justify-center !py-3.5 !text-base gap-2" wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                    <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
                    <span wire:loading.remove>{{ __('Email Password Reset Link') }}</span>
                    <span wire:loading>{{ __('Sending...') }}</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-[var(--color-primary-500)] hover:opacity-80 transition-opacity" wire:navigate>{{ __('Back to login') }}</a>
            </div>
            
            <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-8">
                &copy; {{ date('Y') }} Jairo's Adventure. All rights reserved.
            </p>
        </div>
    </div>
</div>
