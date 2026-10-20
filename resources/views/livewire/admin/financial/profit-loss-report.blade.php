<div class="space-y-6 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Profit & Loss Report') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Overall financial performance summary') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Currency Switcher -->
            <div class="flex items-center gap-2 bg-card px-3 py-1.5 rounded-[3px] border border-gray-200 dark:border-gray-700 shadow-sm">
                <x-icon name="currency-dollar" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Currency') }}:</span>
                <select wire:model.live="selectedCurrency" class="text-xs font-bold text-gray-900 dark:text-white bg-transparent border-0 focus:ring-0 p-0 pr-4 cursor-pointer">
                    @foreach($currencies as $curr)
                        <option value="{{ $curr->code }}" class="text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800">
                            {{ $curr->code }} ({{ $curr->symbol }})
                        </option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('cms.pdf.profit-loss', ['start_date' => $startDate, 'end_date' => $endDate, 'currency' => $selectedCurrency]) }}"
                target="_blank" class="btn-secondary">
                <x-icon name="document-arrow-down" class="w-4 h-4" />{{ __('Download Report') }}
            </a>
            <button wire:click="syncCurrentMonth" class="btn-secondary">
                <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Update Snapshot') }}</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-4 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">{{ __('Start Date') }}</label>
                <input type="date" wire:model.live="startDate" class="form-input text-sm w-auto">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">{{ __('End Date') }}</label>
                <input type="date" wire:model.live="endDate" class="form-input text-sm w-auto">
            </div>
            <div>
                <button wire:click="calculateReport" class="btn-primary" wire:loading.attr="disabled">
                    <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="calculateReport" />
                    <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="calculateReport" />
                    <span wire:loading.remove wire:target="calculateReport">{{ __('Generate Report') }}</span>
                    <span wire:loading wire:target="calculateReport">{{ __('Generating...') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue -->
        <div
            class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden relative group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-110 transition-transform">
            </div>
            <div class="flex items-center gap-4 mb-3">
                <div
                    class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-[3px] flex items-center justify-center">
                    <x-icon name="banknotes" class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Total Revenue') }}
                </h3>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white">
                {{ $currencySymbol }} {{ number_format($reportData['revenue'], 2) }}
            </p>
            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-2 flex items-center gap-1 font-medium">
                {{ __('Invoiced Amount') }} ({{ $selectedCurrency }})
            </p>
        </div>

        <!-- Expenses -->
        <div
            class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden relative group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full group-hover:scale-110 transition-transform">
            </div>
            <div class="flex items-center gap-4 mb-3">
                <div
                    class="w-10 h-10 bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-[3px] flex items-center justify-center">
                    <x-icon name="credit-card" class="w-5 h-5" />
                </div>
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Total Expenses') }}
                </h3>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white">
                {{ $currencySymbol }} {{ number_format($reportData['total_expenses'], 2) }}
            </p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center gap-1 font-medium">
                {{ __('Outflow') }} ({{ $selectedCurrency }})
            </p>
        </div>

        <!-- Net Profit -->
        <div
            class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden relative group lg:col-span-2 {{ $reportData['net_profit'] >= 0 ? 'bg-emerald-50/30 dark:bg-emerald-500/5' : 'bg-red-50/30 dark:bg-red-500/5' }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-4 mb-3">
                        <div
                            class="w-10 h-10 {{ $reportData['net_profit'] >= 0 ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }} rounded-[3px] flex items-center justify-center shadow-lg shadow-current/20">
                            <x-icon
                                name="{{ $reportData['net_profit'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="w-5 h-5" />
                        </div>
                        <h3
                            class="text-sm font-bold {{ $reportData['net_profit'] >= 0 ? 'text-emerald-800 dark:text-emerald-400' : 'text-red-800 dark:text-red-400' }} uppercase tracking-wider">
                            {{ __('Net Profit / Loss') }}
                        </h3>
                    </div>
                    <p
                        class="text-4xl font-black {{ $reportData['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $currencySymbol }} {{ number_format($reportData['net_profit'], 2) }}
                    </p>
                </div>
                <div class="hidden sm:block">
                    @php 
                        $margin = $reportData['revenue'] > 0 ? ($reportData['net_profit'] / $reportData['revenue']) * 100 : 0;
                     @endphp
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">
                            {{ __('Profit Margin') }}
                        </p>
                        <p class="text-2xl font-black text-gray-700 dark:text-gray-300">{{ number_format($margin, 1) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Expense Breakdown -->
        <div class="lg:col-span-1 bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="chart-pie" class="w-5 h-5 text-indigo-500" />{{ __('Expense Breakdown') }}
                </h3>
            </div>
            <div class="p-6 space-y-6">
                @php $types = [
                        ['label' => 'General Expenses', 'amount' => $reportData['general_expenses'], 'color' => 'bg-blue-500', 'type' => 'general_expenses'],
                        ['label' => 'Invoice Expenses', 'amount' => $reportData['invoice_expenses'], 'color' => 'bg-purple-500', 'type' => 'invoice_expenses'],
                    ];
                    $total = array_sum(array_column($types, 'amount')) ?: 1;
                @endphp

                @foreach($types as $type)
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs font-bold uppercase tracking-wider">
                            <span class="text-gray-500 flex items-center gap-2">
                                {{ $type['label'] }}
                                <button wire:click="openExpenseReportModal('{{ $type['type'] }}')"
                                    class="text-gray-400 hover:text-indigo-500"
                                    title="{{ __('Download :type', ['type' => $type['label']]) }}">
                                    <x-icon name="document-arrow-down" class="w-4 h-4" />
                                </button>
                            </span>
                            <span class="text-gray-900 dark:text-white">{{ $currencySymbol }} {{ number_format($type['amount'], 2) }}</span>
                        </div>
                        <div class="h-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            <div class="{{ $type['color'] }} h-full rounded-full"
                                style="width: {{ ($type['amount'] / $total) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="lg:col-span-2 bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="chart-bar" class="w-5 h-5 text-indigo-500" />{{ __('Monthly Historical Data') }} ({{ $selectedCurrency }})
                </h3>
                <button wire:click="openExpenseReportModal('monthly_profit_loss')"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-[3px] bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors border border-indigo-200 dark:border-indigo-500/30">
                    <x-icon name="document-arrow-down" class="w-4 h-4" />
                    {{ __('Download Report') }}
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Month') }}</th>
                            <th>{{ __('Revenue') }}</th>
                            <th>{{ __('Expenses') }}</th>
                            <th class="text-end">
                                <div class="flex justify-end">{{ __('Net Profit') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlySnapshots as $snap)
                            <tr>
                                <td class="font-bold text-gray-700 dark:text-gray-300">
                                    {{ date("F", mktime(0, 0, 0, $snap->month, 1)) }} {{ $snap->year }}
                                </td>
                                <td class="text-emerald-600 font-medium">{{ $currencySymbol }} {{ number_format($snap->total_revenue * $exchangeRate, 2) }}</td>
                                <td class="text-red-600 font-medium">
                                    {{ $currencySymbol }} {{ number_format(($snap->total_general_expenses + $snap->total_invoice_expenses) * $exchangeRate, 2) }}
                                </td>
                                <td class="text-right font-black {{ $snap->net_profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $currencySymbol }} {{ number_format($snap->net_profit * $exchangeRate, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">
                                    {{ __('No historical snapshots found for this year.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Detailed Expense Report') }}" :show="$showReportModal" />
</div>