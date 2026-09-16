<div class="space-y-6 animate-fade-in">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome,
                {{ auth()->user()->name }} 👋
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __('Here\'s an overview of your account and recent activities.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-400">{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        @foreach($stats as $index => $stat)
            <div class="stat-card animate-fade-in-up" style="animation-delay: {{ $index * 75 }}ms">
                <!-- Background decoration -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 {{ $stat['bg'] }} rounded-full -translate-y-8 translate-x-8 opacity-60">
                </div>

                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['value'] }}</p>
                    </div>
                    <div
                        class="{{ $stat['gradient'] }} w-12 h-12 rounded-[3px] flex items-center justify-center flex-shrink-0 shadow-lg">
                        <x-icon name="{{ $stat['icon'] }}" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Data Tables -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        <!-- Recent Estimations -->
        <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Recent Estimations') }}</h3>
                <a href="{{ route('client.estimations') }}" class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline" wire:navigate>{{ __('View All') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="px-5 py-3 font-semibold">Estimate #</th>
                            <th class="px-5 py-3 font-semibold">{{ __('Date') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ __('Status') }}</th>
                            <th class="px-5 py-3 font-semibold text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($recentEstimations as $estimate)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $estimate->estimate_number }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $estimate->date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="badge {{ $estimate->status === 'accepted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                        {{ ucfirst($estimate->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white text-right">
                                    {{ $estimate->currency_symbol }}{{ number_format($estimate->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">{{ __('No estimations found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Recent Invoices') }}</h3>
                <a href="{{ route('client.invoices') }}" class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline" wire:navigate>{{ __('View All') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="px-5 py-3 font-semibold">Invoice #</th>
                            <th class="px-5 py-3 font-semibold">{{ __('Date') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ __('Status') }}</th>
                            <th class="px-5 py-3 font-semibold text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($recentInvoices as $invoice)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $invoice->created_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="badge {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white text-right">
                                    {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">{{ __('No invoices found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

