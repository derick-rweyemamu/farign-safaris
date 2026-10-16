<div class="space-y-5 animate-fade-in w-full">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Navigation Menu') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage links shown in the website header and footer') }}</p>
        </div>
        <button wire:click="openModal" class="btn-primary flex items-center gap-2">
            <x-icon name="plus" class="w-4 h-4" />{{ __('Add Menu Item') }}</button>
    </div>

    <!-- Location Tabs -->
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800/50 p-1 rounded-[3px] w-fit overflow-x-auto max-w-full flex-wrap">
        @foreach($locations as $key => $label)
            <button wire:click="$set('activeLocation', '{{ $key }}')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-[3px] transition-all whitespace-nowrap
                    {{ $activeLocation === $key
                        ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm'
                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                @if($key === 'header')
                    <x-icon name="bars-3" class="w-4 h-4" />
                @else
                    <x-icon name="link" class="w-4 h-4" />
                @endif
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Menu Items Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        @if($items->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 text-xs font-bold uppercase tracking-wider text-gray-500 bg-gray-50 dark:bg-gray-800/40">
                        <th class="py-3 px-4 text-left w-10">#</th>
                        <th class="py-3 px-4 text-left">{{ __('Label') }}</th>
                        <th class="py-3 px-4 text-left">{{ __('URL / Page') }}</th>
                        <th class="py-3 px-4 text-center">{{ __('External') }}</th>
                        <th class="py-3 px-4 text-center">{{ __('Active') }}</th>
                        <th class="py-3 px-4 text-center">{{ __('Order') }}</th>
                        <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($items as $item)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition-colors group bg-white dark:bg-gray-900">
                            <td class="py-3 px-4 text-gray-400 font-mono text-xs font-bold">{{ $item->sort_order }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $item->label }}</span>
                                    @if($item->is_mega_menu)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#7d834a]/15 text-[#7d834a] border border-[#7d834a]/30">
                                            <x-icon name="squares-plus" class="w-3 h-3" />
                                            Mega Menu
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    @if($item->page)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold dark:bg-indigo-900/30 dark:text-indigo-400">
                                            <x-icon name="document-text" class="w-3 h-3" />
                                            Page: {{ $item->page->title }}
                                        </span>
                                    @endif
                                    <span class="text-gray-500 text-xs font-mono truncate max-w-[180px]">{{ $item->url }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->is_external)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                        <x-icon name="arrow-top-right-on-square" class="w-3.5 h-3.5" />{{ __('Yes') }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button wire:click="toggleActive({{ $item->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-all
                                        {{ $item->is_active
                                            ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ $item->is_active ? 'Visible' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="moveUp({{ $item->id }})" title="{{ __('Move Up') }}"
                                        class="p-1.5 rounded-[3px] text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                        <x-icon name="chevron-up" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="moveDown({{ $item->id }})" title="{{ __('Move Down') }}"
                                        class="p-1.5 rounded-[3px] text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                        <x-icon name="chevron-down" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <x-admin.action-dropdown>
                                    <button wire:click="edit({{ $item->id }})"
                                        class="p-1.5 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors" title="{{ __('Edit') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                        class="p-1.5 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="{{ __('Delete') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                        
                        @foreach($item->children as $child)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors group bg-gray-50/30 dark:bg-gray-800/20">
                                <td class="py-2.5 px-4 text-gray-400 font-mono text-[10px] pl-6 border-l-2 border-indigo-500/20">{{ $item->sort_order }}.{{ $child->sort_order }}</td>
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="arrow-turn-down-right" class="w-3 h-3 text-gray-400" />
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $child->label }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-2">
                                        @if($child->page)<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-semibold dark:bg-indigo-900/30 dark:text-indigo-400">
                                                <x-icon name="document-text" class="w-2.5 h-2.5" />
                                                {{ $child->page->title }}
                                            </span>
                                        @endif
                                        <span class="text-gray-500 text-[10px] font-mono truncate max-w-[150px]">{{ $child->url }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($child->is_external)
                                        <span class="text-xs font-medium text-amber-600 dark:text-amber-400">{{ __('Yes') }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <button wire:click="toggleActive({{ $child->id }})"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold transition-all
                                            {{ $child->is_active
                                                ? 'bg-emerald-100/50 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400'
                                                : 'bg-gray-100/50 text-gray-500 hover:bg-gray-200 dark:bg-gray-800/50 dark:text-gray-400' }}">
                                        {{ $child->is_active ? 'Visible' : 'Hidden' }}
                                    </button>
                                </td>
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="moveUp({{ $child->id }})" title="{{ __('Move Up') }}"
                                            class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                            <x-icon name="chevron-up" class="w-3 h-3" />
                                        </button>
                                        <button wire:click="moveDown({{ $child->id }})" title="{{ __('Move Down') }}"
                                            class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                            <x-icon name="chevron-down" class="w-3 h-3" />
                                        </button>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4">
                                    <x-admin.action-dropdown>
                                        <button wire:click="edit({{ $child->id }})"
                                            class="p-1.5 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors" title="{{ __('Edit') }}">
                                            <x-icon name="pencil" class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="confirmDelete({{ $child->id }})"
                                            class="p-1.5 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="{{ __('Delete') }}">
                                            <x-icon name="trash" class="w-3.5 h-3.5" />
                                        </button>
                                    </x-admin.action-dropdown>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <x-icon name="bars-3" class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('No menu items yet') }}</h3>
                <p class="text-xs text-gray-400 mb-4">{{ __('Add items to appear in the') }}<strong>{{ $locations[$activeLocation] ?? $activeLocation }}</strong>{{ __('section') }}</p>
                <button wire:click="openModal" class="btn-primary text-sm">
                    <x-icon name="plus" class="w-4 h-4 mr-1" />{{ __('Add First Item') }}</button>
            </div>
        @endif
    </div>

    <!-- Tip -->
    <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-[3px] p-4 text-sm text-amber-800 dark:text-amber-300">
        <x-icon name="light-bulb" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" />
        <div>
            <strong class="font-semibold">{{ __('Tip:') }}</strong>{{ __('Changes to navigation are reflected on the website') }}<strong>{{ __('immediately') }}</strong> — no cache clearing needed.
            Use the <em>{{ __('Active') }}</em>{{ __('toggle to temporarily hide a link without deleting it.') }}</div>
    </div>

    <!-- ── Add / Edit Modal ─────────────────────────────────────────────── -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
        <div class="bg-white dark:bg-gray-900 rounded-[3px] shadow-2xl w-full {{ $is_mega_menu ? 'max-w-5xl' : 'max-w-lg' }} max-h-[92vh] flex flex-col border border-gray-200 dark:border-gray-700"
             @click.outside="$wire.set('showModal', false)">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ $editingId ? 'Edit Menu Item' : 'Add Menu Item' }}
                </h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit="save" class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Location *</label>
                    <select wire:model="location" class="form-input">
                        @foreach($locations as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Label -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Display Label *</label>
                    <input type="text" wire:model="label" class="form-input" placeholder="{{ __('e.g. About Us') }}">
                    @error('label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Link Source -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Link Source') }}</label>

                    <!-- Link to a CMS Page -->
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Link to a CMS Page (optional)') }}</label>
                        <select wire:model="page_id" class="form-input text-sm">
                            <option value="">— Custom URL below —</option>
                            @foreach($pages as $page)
                                <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->slug }})</option>
                            @endforeach
                        </select>
                        @error('page_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Parent Menu Item -->
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Parent Menu Item (for dropdowns)') }}</label>
                        <select wire:model="parent_id" class="form-input text-sm">
                            <option value="">— Top Level (No Parent) —</option>
                            @foreach($parentOptions as $parent)
                                @if($editingId != $parent->id)<option value="{{ $parent->id }}">{{ $parent->label }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- OR Manual URL -->
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Custom URL (used when no page selected)') }}</label>
                        <input type="text" wire:model="url" class="form-input text-sm" placeholder="/tours  or  https://example.com">
                        @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Sort Order + Toggles -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 dark:bg-gray-800/50 p-3 rounded border border-gray-200 dark:border-gray-700">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Sort Order') }}</label>
                        <input type="number" wire:model="sort_order" class="form-input text-xs" min="0">
                    </div>
                    <div class="flex items-center pt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_external" class="rounded border-gray-300 text-indigo-600">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Open New Tab') }}</span>
                        </label>
                    </div>
                    <div class="flex items-center pt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Active') }}</span>
                        </label>
                    </div>
                    <div class="flex items-center pt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model.live="is_mega_menu" class="rounded border-[#7d834a] text-[#7d834a] focus:ring-[#7d834a]">
                            <span class="text-xs font-bold text-[#7d834a] flex items-center gap-1">
                                <x-icon name="squares-plus" class="w-3.5 h-3.5" />
                                {{ __('Mega Menu') }}
                            </span>
                        </label>
                    </div>
                </div>

                {{-- ── MEGA MENU BUILDER (Active when is_mega_menu is true) ── --}}
                @if($is_mega_menu)
                    <div class="border-t-2 border-[#7d834a] pt-4 mt-2 space-y-4 bg-[#7d834a]/5 p-4 rounded-md border border-[#7d834a]/20">
                        <div class="flex items-center justify-between pb-2 border-b border-[#7d834a]/20">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <x-icon name="squares-plus" class="w-4 h-4 text-[#7d834a]" />
                                    {{ __('Mega Menu Configuration (Dynamic Columns)') }}
                                </h4>
                                <p class="text-xs text-gray-500">{{ __('Customize your header title, add or remove columns (1 to 4 columns), and reorder them.') }}</p>
                            </div>
                        </div>

                        {{-- Top Header Title --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Mega Menu Header Title') }}</label>
                            <input type="text" wire:model="mega_menu_data.header_title" class="form-input text-sm" placeholder="e.g. Climb & Conquer">
                        </div>

                        {{-- Columns Section Header & Add Toolbar --}}
                        @php $adminColCount = count($mega_menu_data['columns'] ?? []); @endphp
                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                {{ __('Columns') }} ({{ $adminColCount }}/4)
                            </span>
                            @if($adminColCount < 4)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[11px] text-gray-400 mr-1">{{ __('+ Add Column:') }}</span>
                                    <button type="button" wire:click="addColumn('links')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 text-[11px] font-semibold transition-colors">
                                        <x-icon name="list-bullet" class="w-3 h-3" />
                                        {{ __('Link List') }}
                                    </button>
                                    <button type="button" wire:click="addColumn('media_list')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 text-[11px] font-semibold transition-colors">
                                        <x-icon name="photo" class="w-3 h-3" />
                                        {{ __('Thumbnail Cards') }}
                                    </button>
                                    <button type="button" wire:click="addColumn('promo')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 text-[11px] font-semibold transition-colors">
                                        <x-icon name="sparkles" class="w-3 h-3" />
                                        {{ __('Promo Card') }}
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if($adminColCount === 0)
                            <div class="p-6 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                                <x-icon name="squares-plus" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('No columns configured yet.') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('Click any "+ Add Column" button above to add a Link List, Thumbnail Cards, or Promo Card.') }}</p>
                            </div>
                        @endif

                        {{-- Dynamic Columns Cards --}}
                        <div class="grid grid-cols-1 {{ $adminColCount === 1 ? 'md:grid-cols-1' : ($adminColCount === 2 ? 'md:grid-cols-2' : ($adminColCount === 4 ? 'md:grid-cols-2 lg:grid-cols-4' : 'md:grid-cols-3')) }} gap-4 pt-1">
                            @foreach($mega_menu_data['columns'] ?? [] as $colIdx => $col)
                                @php $cType = $col['type'] ?? 'links'; @endphp
                                <div class="bg-white dark:bg-gray-800 p-3.5 rounded border border-gray-200 dark:border-gray-700 space-y-3 relative flex flex-col justify-between shadow-xs">
                                    
                                    {{-- Column Card Header --}}
                                    <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-mono">
                                                #{{ $colIdx + 1 }}
                                            </span>
                                            <span class="text-xs font-bold text-[#7d834a] uppercase tracking-wide">
                                                @if($cType === 'links')
                                                    {{ __('Routes / Links') }}
                                                @elseif($cType === 'media_list')
                                                    {{ __('Thumbnail Cards') }}
                                                @else
                                                    {{ __('Promo Banner') }}
                                                @endif
                                            </span>
                                        </div>

                                        {{-- Actions: Move & Remove --}}
                                        <div class="flex items-center gap-1">
                                            @if($colIdx > 0)
                                                <button type="button" wire:click="moveColumn({{ $colIdx }}, 'left')" title="{{ __('Move Left') }}" class="p-1 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <x-icon name="arrow-left" class="w-3 h-3" />
                                                </button>
                                            @endif
                                            @if($colIdx < count($mega_menu_data['columns']) - 1)
                                                <button type="button" wire:click="moveColumn({{ $colIdx }}, 'right')" title="{{ __('Move Right') }}" class="p-1 rounded text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <x-icon name="arrow-right" class="w-3 h-3" />
                                                </button>
                                            @endif
                                            <button type="button" wire:click="removeColumn({{ $colIdx }})" title="{{ __('Remove Column') }}" class="p-1 rounded text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 ml-1">
                                                <x-icon name="trash" class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Column Form Body --}}
                                    <div class="space-y-3 flex-1">
                                        @if($cType === 'links')
                                            <div>
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('Section Heading') }}</label>
                                                <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.title" class="form-input text-xs" placeholder="e.g. Mount Kilimanjaro">
                                            </div>

                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <label class="block text-[11px] text-gray-500">{{ __('Links') }}</label>
                                                    <button type="button" wire:click="addColItem({{ $colIdx }})" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-0.5">
                                                        <x-icon name="plus" class="w-2.5 h-2.5" /> {{ __('Add Link') }}
                                                    </button>
                                                </div>

                                                @foreach($col['items'] ?? [] as $rIdx => $rItem)
                                                    <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-800 space-y-1">
                                                        <div class="flex items-center gap-1">
                                                            <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $rIdx }}.label" class="form-input text-[11px] py-1 flex-1" placeholder="Label">
                                                            <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $rIdx }}.badge" class="form-input text-[11px] py-1 w-16" placeholder="Badge">
                                                            <button type="button" wire:click="removeColItem({{ $colIdx }}, {{ $rIdx }})" class="p-1 text-red-400 hover:text-red-600" title="{{ __('Remove') }}">
                                                                <x-icon name="trash" class="w-3 h-3" />
                                                            </button>
                                                        </div>
                                                        <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $rIdx }}.url" class="form-input text-[10px] py-0.5 font-mono text-gray-500" placeholder="URL">
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('Bottom Button (Label & URL)') }}</label>
                                                <div class="grid grid-cols-2 gap-1.5">
                                                    <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.btn_text" class="form-input text-xs" placeholder="View All Packages">
                                                    <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.btn_url" class="form-input text-xs" placeholder="/tours">
                                                </div>
                                            </div>

                                        @elseif($cType === 'media_list')
                                            <div>
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('Section Heading (Optional)') }}</label>
                                                <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.title" class="form-input text-xs" placeholder="e.g. Featured Treks">
                                            </div>

                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <label class="block text-[11px] text-gray-500">{{ __('Thumbnail Items') }}</label>
                                                    <button type="button" wire:click="addColItem({{ $colIdx }})" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-0.5">
                                                        <x-icon name="plus" class="w-2.5 h-2.5" /> {{ __('Add Card') }}
                                                    </button>
                                                </div>

                                                @foreach($col['items'] ?? [] as $mIdx => $mItem)
                                                    <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-800 space-y-1">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Item #{{ $mIdx + 1 }}</span>
                                                            <button type="button" wire:click="removeColItem({{ $colIdx }}, {{ $mIdx }})" class="text-red-400 hover:text-red-600">
                                                                <x-icon name="trash" class="w-3 h-3" />
                                                            </button>
                                                        </div>
                                                        <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $mIdx }}.title" class="form-input text-xs py-1" placeholder="Title">
                                                        <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $mIdx }}.subtitle" class="form-input text-xs py-1" placeholder="Subtitle">
                                                        <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $mIdx }}.image" class="form-input text-[10px] py-0.5 font-mono" placeholder="Image URL">
                                                        <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.items.{{ $mIdx }}.url" class="form-input text-[10px] py-0.5 font-mono text-gray-500" placeholder="Target URL">
                                                    </div>
                                                @endforeach
                                            </div>

                                        @elseif($cType === 'promo')
                                            <div>
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('Card Heading') }}</label>
                                                <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.title" class="form-input text-xs" placeholder="Card Title">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('Promo Image URL') }}</label>
                                                <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.image" class="form-input text-[10px] py-1 font-mono" placeholder="/images/mega-menu/...">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('CTA Button Text & URL') }}</label>
                                                <div class="grid grid-cols-2 gap-1.5">
                                                    <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.btn_text" class="form-input text-xs" placeholder="Explore">
                                                    <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.btn_url" class="form-input text-xs" placeholder="/page">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] text-gray-500 mb-0.5">{{ __('Card Background Color') }}</label>
                                                <div class="flex items-center gap-2">
                                                    <input type="color" wire:model="mega_menu_data.columns.{{ $colIdx }}.bg_color" class="w-8 h-8 rounded border p-0 cursor-pointer">
                                                    <input type="text" wire:model="mega_menu_data.columns.{{ $colIdx }}.bg_color" class="form-input text-xs font-mono flex-1" placeholder="#7d834a">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update' : 'Add Item' }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ── Delete Confirm Modal ─────────────────────────────────────────── -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-900 rounded-[3px] shadow-2xl w-full max-w-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Delete Menu Item?') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('This will remove it from the website navigation immediately.') }}</p>
            <div class="flex gap-3 justify-center">
                <button wire:click="$set('showDeleteModal', false)" class="btn-secondary flex-1">{{ __('Cancel') }}</button>
                <button wire:click="delete" class="btn-danger flex-1" wire:loading.attr="disabled" title="{{ __('Delete Navigation Menu Manager') }}">
                    <x-icon name="trash" class="w-4 h-4" wire:loading.remove wire:target="delete" />
                    <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="delete" />
                    <span wire:loading.remove wire:target="delete">{{ __('Delete') }}</span>
                    <span wire:loading wire:target="delete">{{ __('Deleting...') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

