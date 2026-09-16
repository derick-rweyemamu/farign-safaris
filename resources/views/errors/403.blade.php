@component('layouts.app')
    @slot('title', '403 Forbidden')

    <div class="flex items-center justify-center h-[80vh] relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0 opacity-50 dark:opacity-30 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] rounded-full bg-indigo-500/20 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[40%] h-[40%] rounded-full bg-red-500/20 blur-[100px]"></div>
        </div>

        <div class="max-w-sm w-full space-y-6 relative z-10 text-center bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl p-8 rounded-[3px] border border-white/40 dark:border-gray-800/60 shadow-2xl">
            <div class="flex justify-center">
                <div class="relative group cursor-not-allowed">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-900/10 rounded-full flex items-center justify-center shadow-inner relative overflow-hidden">
                        <div class="absolute inset-0 bg-red-500/10 animate-pulse"></div>
                        <x-icon name="shield-exclamation" class="w-10 h-10 text-red-600 dark:text-red-500 relative z-10 transition-transform duration-500 group-hover:scale-110" />
                    </div>
                    <div class="absolute -bottom-1 -right-1 bg-white dark:bg-gray-900 rounded-full p-1 shadow-lg">
                        <div class="w-7 h-7 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-md">
                            <x-icon name="lock-closed" class="w-3.5 h-3.5 text-white" />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h1 class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-indigo-600 dark:from-red-400 dark:to-indigo-400 mb-1 tracking-tight">403</h1>
                <h2 class="mt-1 text-xl font-bold text-gray-900 dark:text-white tracking-tight">{{ __('Access Denied') }}</h2>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">{{ __('Oops! It looks like you don\'t have the necessary permissions to view this page. If you believe this is a mistake, please contact your administrator.') }}</p>
            </div>

            <div class="pt-5 border-t border-gray-100/50 dark:border-gray-800/50 flex flex-col gap-2.5">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" 
                   class="inline-flex items-center justify-center w-full px-5 py-2.5 border border-transparent text-sm font-semibold rounded-[3px] text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow-md shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5 mr-2 stroke-2" />{{ __('Go Back') }}</a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center w-full px-5 py-2.5 border-2 border-gray-200 dark:border-gray-800 text-sm font-semibold rounded-[3px] text-gray-700 dark:text-gray-300 bg-transparent hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:focus:ring-offset-gray-900">
                    <x-icon name="home" class="w-3.5 h-3.5 mr-2" />{{ __('Dashboard') }}</a>
            </div>
        </div>
    </div>
@endcomponent
