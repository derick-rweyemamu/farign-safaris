<div>
    @if(isset($categories) && $categories->count() > 1)
        <!-- TABS -->
        <div class="max-w-7xl mx-auto mb-10 relative z-10">
            <nav
                class="flex overflow-x-auto w-full border-b-2 border-[#EEE5D4] dark:border-gray-800 md:[&::-webkit-scrollbar]:hidden md:[-ms-overflow-style:none] md:[scrollbar-width:none]">
                @foreach($categories as $category)
                    <button wire:click="setActiveTab('{{ $category->slug }}')"
                        class="px-7 py-3 text-[13px] font-medium tracking-[0.06em] uppercase cursor-pointer border-b-2 -mb-[2px] transition-all duration-200 flex flex-shrink-0 items-center justify-center gap-2 bg-transparent whitespace-nowrap focus:outline-none 
                                                    {{ $activeCategoryTab === $category->slug
                    ? 'text-[#2E1F12] border-[#EC9824] dark:text-white dark:border-[#EC9824] bg-[#EC9824]/5 dark:bg-[#EC9824]/10'
                    : 'text-[#8C7565] border-transparent hover:text-[#EC9824] hover:border-[#EC9824]/50 dark:text-gray-400 dark:hover:text-[#EC9824] hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <span class="text-[16px]">✦</span>
                        {{ $category->name }}
                        @php
                            $minPrice = $category->tours()->min('adult_price');
                        @endphp
                        @if($minPrice > 0)
                            <span
                                class="{{ $activeCategoryTab === $category->slug ? 'bg-[rgba(201,168,76,0.18)] text-[#8B6914] dark:bg-[#EC9824]/20 dark:text-[#EC9824]' : 'bg-[#EEE5D4] text-[#8C7565] dark:bg-gray-800 dark:text-gray-450' }} text-[10px] px-2 py-0.5 rounded-full font-normal tracking-normal transition-colors duration-200">
                                {{__('From')}} ${{ number_format($minPrice, 0) }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>
    @endif

    <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200" wire:target="setActiveTab">
        @include('frontend.partials.tours-grid', ['tours' => $tours])
    </div>
</div>