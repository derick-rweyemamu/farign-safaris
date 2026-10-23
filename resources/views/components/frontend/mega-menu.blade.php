@props(['item'])

@php
    $data = $item->mega_menu_data ?? [];
    $headerTitle = $data['header_title'] ?? __('Climb & Conquer');
    $columns = $item->getMegaMenuColumns();
    $colCount = count($columns);

    // Dynamic width based on number of columns
    $widthClass = match($colCount) {
        1 => 'w-[380px] max-w-[95vw]',
        2 => 'w-[740px] max-w-[95vw]',
        4 => 'w-[1180px] max-w-[96vw]',
        default => 'w-[980px] max-w-[95vw]',
    };

    $gridClass = match($colCount) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-3',
    };
@endphp

<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     @mouseenter="open = true"
     @mouseleave="open = false"
     class="absolute left-1/2 -translate-x-1/2 {{ $widthClass }} top-full mt-0 bg-white shadow-2xl border-t-2 border-[#7d834a] z-[100] text-left cursor-default select-text rounded-b-[6px]"
     style="display: none; background-color: #ffffff;">

    {{-- Top Center Accent Notch pointing directly to parent tab --}}
    <div class="absolute -top-[5px] left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-[#7d834a] rotate-45 rounded-[1px] pointer-events-none"></div>

        {{-- ══ TOP HEADER ══ --}}
        @if(!empty($headerTitle))
            <div class="pt-4 pb-2.5 text-center border-b border-gray-100">
                <h3 class="text-[16px] font-bold tracking-tight text-[#1c2211] font-serif" style="font-family: 'Playfair Display', Georgia, serif;">
                    {{ $headerTitle }}
                </h3>
            </div>
        @endif

    {{-- ══ DYNAMIC CONTENT GRID ══ --}}
    <div class="px-6 py-5 grid {{ $gridClass }} gap-5 items-start w-full">
        @foreach($columns as $col)
            @php $colType = $col['type'] ?? 'links'; @endphp

            @if($colType === 'links')
                {{-- Column: Link / Route List --}}
                <div class="flex flex-col justify-between pr-1 min-w-0 w-full overflow-hidden">
                    <div class="w-full min-w-0">
                        @if(!empty($col['title']))
                            <h4 class="text-[13px] font-bold text-gray-900 mb-2.5 tracking-wider uppercase truncate" style="font-family: 'Playfair Display', Georgia, serif;">
                                {{ $col['title'] }}
                            </h4>
                        @endif
                        <ul class="divide-y divide-gray-100 w-full min-w-0">
                            @foreach($col['items'] ?? [] as $route)
                                @php
                                    $routeUrl = $route['url'] ?? '#';
                                    if ($routeUrl !== '#' && !str_starts_with($routeUrl, 'http') && !str_starts_with($routeUrl, '/') && !str_starts_with($routeUrl, '#')) {
                                        $routeUrl = url($routeUrl);
                                    }
                                    $routeLabel = trim($route['label'] ?? '');
                                    $routeBadge = trim($route['badge'] ?? '');
                                    $hasLabel = !empty($routeLabel);
                                    $hasBadge = !empty($routeBadge);
                                @endphp
                                <li class="py-2 w-full min-w-0">
                                    <a href="{{ $routeUrl }}" class="group/item flex items-start justify-between gap-2 text-[11px] text-gray-700 hover:text-[#7d834a] transition-colors leading-snug w-full min-w-0">
                                        <span class="flex items-start gap-1.5 flex-1 min-w-0">
                                            <span class="text-gray-400 group-hover/item:text-[#7d834a] transition-transform duration-200 group-hover/item:translate-x-0.5 font-bold mt-0.5 flex-shrink-0 text-[10px]">→</span>
                                            @if($hasLabel)
                                                <span class="font-medium tracking-tight break-words text-gray-800 group-hover/item:text-[#7d834a] min-w-0">{{ $routeLabel }}</span>
                                            @endif
                                        </span>
                                        @if($hasBadge)
                                            <span class="inline-block bg-[#333a18] text-white text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded leading-tight shadow-xs {{ $hasLabel ? 'max-w-[50%]' : 'flex-1 max-w-full' }} whitespace-normal break-words text-center flex-shrink-0">
                                                {{ $routeBadge }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if(!empty($col['btn_text']))
                        <div class="mt-4 w-full min-w-0">
                            <a href="{{ url($col['btn_url'] ?? '#') }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#838a53] hover:bg-[#727944] text-white text-[11px] font-bold tracking-wide rounded-[3px] shadow-sm transition-all duration-200 max-w-full truncate">
                                <span class="text-[12px] font-bold leading-none flex-shrink-0">»</span>
                                <span class="truncate">{{ $col['btn_text'] }}</span>
                            </a>
                        </div>
                    @endif
                </div>

            @elseif($colType === 'media_list')
                {{-- Column: Media Thumbnail Cards --}}
                <div class="flex flex-col justify-center space-y-3 px-1 min-w-0 w-full overflow-hidden">
                    @if(!empty($col['title']))
                        <h4 class="text-[13px] font-bold text-gray-900 mb-1 tracking-wider uppercase truncate" style="font-family: 'Playfair Display', Georgia, serif;">
                            {{ $col['title'] }}
                        </h4>
                    @endif
                    @foreach($col['items'] ?? [] as $mediaItem)
                        @php
                            $mUrl = $mediaItem['url'] ?? '#';
                            if ($mUrl !== '#' && !str_starts_with($mUrl, 'http') && !str_starts_with($mUrl, '/') && !str_starts_with($mUrl, '#')) {
                                $mUrl = url($mUrl);
                            }
                            $mImg = $mediaItem['image'] ?? asset('images/mega-menu/mount-meru.jpg');
                            if (!str_starts_with($mImg, 'http') && !str_starts_with($mImg, '/')) {
                                $mImg = asset('storage/' . $mImg);
                            }
                        @endphp
                        <a href="{{ $mUrl }}" class="group/trek flex items-center gap-2.5 p-1 rounded-md hover:bg-gray-50/80 transition-all duration-200 w-full min-w-0 overflow-hidden">
                            <div class="w-[56px] h-[40px] rounded-[4px] overflow-hidden flex-shrink-0 shadow-xs border border-gray-100 bg-gray-100">
                                <img src="{{ $mImg }}" alt="{{ $mediaItem['title'] ?? '' }}" class="w-full h-full object-cover group-hover/trek:scale-105 transition-transform duration-300">
                            </div>
                            <div class="flex-1 min-w-0 overflow-hidden">
                                <h5 class="text-[11.5px] font-bold text-gray-900 group-hover/trek:text-[#7d834a] transition-colors leading-tight break-words">
                                    {{ $mediaItem['title'] ?? '' }}
                                </h5>
                                @if(!empty($mediaItem['subtitle']))
                                    <p class="text-[10px] text-gray-500 mt-0.5 leading-snug break-words">
                                        {{ $mediaItem['subtitle'] }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

            @elseif($colType === 'promo')
                {{-- Column: Highlight Promo Card --}}
                @php
                    $pImg = !empty($col['image']) ? (str_starts_with($col['image'], 'http') || str_starts_with($col['image'], '/') ? $col['image'] : asset('storage/' . $col['image'])) : asset('images/mega-menu/kilimanjaro-summit.jpg');
                    $pBg = $col['bg_color'] ?? '#7d834a';
                @endphp
                <div class="min-w-0 w-full overflow-hidden">
                    <div class="p-3.5 rounded-[4px] shadow-sm text-white flex flex-col justify-between w-full min-w-0"
                         style="background-color: {{ $pBg }}; min-height: 260px;">
                        
                        {{-- Promo Image --}}
                        <div class="w-full h-[135px] rounded-[3px] overflow-hidden shadow-xs mb-2.5 bg-black/10">
                            <img src="{{ $pImg }}" alt="{{ $col['title'] ?? '' }}" class="w-full h-full object-cover">
                        </div>

                        {{-- Promo Heading --}}
                        <div class="mb-2.5 min-w-0">
                            <h5 class="text-[13px] font-semibold text-white tracking-wide leading-snug font-serif break-words" style="font-family: 'Playfair Display', Georgia, serif;">
                                {{ $col['title'] ?? '' }}
                            </h5>
                        </div>

                        {{-- Promo Button --}}
                        @if(!empty($col['btn_text']))
                            <div class="w-full min-w-0">
                                <a href="{{ url($col['btn_url'] ?? '#') }}"
                                   class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 bg-[#c69752] hover:bg-[#b58742] text-white text-[10.5px] font-bold tracking-wider rounded-[3px] shadow transition-all duration-200 text-center truncate">
                                    <span class="text-[12px] font-bold leading-none flex-shrink-0">»</span>
                                    <span class="truncate">{{ $col['btn_text'] }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

