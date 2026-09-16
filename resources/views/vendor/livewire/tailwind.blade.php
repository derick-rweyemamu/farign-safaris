@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}
$scrollIntoViewJsSnippet = '';
@endphp

<div>
    @if ($paginator->hasPages())
        @php
            $currentPage = $paginator->currentPage();
            $lastPage    = $paginator->lastPage();
            $window      = 2; // pages shown on each side of current

            $pageNumbers = [];
            for ($i = 1; $i <= $lastPage; $i++) {
                if (
                    $i === 1 ||
                    $i === $lastPage ||
                    ($i >= $currentPage - $window && $i <= $currentPage + $window)
                ) {
                    $pageNumbers[] = $i;
                }
            }
            $pageNumbers = array_values(array_unique($pageNumbers));
        @endphp

        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            {{-- Mobile simple prev/next --}}
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-[3px] dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-[3px] hover:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>
                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-[3px] hover:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-[3px] dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            {{-- Desktop pagination --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-end">
                <div class="flex items-center gap-6 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">

                    <span class="flex items-center gap-1 rtl:flex-row-reverse">

                        {{-- Previous Button --}}
                        @if ($paginator->onFirstPage())
                            <span aria-disabled="true" class="w-9 h-9 flex items-center justify-center text-gray-300 bg-gray-50 border border-gray-100 rounded-full cursor-not-allowed dark:bg-gray-800/50 dark:border-gray-700 dark:text-gray-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        @else
                            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="w-9 h-9 flex items-center justify-center text-gray-600 bg-white border border-gray-200 rounded-full hover:bg-[#EC9824]/10 hover:text-[#EC9824] hover:border-[#EC9824]/40 transition-all shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-[#EC9824]/20 dark:hover:text-[#EC9824] focus:outline-none focus:ring-2 focus:ring-[#EC9824] focus:ring-offset-1 dark:focus:ring-offset-gray-900" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </button>
                        @endif

                        {{-- Smart truncated page numbers --}}
                        @php $prev = null; @endphp
                        @foreach ($pageNumbers as $page)
                            @if ($prev !== null && $page - $prev > 1)
                                <span class="w-9 h-9 flex items-center justify-center text-gray-400 dark:text-gray-600 font-bold select-none">…</span>
                            @endif

                            <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                @if ($page === $currentPage)
                                    <span aria-current="page" class="min-w-[36px] h-9 px-2 flex items-center justify-center text-sm font-semibold text-white bg-[#EC9824] border border-[#EC9824] rounded-md shadow-md dark:bg-[#EC9824] dark:border-[#EC9824]">{{ $page }}</span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="min-w-[36px] h-9 px-2 flex items-center justify-center text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-[#EC9824] focus:ring-offset-1 dark:focus:ring-offset-gray-900" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            </span>

                            @php $prev = $page; @endphp
                        @endforeach

                        {{-- Next Button --}}
                        @if ($paginator->hasMorePages())
                            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="w-9 h-9 flex items-center justify-center text-gray-600 bg-white border border-gray-200 rounded-full hover:bg-[#EC9824]/10 hover:text-[#EC9824] hover:border-[#EC9824]/40 transition-all shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-[#EC9824]/20 dark:hover:text-[#EC9824] focus:outline-none focus:ring-2 focus:ring-[#EC9824] focus:ring-offset-1 dark:focus:ring-offset-gray-900" aria-label="{{ __('pagination.next') }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </button>
                        @else
                            <span aria-disabled="true" class="w-9 h-9 flex items-center justify-center text-gray-300 bg-gray-50 border border-gray-100 rounded-full cursor-not-allowed dark:bg-gray-800/50 dark:border-gray-700 dark:text-gray-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif

                    </span>

                    {{-- Divider --}}
                    <div class="h-8 w-px bg-gray-200 dark:bg-gray-700"></div>

                    {{-- Go To Page --}}
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Go to</span>
                        <div class="relative flex items-center">
                            <input type="number" min="1" max="{{ $paginator->lastPage() }}"
                                x-data="{ page: {{ $paginator->currentPage() }} }"
                                x-model="page"
                                x-on:keydown.enter="$wire.gotoPage(page, '{{ $paginator->getPageName() }}'); {{ $scrollIntoViewJsSnippet }}"
                                class="w-16 h-9 pl-3 pr-8 text-sm font-medium text-center text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm focus:ring-2 focus:ring-[#EC9824] focus:border-[#EC9824] transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                placeholder="#" />
                            <button type="button"
                                x-data="{ page: {{ $paginator->currentPage() }} }"
                                x-on:click="let inputPage = $el.previousElementSibling.value; if(inputPage) { $wire.gotoPage(inputPage, '{{ $paginator->getPageName() }}'); {{ $scrollIntoViewJsSnippet }} }"
                                class="absolute right-1 w-6 h-6 flex items-center justify-center text-gray-400 hover:text-[#EC9824] dark:hover:text-[#EC9824] bg-gray-50 hover:bg-[#EC9824]/10 dark:bg-gray-700 dark:hover:bg-[#EC9824]/20 rounded transition-colors" title="Go">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </nav>
    @endif
</div>
