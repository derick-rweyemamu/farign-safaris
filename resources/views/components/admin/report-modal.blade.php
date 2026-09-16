@props(['title', 'show'])

@if($show)
    <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showReportModal', false)">
        <div class="modal-box max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl animate-fade-in-up">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('Configure and generate your data report') }}</p>
                </div>
                <button wire:click="$set('showReportModal', false)"
                    class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Modal Body -->
            <form wire:submit="generateReport" class="flex-1 flex flex-col overflow-hidden">
                <div class="p-5 space-y-5 overflow-y-auto flex-1">
                    <!-- Report Format -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Export Format') }}</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="relative flex items-center justify-center p-3 border rounded-[3px] cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                :class="$wire.reportFormat === 'csv' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700'">
                                <input type="radio" wire:model="reportFormat" value="csv" class="sr-only">
                                <div class="flex flex-col items-center gap-1.5">
                                    <x-icon name="document-text" class="w-6 h-6 text-emerald-600" />
                                    <span class="text-xs font-semibold">{{ __('CSV / Excel') }}</span>
                                </div>
                                <div x-show="$wire.reportFormat === 'csv'" class="absolute top-1.5 right-1.5">
                                    <x-icon name="check" class="w-3.5 h-3.5 text-indigo-600" />
                                </div>
                            </label>

                            <label
                                class="relative flex items-center justify-center p-3 border rounded-[3px] cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                :class="$wire.reportFormat === 'pdf' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700'">
                                <input type="radio" wire:model="reportFormat" value="pdf" class="sr-only">
                                <div class="flex flex-col items-center gap-1.5">
                                    <x-icon name="document-chart-bar" class="w-6 h-6 text-red-600" />
                                    <span class="text-xs font-semibold">{{ __('PDF Report') }}</span>
                                </div>
                                <div x-show="$wire.reportFormat === 'pdf'" class="absolute top-1.5 right-1.5">
                                    <x-icon name="check" class="w-3.5 h-3.5 text-indigo-600" />
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Time Period') }}</label>
                        <select wire:model.live="reportDateRange" class="form-input">
                            <option value="today">{{ __('Today') }}</option>
                            <option value="yesterday">{{ __('Yesterday') }}</option>
                            <option value="last_7_days">{{ __('Last 7 Days') }}</option>
                            <option value="last_30_days">{{ __('Last 30 Days') }}</option>
                            <option value="this_month">{{ __('This Month') }}</option>
                            <option value="last_month">{{ __('Last Month') }}</option>
                            <option value="this_year">{{ __('This Year') }}</option>
                            <option value="custom">{{ __('Custom Range') }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 animate-fade-in" x-show="$wire.reportDateRange === 'custom'" x-cloak>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('From') }}</label>
                            <input type="date" wire:model="reportStartDate" class="form-input text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('To') }}</label>
                            <input type="date" wire:model="reportEndDate" class="form-input text-sm">
                        </div>
                    </div>

                    <!-- PDF Options -->
                    <div x-show="$wire.reportFormat === 'pdf'" class="animate-fade-in space-y-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('PDF Orientation') }}</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2 border rounded cursor-pointer transition-colors"
                                :class="$wire.pdfOrientation === 'portrait' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700'">
                                <input type="radio" wire:model="pdfOrientation" value="portrait" class="sr-only">
                                <x-icon name="document" class="w-4 h-4" />
                                <span class="text-xs font-medium">{{ __('Portrait') }}</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border rounded cursor-pointer transition-colors"
                                :class="$wire.pdfOrientation === 'landscape' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700'">
                                <input type="radio" wire:model="pdfOrientation" value="landscape" class="sr-only">
                                <x-icon name="document" class="w-4 h-4 rotate-90" />
                                <span class="text-xs font-medium">{{ __('Landscape') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Column Selection -->
                    @if(isset($this->availableColumns) && count($this->availableColumns) > 0)
                        <div class="pt-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <x-icon name="table-cells" class="w-4 h-4 text-gray-400" />{{ __('Select Columns') }}</span>
                                <span class="text-[10px] text-gray-400 font-normal">{{ __('Choose which data to include') }}</span>
                            </label>
                            <div
                                class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-[3px] border border-gray-100 dark:border-gray-700 max-h-40 overflow-y-auto">
                                @foreach($this->availableColumns as $key => $label)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" wire:model="selectedColumns" value="{{ $key }}"
                                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                        <span
                                            class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Additional Filters Slot -->
                    @if(trim($slot) !== '')
                        <div class="pt-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <x-icon name="funnel" class="w-4 h-4 text-gray-400" />{{ __('Data Filters') }}</label>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-[3px] border border-gray-100 dark:border-gray-700">
                                {{ $slot }}
                            </div>
                        </div>
                    @endif

                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/20 rounded-[3px] p-4 border border-indigo-100 dark:border-indigo-900/30">
                        <div class="flex gap-3">
                            <x-icon name="clipboard-list" class="w-5 h-5 text-indigo-600 flex-shrink-0" />
                            <p class="text-xs text-indigo-800 dark:text-indigo-300 leading-relaxed">{{ __('The generated report will include all data matching your current filters and the selected
                                date range.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="flex-shrink-0 flex items-center justify-end gap-3 p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" wire:click="$set('showReportModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                        <x-icon name="document-arrow-down" class="w-4 h-4" wire:loading.remove />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                        <span wire:loading.remove>{{ __('Download Report') }}</span>
                        <span wire:loading>{{ __('Preparing...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
