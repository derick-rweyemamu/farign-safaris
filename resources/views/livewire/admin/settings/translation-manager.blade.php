<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('UI Translations Manager') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ __('Manage static website words and phrases across all languages. Changes update immediately and are cached.') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="importFromJson" wire:loading.attr="disabled"
                class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold shadow-sm">
                <x-icon wire:loading.remove wire:target="importFromJson" name="arrow-down-tray" class="w-4 h-4" />
                <x-icon wire:loading wire:target="importFromJson" name="arrow-path" class="w-4 h-4 animate-spin" />
                <span wire:loading.remove wire:target="importFromJson">{{ __('Import JSON to Database') }}</span>
                <span wire:loading wire:target="importFromJson">{{ __('Importing...') }}</span>
            </button>
        </div>
    </div>

    <!-- Import Progress Bar (Active / Processing) -->
    <div wire:loading wire:target="importFromJson"
        class="w-full bg-card rounded-[3px] p-4 border border-[#EC9824]/30 shadow-sm transition-all animate-pulse">
        <div class="flex items-center justify-between text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
            <span class="flex items-center gap-2">
                <x-icon name="arrow-path" class="w-4 h-4 animate-spin text-[#EC9824]" />
                {{ __('Importing language JSON files into database...') }}
            </span>
            <span class="text-[#EC9824] uppercase text-[11px] font-extrabold">{{ __('Processing...') }}</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-[#EC9824] to-[#F1B153] h-2.5 rounded-full animate-pulse w-full"></div>
        </div>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">
            {{ __('Syncing lang. All json translation keys across all locales without disrupting active site visitors.') }}
        </p>
    </div>

    <!-- Import Progress Bar (Completed) -->
    @if($importSummary)
        <div
            class="w-full bg-card rounded-[3px] p-4 border border-emerald-500/30 dark:border-emerald-500/40 shadow-sm transition-all">
            <div class="flex items-center justify-between text-xs font-bold text-gray-900 dark:text-white mb-2">
                <span class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    {{ __('Import 100% Completed') }}
                </span>
                <div class="flex items-center gap-3">
                    <span
                        class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold uppercase">
                        {{ __('100% DONE') }}
                    </span>
                    <button type="button" wire:click="$set('importSummary', null)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <x-icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2.5 rounded-full" style="width: 100%"></div>
            </div>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 mt-2 font-medium">{{ $importSummary }}</p>
        </div>
    @endif

    <!-- Language Selector Tabs -->
    <div
        class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800/50 p-1 rounded-[3px] w-fit overflow-x-auto max-w-full flex-wrap">
        @foreach($availableLanguages as $code => $label)
            <button type="button" wire:key="lang-tab-{{ $code }}" wire:click="$set('selectedLang', '{{ $code }}')"
                class="flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-medium rounded-[3px] transition-all whitespace-nowrap {{ $selectedLang === $code ? 'bg-white dark:bg-gray-700 text-[#EC9824] dark:text-[#EC9824] shadow-sm font-bold' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <span>{{ $label }}</span>
                <span
                    class="py-0.5 px-2 rounded-full text-[10px] font-bold uppercase {{ $selectedLang === $code ? 'bg-[#EC9824]/15 text-[#EC9824]' : 'bg-gray-200/60 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                    {{ strtoupper($code) }}
                </span>
            </button>
        @endforeach
    </div>

    <!-- Filter & Search Bar -->
    <div
        class="bg-card rounded-[3px] p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-96">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search phrase or translation...') }}" class="form-input text-xs py-2 !pl-9 w-full">
            <x-icon name="magnifying-glass"
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
        </div>
        <div class="text-xs text-gray-500 font-medium">{{ __('Showing') }} <span
                class="font-bold text-gray-900 dark:text-white">{{ $translations->total() }}</span>
            {{ __('strings for') }} <span class="uppercase font-bold text-[#EC9824]">{{ $selectedLang }}</span>
        </div>
    </div>

    <!-- Translations Data Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-5/12">
                            {{ __('English Reference (Key)') }}
                        </th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-6/12">
                            {{ __('Translated Phrase') }} ({{ strtoupper($selectedLang) }})
                        </th>
                        <th
                            class="px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right w-1/12">
                            {{ __('Action') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800"
                    wire:loading.class="opacity-50 pointer-events-none">
                    @forelse($translations as $item)
                        <tr wire:key="row-{{ $selectedLang }}-{{ $item->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors group"
                            x-data="{ value: '{{ addslashes($item->lang_value ?? '') }}', isDirty: false }">
                            <td class="px-5 py-3 text-xs font-medium text-gray-800 dark:text-gray-200 align-top">
                                <div
                                    class="break-words font-mono bg-gray-100 dark:bg-gray-800 px-2.5 py-1.5 rounded-[3px] border border-gray-200 dark:border-gray-700 text-[11px]">
                                    {{ $item->lang_key }}
                                </div>
                            </td>
                            <td class="px-5 py-3 align-top">
                                <textarea x-model="value" @input="isDirty = true" rows="2"
                                    placeholder="Enter translation in {{ strtoupper($selectedLang) }}..."
                                    class="w-full form-input text-xs py-1.5 px-2.5 rounded-[3px] focus:ring-[#EC9824] focus:border-[#EC9824]"></textarea>
                            </td>
                            <td class="px-5 py-3 text-right align-top">
                                <button type="button"
                                    @click="$wire.saveTranslation({{ $item->id }}, value); isDirty = false"
                                    :class="isDirty ? 'bg-[#EC9824] hover:bg-[#A68A3D] text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[3px] text-xs font-semibold transition-colors">
                                    <x-icon name="check" class="w-3.5 h-3.5" />
                                    <span>{{ __('Save') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-gray-400 text-xs">
                                {{ __('No translations found for your search query.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($translations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $translations->links() }}
            </div>
        @endif
    </div>

    <!-- Add Modal removed as translations are imported from JSON files -->
</div>