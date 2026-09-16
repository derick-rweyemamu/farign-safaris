<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data
    :class="{ 'dark': $store.theme.dark, 'sidebar-collapsed': $store.sidebar.collapsed }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? app(\App\Settings\GeneralSetting::class)->company_name ?? config('app.name') }} | {{ __('Client Portal') }}
    </title>
    <meta name="description"
        content="{{ $description ?? __('Client portal for ') . (app(\App\Settings\GeneralSetting::class)->company_name ?? config('app.name')) }}">
    @if(!empty(app(\App\Settings\GeneralSetting::class)->favicon))<link rel="icon" href="{{ Storage::url(app(\App\Settings\GeneralSetting::class)->favicon) }}?v={{ file_exists(storage_path('app/public/' . app(\App\Settings\GeneralSetting::class)->favicon)) ? filemtime(storage_path('app/public/' . app(\App\Settings\GeneralSetting::class)->favicon)) : '1' }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Theme persistence script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>

    <!-- Theme Assets -->
    @vite(['resources/themes/admin/assets/css/app.css', 'resources/themes/admin/assets/js/app.js'])
    @livewireStyles
</head>

<body class="text-gray-900 dark:text-gray-100 antialiased min-h-screen font-poppins">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <x-client.sidebar />

        <!-- Mobile sidebar overlay -->
        <div class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden" x-show="$store.sidebar.mobileOpen"
            x-cloak @click="$store.sidebar.close()" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" style="display:none;">
        </div>

        <!-- Main content area -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            <!-- Topbar (Reusing admin topbar as it's mostly generic) -->
            <x-admin.topbar :title="$title ?? __('Client Portal')" />

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-5">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Global Notifications -->
    <x-admin.notifications />

    @include('partials.livewire-waf-bypass')

    @livewireScripts
    @stack('scripts')
</body>

</html>
