@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}
$scrollIntoViewJsSnippet = '';
@endphp

<div>
    @if ($paginator->hasPages())
        @php
            $currentPage  = $paginator->currentPage();
            $lastPage     = $paginator->lastPage();
            $window       = 2; // pages on each side of current
            $pageNumbers  = [];

            // Always include first and last page; build a window around current
            for ($i = 1; $i <= $lastPage; $i++) {
                if (
                    $i === 1 ||
                    $i === $lastPage ||
                    ($i >= $currentPage - $window && $i <= $currentPage + $window)
                ) {
                    $pageNumbers[] = $i;
                }
            }

            // Deduplicate and sort
            $pageNumbers = array_values(array_unique($pageNumbers));
        @endphp

        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between sm:justify-center w-full">
            {{-- Mobile simple prev/next --}}
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-[3px] dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-[3px] hover:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            {!! __('pagination.previous') !!}
                        </a>
                    @endif
                </span>
                <span>
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-[3px] hover:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            {!! __('pagination.next') !!}
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-[3px] dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            {{-- Desktop full pagination --}}
            <div class="hidden sm:flex sm:items-center sm:justify-center gap-2">
                <span class="flex items-center gap-1.5 rtl:flex-row-reverse">

                    {{-- Previous Button --}}
                    @if ($paginator->onFirstPage())
                        <span class="w-9 h-9 flex items-center justify-center text-gray-300 bg-white/50 border border-gray-200/60 rounded-[3px] cursor-not-allowed dark:bg-gray-800/40 dark:border-gray-800 dark:text-gray-600" aria-disabled="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center text-gray-700 bg-white border border-gray-200 rounded-[3px] hover:bg-brand-primary hover:text-white hover:border-brand-primary transition-all duration-200 shadow-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-brand-primary dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary" aria-label="Previous">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    @endif

                    {{-- Page Numbers with smart truncation --}}
                    @php $prev = null; @endphp
                    @foreach ($pageNumbers as $page)
                        @if ($prev !== null && $page - $prev > 1)
                            {{-- Gap — show ellipsis --}}
                            <span class="w-9 h-9 flex items-center justify-center text-gray-400 dark:text-gray-600 font-bold tracking-widest select-none">…</span>
                        @endif

                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                            @if ($page === $currentPage)
                                <span class="min-w-[36px] h-9 px-2 flex items-center justify-center text-sm font-bold text-white bg-brand-primary border border-brand-primary rounded-[3px] shadow-sm select-none">{{ $page }}</span>
                            @else
                                <a href="{{ $paginator->url($page) }}" class="min-w-[36px] h-9 px-2 flex items-center justify-center text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-[3px] hover:bg-brand-primary hover:text-white hover:border-brand-primary transition-all duration-200 shadow-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-brand-primary dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary">
                                    {{ $page }}
                                </a>
                            @endif
                        </span>

                        @php $prev = $page; @endphp
                    @endforeach

                    {{-- Next Button --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center text-gray-700 bg-white border border-gray-200 rounded-[3px] hover:bg-brand-primary hover:text-white hover:border-brand-primary transition-all duration-200 shadow-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-brand-primary dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary" aria-label="Next">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4-4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center text-gray-300 bg-white/50 border border-gray-200/60 rounded-[3px] cursor-not-allowed dark:bg-gray-800/40 dark:border-gray-800 dark:text-gray-600" aria-disabled="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4-4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </span>
                    @endif

                </span>

                {{-- Divider --}}
                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-2"></div>

                {{-- Go To Page --}}
                <div class="flex items-center gap-1.5" x-data="{ targetPage: {{ $paginator->currentPage() }} }">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mr-1">{{ __('Go to') }}</span>
                    <input type="number" min="1" max="{{ $paginator->lastPage() }}"
                        x-model="targetPage"
                        x-on:keydown.enter="if(targetPage >= 1 && targetPage <= {{ $paginator->lastPage() }}) { let url = new URL(window.location.href); url.searchParams.set('{{ $paginator->getPageName() }}', targetPage); window.location.href = url.href; }"
                        class="w-12 h-9 text-xs font-bold text-center text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-[3px] shadow-xs focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        placeholder="#" />
                    <button type="button"
                        x-on:click="if(targetPage >= 1 && targetPage <= {{ $paginator->lastPage() }}) { let url = new URL(window.location.href); url.searchParams.set('{{ $paginator->getPageName() }}', targetPage); window.location.href = url.href; }"
                        class="w-9 h-9 flex items-center justify-center text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-brand-primary hover:text-white hover:border-brand-primary rounded-[3px] shadow-xs transition-colors"
                        title="{{ __('Go to page') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>
        </nav>
    @endif
</div>
