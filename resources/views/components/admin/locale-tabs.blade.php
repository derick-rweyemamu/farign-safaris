@props([
    'locales',
    'activeLocaleTab',
    'fields' => ['name', 'description'],  // which fields to show per tab
    'hasDescription' => true,
    'namePlaceholder' => 'e.g. Budget Safari',
    'descPlaceholder' => 'Description...',
])

<div>
    {{-- Tab buttons --}}
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-3 gap-1">
        @foreach($locales as $locale)
            <button type="button"
                wire:click="$set('activeLocaleTab', '{{ $locale }}')"
                class="px-3 py-1.5 text-xs font-semibold uppercase rounded-t transition-colors
                    {{ $activeLocaleTab === $locale
                        ? 'bg-indigo-600 text-white'
                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                {{ strtoupper($locale) }}
                @if($locale === 'en')<span class="ml-0.5 text-[9px] opacity-80">*</span>@endif
            </button>
        @endforeach
    </div>

    {{-- Tab content --}}
    @foreach($locales as $locale)
        <div @class(['hidden' => $activeLocaleTab !== $locale]) wire:key="locale-tab-{{ $locale }}">
            <div class="space-y-3">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Name') }} ({{ strtoupper($locale) }})
                        @if($locale === 'en')<span class="text-red-500 ml-1">*</span>@endif
                    </label>
                    <input type="text"
                        wire:model.live="names.{{ $locale }}"
                        class="form-input"
                        placeholder="{{ $locale === 'en' ? $namePlaceholder : 'Translation in ' . strtoupper($locale) }}">
                    @if($locale === 'en')
                        @error('names.en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                {{-- Description (optional) --}}
                @if($hasDescription)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Description') }} ({{ strtoupper($locale) }})
                        </label>
                        <textarea wire:model="descriptions.{{ $locale }}"
                            class="form-input min-h-[80px]"
                            placeholder="{{ $locale === 'en' ? $descPlaceholder : 'Description in ' . strtoupper($locale) }}"></textarea>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
