@extends('layouts.frontend')

@section('title', '404 Not Found')

@section('content')
    <div class="flex items-center justify-center min-h-[70vh] relative overflow-hidden py-16">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0 opacity-50 dark:opacity-30 pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] rounded-full bg-[#EC9824]/10 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[40%] h-[40%] rounded-full bg-amber-600/10 blur-[100px]"></div>
        </div>

        <div class="max-w-md w-full space-y-6 relative z-10 text-center mx-4">
            <div class="flex justify-center">
                <div class="relative group cursor-not-allowed">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-amber-100 to-amber-50 dark:from-[#EC9824]/20 dark:to-amber-900/10 rounded-full flex items-center justify-center shadow-inner relative overflow-hidden">
                        <div class="absolute inset-0 bg-[#EC9824]/10 animate-pulse"></div>
                        <x-icon name="magnifying-glass"
                            class="w-10 h-10 text-[#EC9824] relative z-10 transition-transform duration-500 group-hover:scale-110" />
                    </div>
                    <div class="absolute -bottom-1 -right-1 bg-white dark:bg-gray-900 rounded-full p-1 shadow-lg">
                        <div
                            class="w-7 h-7 bg-gradient-to-r from-amber-500 to-[#EC9824] rounded-full flex items-center justify-center shadow-md">
                            <x-icon name="question-mark-circle" class="w-3.5 h-3.5 text-white" />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h1
                    class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#EC9824] to-amber-700 dark:from-[#EC9824] dark:to-amber-500 mb-1 tracking-tight">
                    404</h1>
                <h2 class="mt-1 text-xl font-bold text-gray-900 dark:text-white tracking-tight">{{ __('Page Not Found') }}</h2>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                    Oops! The page you are looking for might have been removed, had its name changed, or is temporarily
                    unavailable. Use the menu at the top to locate your page.
                </p>
            </div>

            <div class="pt-5">
                <a href="{{ route('home') }}" style="background-color: #EC9824;"
                    class="inline-flex items-center justify-center w-full px-5 py-2.5 border border-transparent text-sm font-semibold rounded-[3px] text-white hover:bg-[#b0913b] shadow-md shadow-[#EC9824]/20 hover:shadow-[#EC9824]/40 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#EC9824] dark:focus:ring-offset-gray-900">
                    <x-icon name="home" class="w-3.5 h-3.5 mr-2" />{{ __('Back to Home Page') }}</a>
            </div>
        </div>
    </div>
@endsection