@php
$lastPage = $paginator->lastPage();
$currentPage = $paginator->currentPage();
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-2">
            
            <div class="text-xs text-gray-500 dark:text-gray-400 order-2 sm:order-1">
                <span>{!! __('Showing') !!}</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                <span>{!! __('to') !!}</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                <span>{!! __('of') !!}</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
                <span>{!! __('results') !!}</span>
            </div>

            <div class="flex items-center gap-1 order-1 sm:order-2 bg-gray-50 dark:bg-gray-800/50 p-1.5 rounded-[3px] border border-gray-100 dark:border-gray-800/50">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="p-1.5 rounded-[3px] text-gray-300 dark:text-gray-600 cursor-not-allowed" aria-hidden="true">
                        <x-icon name="chevron-left" class="w-4 h-4" stroke-width="2.5" />
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="p-1.5 rounded-[3px] text-gray-500 hover:bg-white hover:shadow-sm hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-all" rel="prev" aria-label="{{ __('pagination.previous') }}">
                        <x-icon name="chevron-left" class="w-4 h-4" stroke-width="2.5" />
                    </a>
                @endif

                {{-- Pagination Elements --}}
                <div class="flex items-center gap-1 mx-1">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-gray-400 dark:text-gray-500">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="w-8 h-8 flex items-center justify-center rounded-[3px] bg-[#fbb158] text-white text-xs font-bold shadow-sm shadow-[#fbb158]/30">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-[3px] text-xs font-semibold text-gray-500 hover:bg-white hover:shadow-sm hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-all" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="p-1.5 rounded-[3px] text-gray-500 hover:bg-white hover:shadow-sm hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-all" rel="next" aria-label="{{ __('pagination.next') }}">
                        <x-icon name="chevron-right" class="w-4 h-4" stroke-width="2.5" />
                    </a>
                @else
                    <span class="p-1.5 rounded-[3px] text-gray-300 dark:text-gray-600 cursor-not-allowed" aria-hidden="true">
                        <x-icon name="chevron-right" class="w-4 h-4" stroke-width="2.5" />
                    </span>
                @endif
            </div>
            
        </nav>
    @endif
</div>

