<div class="space-y-4 animate-fade-in">

    {{-- ─────────────────────────────────────── Header ─────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-card p-4 rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cost-sheets.index') }}" class="text-gray-400 hover:text-indigo-600 transition-colors" title="{{ __('Back to Cost Sheets') }}">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $isEdit ? 'Edit Cost Sheet' : 'New Cost Sheet' }}
                </h2>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-7">
                @if($isEdit)
                    Refining cost calculations for "{{ $title }}"
                @else
                    Create a new multi-day cost calculation matrix
                @endif
            </p>
        </div>

        {{-- Progress steps indicator --}}
        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-850 px-4 py-2 rounded-[3px] border border-gray-150 dark:border-gray-800">
            {{-- Step 1 --}}
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $step > 1 ? 'bg-green-500 text-white' : ($step === 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400') }}">
                    @if($step > 1)
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    @else
                        1
                    @endif
                </div>
                <span class="text-xs {{ $step === 1 ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-400' }}">{{ __('Trip Setup') }}</span>
            </div>
            <span class="text-gray-300 dark:text-gray-650 text-sm font-semibold">/</span>
            {{-- Step 2 --}}
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $step > 2 ? 'bg-green-500 text-white' : ($step === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400') }}">
                    @if($step > 2)
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    @else
                        2
                    @endif
                </div>
                <span class="text-xs {{ $step === 2 ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-400' }}">{{ __('Day by Day') }}</span>
            </div>
            <span class="text-gray-300 dark:text-gray-650 text-sm font-semibold">/</span>
            {{-- Step 3 --}}
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $step === 4 ? 'bg-indigo-600 text-white' : 'bg-gray-250 dark:bg-gray-700 text-gray-400' }}">
                    3
                </div>
                <span class="text-xs {{ $step === 4 ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-400' }}">{{ __('Matrix &amp; Summary') }}</span>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────── Main Content ───────────────── --}}
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-6">

        {{-- ══════════════ STEP 1: Trip Setup ══════════════ --}}
        @if($step === 1)
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Cost Sheet Title *</label>
                        <input type="text" wire:model.live="title" class="form-input" placeholder="{{ __('e.g. Serengeti 5-Day Safari — Johnson Family') }}">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Season --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Season *</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('season', 'high')"
                                class="flex items-center justify-center gap-2 p-3 rounded-[3px] border-2 transition-all
                                    {{ $season === 'high'
            ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400'
            : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-orange-300' }}">
                                <x-icon name="sun" class="w-4 h-4" />
                                <span class="text-sm font-medium">{{ __('High Season') }}</span>
                            </button>
                            <button type="button" wire:click="$set('season', 'low')"
                                class="flex items-center justify-center gap-2 p-3 rounded-[3px] border-2 transition-all
                                    {{ $season === 'low'
            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'
            : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-blue-300' }}">
                                <x-icon name="cloud" class="w-4 h-4" />
                                <span class="text-sm font-medium">{{ __('Low Season') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Dates + Duration in one row --}}
                    <div class="md:col-span-2 border border-gray-100 dark:border-gray-800 p-6">
                      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 items-end">
                        {{-- Start Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Start Date') }}<span class="text-xs text-gray-400 font-normal">(optional)</span>
                            </label>
                            <input type="date" wire:model.blur="start_date" class="form-input w-full">
                            @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                            {{-- End Date --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('End Date') }}<span class="text-xs text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input type="date" wire:model.blur="end_date" class="form-input w-full">
            @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

    {{-- Duration --}}
    @if($start_date && $end_date)
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Duration') }}</label>
            <div class="flex items-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-[3px] border border-indigo-100 dark:border-indigo-800 h-[38px]">
                <x-icon name="calendar-days" class="w-4 h-4 text-indigo-500 shrink-0" />
                <span class="text-sm text-indigo-700 dark:text-indigo-300 font-semibold whitespace-nowrap">
                    {{ $duration_days }} day{{ $duration_days != 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    @else
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Days *
            </label>
            <input type="number" wire:model.live="duration_days" min="1" max="30" class="form-input w-full" placeholder="e.g. 5">
        </div>
    @endif
</div>
                    </div>

                    {{-- ── Travellers & Vehicles ── --}}
                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                            <x-icon name="users" class="w-4 h-4 text-indigo-500" />{{ __('Group Composition') }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="user" class="w-3.5 h-3.5 text-indigo-500" /> Adults *
                                    </span>
                                </label>
                                <input type="number" wire:model.blur="num_adults" min="1" class="form-input" placeholder="2">
                                @error('num_adults') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="user" class="w-3.5 h-3.5 text-amber-500" />{{ __('Children') }}</span>
                                </label>
                                <input type="number" wire:model.blur="num_children" min="0" class="form-input" placeholder="0">
                                @error('num_children') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="truck" class="w-3.5 h-3.5 text-green-600" /> Vehicles *
                                    </span>
                                </label>
                                <input type="number" wire:model.blur="num_vehicles" min="1" class="form-input" placeholder="1">
                                @error('num_vehicles') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Pax summary pill --}}
                        @if($num_adults > 0)
                            <div class="mt-3 flex items-center gap-3 flex-wrap">
                                <div class="flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-full px-3 py-1 text-xs">
                                    <x-icon name="user" class="w-3.5 h-3.5 text-indigo-500" />
                                    <span class="text-indigo-700 dark:text-indigo-300 font-medium">{{ $num_adults }} Adult{{ $num_adults != 1 ? 's' : '' }}</span>
                                </div>
                                @if($num_children > 0)
                                    <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-full px-3 py-1 text-xs">
                                        <x-icon name="user" class="w-3.5 h-3.5 text-amber-500" />
                                        <span class="text-amber-700 dark:text-amber-300 font-medium">{{ $num_children }} Child{{ $num_children != 1 ? 'ren' : '' }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-3 py-1 text-xs">
                                    <x-icon name="users" class="w-3.5 h-3.5 text-gray-500" />
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $num_adults + $num_children }} Total Pax</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Internal Notes') }}</label>
                        <textarea wire:model="notes" rows="3" class="form-textarea w-full resize-none" placeholder="{{ __('Any internal notes about this cost sheet...') }}"></textarea>
                    </div>
                </div>
            </div>
        @endif

        {{-- ══════════════ STEP 2: Day-by-Day Expenses ══════════════ --}}
        @if($step === 2)
            @php
                $totalDays = count($matrixDays);
                $currentDay = $matrixDays[$currentDayStep] ?? null;
                $dayLabel = $currentDay['label'] ?? ('Day ' . ($currentDayStep + 1));
                $rowColorMap = [
                    'car' => ['bg' => '#ecfdf5', 'text' => '#047857', 'dark_bg' => 'bg-emerald-900/10', 'dark_text' => 'text-emerald-400', 'border' => 'border-emerald-200'],
                    'park_fee' => ['bg' => '#fffbeb', 'text' => '#92400e', 'dark_bg' => 'bg-amber-900/10', 'dark_text' => 'text-amber-400', 'border' => 'border-amber-200'],
                    'park_fee_adult' => ['bg' => '#fffbeb', 'text' => '#92400e', 'dark_bg' => 'bg-amber-900/10', 'dark_text' => 'text-amber-400', 'border' => 'border-amber-200'],
                    'park_fee_child' => ['bg' => '#fef3c7', 'text' => '#b45309', 'dark_bg' => 'bg-yellow-900/10', 'dark_text' => 'text-yellow-400', 'border' => 'border-yellow-200'],
                    'pass_fee' => ['bg' => '#fdf4ff', 'text' => '#7e22ce', 'dark_bg' => 'bg-purple-900/10', 'dark_text' => 'text-purple-400', 'border' => 'border-purple-200'],
                    'pass_fee_adult' => ['bg' => '#fdf4ff', 'text' => '#7e22ce', 'dark_bg' => 'bg-purple-900/10', 'dark_text' => 'text-purple-400', 'border' => 'border-purple-200'],
                    'pass_fee_child' => ['bg' => '#fae8ff', 'text' => '#a21caf', 'dark_bg' => 'bg-fuchsia-900/10', 'dark_text' => 'text-fuchsia-400', 'border' => 'border-fuchsia-200'],
                    'crater' => ['bg' => '#f0fdf4', 'text' => '#166534', 'dark_bg' => 'bg-green-900/10', 'dark_text' => 'text-green-400', 'border' => 'border-green-200'],
                    'crater_adult' => ['bg' => '#f0fdf4', 'text' => '#166534', 'dark_bg' => 'bg-green-900/10', 'dark_text' => 'text-green-400', 'border' => 'border-green-200'],
                    'crater_child' => ['bg' => '#dcfce7', 'text' => '#15803d', 'dark_bg' => 'bg-green-900/20', 'dark_text' => 'text-green-300', 'border' => 'border-green-300'],
                    'concession' => ['bg' => '#eef2ff', 'text' => '#3730a3', 'dark_bg' => 'bg-indigo-900/10', 'dark_text' => 'text-indigo-400', 'border' => 'border-indigo-200'],
                    'concession_adult' => ['bg' => '#eef2ff', 'text' => '#3730a3', 'dark_bg' => 'bg-indigo-900/10', 'dark_text' => 'text-indigo-400', 'border' => 'border-indigo-200'],
                    'concession_child' => ['bg' => '#e0e7ff', 'text' => '#4338ca', 'dark_bg' => 'bg-indigo-900/20', 'dark_text' => 'text-indigo-300', 'border' => 'border-indigo-300'],
                    'accommodation' => ['bg' => '#faf5ff', 'text' => '#6b21a8', 'dark_bg' => 'bg-violet-900/10', 'dark_text' => 'text-violet-400', 'border' => 'border-violet-200'],
                    'snacks' => ['bg' => '#fff7ed', 'text' => '#9a3412', 'dark_bg' => 'bg-orange-900/10', 'dark_text' => 'text-orange-400', 'border' => 'border-orange-200'],
                    'activity' => ['bg' => '#f0f9ff', 'text' => '#075985', 'dark_bg' => 'bg-sky-900/10', 'dark_text' => 'text-sky-400', 'border' => 'border-sky-200'],
                    'custom' => ['bg' => '#f9fafb', 'text' => '#374151', 'dark_bg' => 'bg-gray-800/30', 'dark_text' => 'text-gray-400', 'border' => 'border-gray-200'],
                ];
            @endphp
            <div class="space-y-5">

                {{-- ── Day Step Progress Bar ────────────────────── --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-[3px] border border-gray-100 dark:border-gray-700 p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Days Progress') }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Day {{ $currentDayStep + 1 }} of {{ $totalDays }}</span>
                    </div>
                    <div class="flex items-center gap-0.5 overflow-x-auto pb-1">
                        @foreach($matrixDays as $di => $mDay)
                                        <button type="button" wire:click="$set('currentDayStep', {{ $di }})"
                                            class="flex flex-col items-center gap-1 px-2 py-1 shrink-0 rounded-[3px] transition-all
                                                {{ $di === $currentDayStep ? 'opacity-100' : 'opacity-50 hover:opacity-80' }}">
                                            <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-all
                                                {{ $di < $currentDayStep
                            ? 'bg-green-500 border-green-500 text-white'
                            : ($di === $currentDayStep
                                ? 'bg-indigo-600 border-indigo-600 text-white shadow-md'
                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500') }}">
                                                @if($di < $currentDayStep)
                                                    <x-icon name="check" class="w-3 h-3" />
                                                @else
                                                    {{ $di + 1 }}
                                                @endif
                                            </div>
                                            <span class="text-[10px] max-w-[56px] truncate font-medium
                                                {{ $di === $currentDayStep ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}">
                                                {{ $mDay['label'] ?? 'Day ' . ($di + 1) }}
                                            </span>
                                        </button>
                                        @if(!$loop->last)<div class="w-4 h-px bg-gray-200 dark:bg-gray-700 shrink-0 mx-0.5"></div>
                                        @endif
                        @endforeach
                    </div>
                </div>

                {{-- ── Active Day Header ────────────────────── --}}
                <div class="rounded-[3px] overflow-hidden border border-indigo-200 dark:border-indigo-800 shadow-sm">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 w-full sm:flex-1">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-lg shrink-0">
                                {{ $currentDayStep + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="text-indigo-200 text-xs font-medium uppercase tracking-wider">Day {{ $currentDayStep + 1 }} of {{ $totalDays }}</p>
                                <input type="text" wire:model.lazy="matrixDays.{{ $currentDayStep }}.label"
                                    class="bg-transparent border-0 text-white font-bold text-lg p-0 focus:ring-0 placeholder-white/50 w-full"
                                    placeholder="{{ __('e.g. Arusha - Tarangire') }}">
                            </div>
                        </div>
                        {{-- Destination selector --}}
                        <div class="sm:w-52 shrink-0">
                            <label class="block text-indigo-200 text-xs mb-1">{{ __('Destination') }}</label>
                            <select wire:model.live="matrixDays.{{ $currentDayStep }}.destination_id"
                                class="w-full bg-white/10 border border-white/20 text-white text-sm rounded-[3px] py-1.5 px-2 focus:ring-1 focus:ring-white/40 dark:bg-indigo-900/30">
                                <option value="" class="text-gray-800 dark:text-white bg-white dark:bg-gray-800">— Select Destination —</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" class="text-gray-800 dark:text-white bg-white dark:bg-gray-800">{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ── Add Cost Row Form ────────────────────── --}}
                    <div class="bg-indigo-50/50 dark:bg-indigo-900/10 px-5 py-4 border-b border-indigo-100 dark:border-indigo-800">
                        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                            <x-icon name="plus-circle" class="w-4 h-4 text-indigo-500" />{{ __('Add Cost Item') }}</h4>
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="flex-shrink-0">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Cost Type') }}</label>
                                <select wire:model.live="newRowType" class="form-input text-xs py-1.5 px-2 w-40 bg-white dark:bg-gray-800">
                                    <option value="car">{{ __('Car / Vehicle') }}</option>
                                    <option value="park_fee">{{ __('Park Fee') }}</option>
                                    <option value="pass_fee">{{ __('Pass Fee') }}</option>
                                    <option value="crater">{{ __('Crater Fee') }}</option>
                                    <option value="concession">{{ __('Concession') }}</option>
                                    <option value="snacks">{{ __('Meals / Snacks') }}</option>
                                    <option value="accommodation">{{ __('Accommodation') }}</option>
                                    <option value="activity">{{ __('Activity') }}</option>
                                    <option value="custom">{{ __('Custom') }}</option>
                                </select>
                            </div>
                            <div class="flex-1 min-w-[160px]">
                                @if($newRowType === 'car')
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Select Vehicle') }}</label>
                                    <select wire:model.live="newRowItemId" class="form-input text-xs py-1.5 w-full bg-white dark:bg-gray-800">
                                        <option value="">-- Choose Vehicle --</option>
                                        @foreach($transports as $transport)
                                            <option value="{{ $transport->id }}">{{ $transport->name }} (${{ number_format($transport->price, 2) }})</option>
                                        @endforeach
                                    </select>
                                @elseif(in_array($newRowType, ['park_fee', 'pass_fee', 'crater', 'concession']))
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Rows to be added') }}</label>
                                    <div class="flex items-center gap-2 h-8">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-medium">
                                            <x-icon name="user" class="w-3 h-3" />{{ __('Adult') }}</span>
                                        @if($num_children > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-xs font-medium">
                                            <x-icon name="user" class="w-3 h-3" /> Child ({{ $num_children }})
                                        </span>
                                        @else
                                        <span class="text-xs text-gray-400 italic">{{ __('No children in group') }}</span>
                                        @endif
                                    </div>
                                @elseif($newRowType === 'snacks')
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Select Meal Plan') }}</label>
                                    <select wire:model.live="newRowItemId" class="form-input text-xs py-1.5 w-full bg-white dark:bg-gray-800">
                                        <option value="">-- Choose Meal Plan --</option>
                                        @foreach($mealPlans as $meal)
                                            <option value="{{ $meal->id }}">{{ $meal->name }} (${{ $meal->price }})</option>
                                        @endforeach
                                    </select>
                                @elseif($newRowType === 'accommodation')
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Select Accommodation') }}</label>
                                    <select wire:model.live="newRowItemId" class="form-input text-xs py-1.5 w-full bg-white dark:bg-gray-800">
                                        <option value="">-- Choose Accommodation --</option>
                                        @foreach($accommodations as $acc)
                                            <optgroup label="{{ $acc->name }}">
                                                @foreach($acc->rates as $rate)
                                                    <option value="{{ $rate->id }}">{{ $rate->room_type }} - {{ $rate->class }} (${{ $rate->sto ?? $rate->rack }})</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                @elseif($newRowType === 'activity')
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Select Activity') }}</label>
                                    <select wire:model.live="newRowItemId" class="form-input text-xs py-1.5 w-full bg-white dark:bg-gray-800">
                                        <option value="">-- Choose Activity --</option>
                                        @foreach($activities as $act)
                                            <option value="{{ $act->id }}">{{ $act->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Row Label') }}<span class="font-normal opacity-60">(optional)</span></label>
                                    <input type="text" wire:model="newRowLabel" placeholder="{{ __('e.g. Custom expense…') }}" class="form-input text-xs py-1.5 w-full bg-white dark:bg-gray-800">
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Multiplier') }}</label>
                                <div class="flex items-center gap-1">
                                    {{-- × Pax --}}
                                    <label class="flex items-center gap-1.5 cursor-pointer h-8 px-3 rounded-l-[3px] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
                                        title="{{ __('Multiply amount by number of pax') }}">
                                        <input type="checkbox" wire:model.live="newRowIsPerPax"
                                            @if(in_array($newRowType, ['park_fee','pass_fee','crater','concession'])) disabled @endif
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">× Pax</span>
                                    </label>
                                    {{-- ÷ Pax --}}
                                    <label class="flex items-center gap-1.5 cursor-pointer h-8 px-3 rounded-r-[3px] border border-l-0 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
                                        title="{{ __('Divide amount equally among total pax') }}">
                                        <input type="checkbox" wire:model.live="newRowIsShared"
                                            @if(in_array($newRowType, ['park_fee','pass_fee','crater','concession'])) disabled @endif
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">÷ Pax</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">&nbsp;</label>
                                <button type="button" wire:click="addMatrixRow"
                                    class="flex items-center gap-1.5 px-4 py-1.5 rounded-[3px] bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition-colors shadow-sm h-8">
                                    <x-icon name="plus" class="w-3.5 h-3.5" />{{ __('Add Item') }}</button>
                            </div>
                        </div>
                    </div>

                    {{-- ── Cost Rows for This Day ────────────────────── --}}
                    <div class="bg-white dark:bg-gray-900 p-5">
                        @if(empty($matrixRows))
                            <div class="text-center py-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-[3px]">
                                <x-icon name="banknotes" class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('No cost items defined yet') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-xs mx-auto">{{ __('Use the form above to add your first cost category (e.g. Car, Park Fee, Accommodation).') }}</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                        <x-icon name="banknotes" class="w-4 h-4 text-indigo-500" />{{ __('Cost Entries') }}</h4>
                                    <div class="text-xs text-gray-400 flex items-center gap-4">
                                        <span><strong class="text-indigo-600 dark:text-indigo-400">{{ $num_adults }}</strong> Adult{{ $num_adults != 1 ? 's' : '' }}</span>
                                        @if($num_children > 0)
                                            <span><strong class="text-amber-600 dark:text-amber-400">{{ $num_children }}</strong> Child{{ $num_children != 1 ? 'ren' : '' }}</span>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    $visibleRowsCount = 0;
                                @endphp
                                @foreach($matrixRows as $ri => $row)
                                    @php
                                        $amt = (float) ($row['amounts'][$currentDayStep] ?? 0);
                                        $isActive = in_array($currentDayStep, $row['active_days'] ?? []) || $amt > 0;
                                    @endphp
                                    @if($isActive)
                                        @php $visibleRowsCount++; @endphp
                                    @php
                                        $rc = $rowColorMap[$row['type'] ?? 'custom'] ?? $rowColorMap['custom'];
                                        $isChild  = str_ends_with($row['type'] ?? '', '_child');
                                        $isPax    = $row['is_per_pax'] ?? false;
                                        $isShared = $row['is_shared'] ?? false;
                                        $totalPax = max(1, $num_adults + $num_children);
                                        $mult     = $isPax ? ($isChild ? $num_children : $num_adults) : 1;
                                        $amt      = (float) ($row['amounts'][$currentDayStep] ?? 0);
                                        $computed = round($amt * $mult, 2);
                                        $perPerson = $isShared && $totalPax > 0 ? round($amt / $totalPax, 2) : null;
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 rounded-[3px] border transition-all"
                                        style="background-color: {{ $rc['bg'] }}; border-color: {{ $rc['text'] }}25;">
                                        {{-- Label & multiplier info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold truncate" style="color: {{ $rc['text'] }}">{{ $row['label'] }}</p>
                                            <p class="text-xs mt-0.5" style="color: {{ $rc['text'] }}; opacity: 0.7;">
                                                @if($isPax)
                                                    @if($isChild)
                                                        × {{ $num_children }} child{{ $num_children != 1 ? 'ren' : '' }}
                                                    @else
                                                        × {{ $num_adults }} adult{{ $num_adults != 1 ? 's' : '' }}
                                                    @endif
                                                    @if($computed > 0)
                                                        &nbsp;=&nbsp;<strong>${{ number_format($computed, 2) }}</strong>
                                                    @endif
                                                @elseif($isShared)
                                                    ÷ {{ $totalPax }} pax
                                                    @if($amt > 0)
                                                        &nbsp;=&nbsp;<strong>${{ number_format($perPerson, 2) }}/person</strong>
                                                    @endif
                                                @else
                                                    Fixed (not per person)
                                                @endif
                                            </p>
                                        </div>
                                        {{-- Amount input --}}
                                        <div class="w-28 shrink-0">
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold" style="color: {{ $rc['text'] }}">$</span>
                                                <input type="number"
                                                    wire:model.live.debounce.500ms="matrixRows.{{ $ri }}.amounts.{{ $currentDayStep }}"
                                                    min="0" step="1" placeholder="0"
                                                    class="w-full text-right text-sm font-mono font-bold pr-2 pl-6 py-2 rounded-[3px] border focus:ring-1 focus:outline-none bg-white dark:bg-gray-800"
                                                    style="border-color: {{ $rc['text'] }}40; color: {{ $rc['text'] }}; focus:border-color: {{ $rc['text'] }}">
                                            </div>
                                        </div>
                                        {{-- Remove row --}}
                                        <button type="button"
                                            wire:click="removeMatrixRow({{ $ri }})"
                                            wire:confirm="Remove this cost row from all days?"
                                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-[3px] text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                            title="{{ __('Remove row') }}">
                                            <x-icon name="x-mark" class="w-4 h-4" />
                                        </button>
                                    </div>
                                    @endif
                                @endforeach

                                {{-- Day subtotal --}}
                                @php
                                    $daySubtotal = 0;
                                    foreach ($matrixRows as $ri => $row) {
                                        $isChild = str_ends_with($row['type'] ?? '', '_child');
                                        $mult = ($row['is_per_pax'] ?? false) ? ($isChild ? $num_children : $num_adults) : 1;
                                        $daySubtotal += (float) ($row['amounts'][$currentDayStep] ?? 0) * $mult;
                                    }
                                @endphp
                                <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Day {{ $currentDayStep + 1 }} Total</span>
                                    <span class="font-mono font-bold text-lg text-emerald-600 dark:text-emerald-400">${{ number_format($daySubtotal, 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ── Day Navigation Footer ────────────────────── --}}
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-700">
                        <div>
                            @if($currentDayStep > 0)
                                <button type="button" wire:click="prevDayStep"
                                    class="flex items-center gap-1.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors px-3 py-2 rounded-[3px] hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <x-icon name="arrow-left" class="w-4 h-4" />{{ __('Prev Day') }}</button>
                            @else
                                <div></div>
                            @endif
                        </div>

                        <span class="text-xs text-gray-400">
                            {{ $currentDayStep + 1 }} / {{ $totalDays }}
                        </span>

                        <div>
                            @if($currentDayStep < $totalDays - 1)
                                <button type="button" wire:click="nextDayStep"
                                    class="flex items-center gap-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition-colors px-3 py-2 rounded-[3px] hover:bg-indigo-50 dark:hover:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800">{{ __('Next Day') }}<x-icon name="arrow-right" class="w-4 h-4" />
                                </button>
                            @else
                                <button type="button" wire:click="nextDayStep"
                                    class="flex items-center gap-1.5 text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 transition-colors px-3 py-2 rounded-[3px] hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-emerald-300 dark:border-emerald-700">
                                    <x-icon name="check-circle" class="w-4 h-4" />{{ __('Finish &amp; Review Matrix') }}</button>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- ══════════════ STEP 2 (wizard step 4): Matrix & Summary ══════════════ --}}
        @if($step === 4)
            <div class="space-y-6">


                {{-- ══════════════ PER-DAY ADD-ONS PANEL ══════════════ --}}
                {{-- (Removed to integrate all items directly into the Cost Matrix) --}}

                <div class="grid grid-cols-1 lg:grid-cols-1 gap-5">

                    {{-- Left Column: Breakdown & Group Pricing Charts --}}
                    <div class="lg:col-span-1 space-y-6">

                        {{-- ══════════════ GROUP PRICING CHART ══════════════ --}}
                        @php
                            $groupChart = $this->generateGroupPricingChart();
                            $maxTotal = collect($groupChart)->max('grand_total') ?: 1;
                            $minPerPerson = collect($groupChart)->min('per_person') ?: 0;
                            $maxPerPerson = collect($groupChart)->max('per_person') ?: 1;
                        @endphp

                        <div class="border border-indigo-100 dark:border-indigo-800/50 rounded-[3px] overflow-hidden shadow-sm">
                            {{-- Chart Header --}}
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-white/20 rounded-[3px] flex items-center justify-center">
                                        <x-icon name="users" class="w-4 h-4 text-white" />
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-white">{{ __('Group Pricing Chart') }}</h5>
                                        <p class="text-xs text-indigo-200">{{ __('How the total cost scales from 1 to 7 Pax') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-indigo-200">{{ __('Best per Pax rate') }}</p>
                                    <p class="text-sm font-bold text-white font-mono">$ {{ number_format($minPerPerson, 2) }} / Pax</p>
                                </div>
                            </div>

                            {{-- Chart Body --}}
                            <div class="bg-white dark:bg-gray-900 p-5">

                                {{-- Bar Chart --}}
                                <div class="mb-4">
                                    <div class="grid grid-cols-7 gap-2 items-end h-28">
                                        @foreach($groupChart as $row)
                                            @php
                                                $barHeight = $maxTotal > 0 ? round(($row['grand_total'] / $maxTotal) * 100) : 0;
                                                $isCurrent = $row['is_current'];
                                            @endphp
                                            <div class="flex flex-col items-center gap-1 h-full justify-end">
                                                <span class="text-xs font-bold font-mono {{ $isCurrent ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}">
                                                    ${{ number_format($row['grand_total'] / 1000, 1) }}k
                                                </span>
                                                <div class="w-full rounded-t-md transition-all relative"
                                                    style="height: {{ max(4, $barHeight) }}%; {{ $isCurrent ? 'background: linear-gradient(to top, #4f46e5, #7c3aed);' : 'background-color: rgb(199 210 254 / 0.7);' }}">
                                                    @if($isCurrent)
                                                        <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-indigo-600 rounded-full"></div>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-semibold {{ $isCurrent ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500' }}">
                                                    {{ $row['adults'] }}A
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center justify-center gap-4 mt-3">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-3 h-3 rounded-[3px] bg-indigo-200 dark:bg-indigo-800"></div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Other group sizes') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-3 h-3 rounded-[3px] bg-gradient-to-t from-indigo-600 to-purple-600"></div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Current group ({{ $num_adults }} adults)</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Detailed Pricing Table (Reversed Layout) --}}
                                <div class="overflow-x-auto pb-4">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-gray-100 dark:bg-gray-800/80 border-y border-gray-300 dark:border-gray-700">
                                                @for($pax = 7; $pax >= 1; $pax--)
                                                    <th class="py-3 px-2 text-center font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700/50">
                                                        {{ $pax }} Pax
                                                    </th>
                                                @endfor
                                                <th class="py-3 px-4 text-left font-bold text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700">{{ __('Cost Fee') }}</th>
                                                @foreach($matrixDays as $di => $day)
                                                    <th class="py-2 px-2 text-center font-semibold text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700/50 min-w-[80px]">
                                                        @php 
                                                                                                            $labelStr = $day['label'] ?? '';
                                                            preg_match('/^(Day\s+\d+)(.*)$/i', $labelStr, $matches);
                                                            $topPart = $matches[1] ?? 'Day ' . ($di + 1);
                                                            $bottomPart = trim($matches[2] ?? $labelStr);
                                                            if (empty($matches)) {
                                                                $topPart = 'Day ' . ($di + 1);
                                                                $bottomPart = $labelStr;
                                                            }
                                                        @endphp
                                                        <div class="whitespace-nowrap">{{ $topPart }}</div>
                                                        @if($bottomPart)
                                                            <div class="text-[10px] font-normal mt-0.5 text-gray-500 dark:text-gray-400 whitespace-normal line-clamp-2">
                                                                {{ $bottomPart }}
                                                            </div>
                                                        @endif
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700/50">
                                            @foreach($matrixRows as $row)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                                    @for($pax = 7; $pax >= 1; $pax--)
                                                        @php
                                                            $rowGroupTotal = 0;
                                                            $type = $row['type'] ?? 'custom';
                                                            foreach ($matrixDays as $di => $day) {
                                                                $baseAmount = (float) ($row['amounts'][$di] ?? 0);
                                                                if ($row['is_per_pax']) {
                                                                    if (str_ends_with($type, '_child')) {
                                                                        $rowGroupTotal += ($baseAmount * $num_children);
                                                                    } else {
                                                                        $rowGroupTotal += ($baseAmount * $pax);
                                                                    }
                                                                } else {
                                                                    $rowGroupTotal += $baseAmount;
                                                                }
                                                            }
                                                        @endphp
                                                        <td class="py-2 px-2 text-center font-mono text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700/50 {{ $pax == $num_adults ? 'bg-indigo-50/50 dark:bg-indigo-900/20 font-bold text-indigo-700 dark:text-indigo-400' : '' }}">
                                                            {{ $rowGroupTotal > 0 ? number_format($rowGroupTotal, 0) : '0' }}
                                                        </td>
                                                    @endfor

                                                    <td class="py-2 px-4 text-left font-semibold text-gray-800 dark:text-gray-200 border-r border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/20">
                                                        {{ $row['label'] }}
                                                    </td>

                                                    @foreach($matrixDays as $di => $day)
                                                        @php
                                                            $baseAmount = (float) ($row['amounts'][$di] ?? 0);
                                                            $type = $row['type'] ?? 'custom';
                                                            $isChild = str_ends_with($type, '_child');
                                                            $mult = ($row['is_per_pax'] ?? false) ? ($isChild ? $num_children : $num_adults) : 1;
                                                            $dayTotal = $baseAmount * $mult;
                                                        @endphp
                                                        <td class="py-2 px-2 text-center font-mono text-gray-600 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700/50">
                                                            {{ $dayTotal > 0 ? number_format($dayTotal, 0) : '0' }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach

                                            <!-- Grand Totals -->
                                            <tr class="bg-gray-100 dark:bg-gray-800/80 border-t-2 border-gray-300 dark:border-gray-600">
                                                @for($pax = 7; $pax >= 1; $pax--)
                                                    <td class="py-3 px-2 text-center font-mono font-bold text-red-600 dark:text-red-400 border-r border-gray-200 dark:border-gray-700/50 {{ $pax == $num_adults ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300' : '' }}">
                                                        {{ number_format($groupChart[$pax]['grand_total'] ?? 0, 0) }}
                                                    </td>
                                                @endfor

                                                <td class="py-3 px-4 text-left font-bold text-red-600 dark:text-red-400 border-r border-gray-300 dark:border-gray-700 uppercase tracking-wider">{{ __('Total') }}</td>

                                                @foreach($matrixDays as $di => $day)
                                                    @php
                                                        $dayGroupTotal = 0;
                                                        foreach ($matrixRows as $row) {
                                                            $type = $row['type'] ?? 'custom';
                                                            $isChild = str_ends_with($type, '_child');
                                                            $mult = ($row['is_per_pax'] ?? false) ? ($isChild ? $num_children : $num_adults) : 1;
                                                            $dayGroupTotal += ((float) ($row['amounts'][$di] ?? 0)) * $mult;
                                                        }
                                                    @endphp
                                                    <td class="py-3 px-2 text-center font-mono font-bold text-red-600 dark:text-red-400 border-r border-gray-200 dark:border-gray-700/50">
                                                        {{ number_format($dayGroupTotal, 0) }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Info Boxes --}}
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="flex items-start gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-[3px] border border-emerald-100 dark:border-emerald-800/50">
                                        <x-icon name="lock-closed" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                                        <div>
                                            <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Car &amp; Crater Fees — Shared Costs</p>
                                            <p class="text-xs text-emerald-650 dark:text-emerald-500 mt-0.5">{{ __('Total:') }}<strong class="font-mono">$ {{ number_format($groupChart[1]['crater_total'] ?? 0, 2) }}</strong> combined (max 7 pax).
                                                Shared equally among all guests — bigger groups pay less per person.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2 p-3 bg-blue-50 dark:bg-blue-900/10 rounded-[3px] border border-blue-100 dark:border-blue-800/50">
                                        <x-icon name="information-circle" class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" />
                                        <div>
                                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-400">{{ __('Scaling Rules') }}</p>
                                            <p class="text-xs text-blue-650 dark:text-blue-500 mt-0.5">
                                                Shared: transport &amp; crater fees (divided by group size).
                                                Fixed: accommodation, concession fees.
                                                Variable: park fees, pass fees, meals, activities.
                                                {{ $num_children > 0 ? $num_children . ' child' . ($num_children !== 1 ? 'ren' : '') . ' included in guest count.' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Totals Panel & Status --}}
                    <div class="space-y-4">
                        {{-- ── Totals Panel ── --}}
                        <div class="bg-gray-900 dark:bg-gray-950 rounded-[3px] p-5 text-white space-y-3 shadow-md">
                            <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 font-sans">{{ __('Cost Summary') }}</h5>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">{{ __('Park & Gov Fees') }}</span>
                                <span class="font-mono">$ {{ number_format($subtotal_park_fees + $subtotal_concession_fees + $subtotal_crater_fees + $subtotal_pass_fees, 2) }}</span>
                            </div>
                            @if($subtotal_activities > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Activities') }}</span>
                                    <span class="font-mono">$ {{ number_format($subtotal_activities, 2) }}</span>
                                </div>
                            @endif
                            @if($subtotal_transport > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Transport') }}</span>
                                    <span class="font-mono">$ {{ number_format($subtotal_transport, 2) }}</span>
                                </div>
                            @endif
                            @if($subtotal_meals > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Meals') }}</span>
                                    <span class="font-mono">$ {{ number_format($subtotal_meals, 2) }}</span>
                                </div>
                            @endif
                            @if($subtotal_accommodation > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Accommodation') }}</span>
                                    <span class="font-mono">$ {{ number_format($subtotal_accommodation, 2) }}</span>
                                </div>
                            @endif
                            @if($subtotal_custom > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{ __('Other') }}</span>
                                    <span class="font-mono">$ {{ number_format($subtotal_custom, 2) }}</span>
                                </div>
                            @endif

                            <div class="border-t border-gray-700 pt-3 flex justify-between">
                                <span class="text-gray-300 font-medium">{{ __('Subtotal') }}</span>
                                <span class="font-mono font-bold">$ {{ number_format($subtotal, 2) }}</span>
                            </div>

                            {{-- Markup --}}
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Markup / Profit Margin (%)</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" wire:model.live="markup_percentage" step="0.5" min="0" max="100"
                                        class="bg-gray-800 border border-gray-650 text-white rounded-[3px] px-3 py-1.5 text-sm font-mono w-full focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span class="text-gray-450 text-sm font-bold">%</span>
                                </div>
                                @if($markup_amount > 0)
                                    <p class="text-xs text-emerald-400">+ $ {{ number_format($markup_amount, 2) }} markup</p>
                                @endif
                            </div>

                            <div class="border-t border-gray-600 pt-3 flex justify-between items-center">
                                <span class="text-white font-bold text-base">{{ __('Grand Total') }}</span>
                                <span class="font-mono font-bold text-2xl text-emerald-400">$ {{ number_format($grand_total, 2) }}</span>
                            </div>

                            @if($num_adults > 0)
                                <div class="pt-1 space-y-0.5 border-t border-gray-800 text-right">
                                    <p class="text-[10px] text-gray-500">Per adult: $ {{ number_format($num_adults > 0 ? $grand_total / $num_adults : 0, 2) }}</p>
                                    <p class="text-[10px] text-gray-500">Per person: $ {{ number_format(($num_adults + $num_children) > 0 ? $grand_total / ($num_adults + $num_children) : 0, 2) }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- ── Status Selector ── --}}
                        <div class="bg-card p-4 rounded-[3px] border border-gray-150 dark:border-gray-800 shadow-sm">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" wire:click="$set('status', 'draft')"
                                    class="flex items-center justify-center gap-2 p-2.5 rounded-[3px] border-2 transition-all text-sm
                                        {{ $status === 'draft'
            ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400'
            : 'border-gray-200 dark:border-gray-700 text-gray-500' }}">
                                    <x-icon name="pencil" class="w-4 h-4" />{{ __('Draft') }}</button>
                                <button type="button" wire:click="$set('status', 'finalized')"
                                    class="flex items-center justify-center gap-2 p-2.5 rounded-[3px] border-2 transition-all text-sm
                                        {{ $status === 'finalized'
            ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400'
            : 'border-gray-200 dark:border-gray-700 text-gray-500' }}">
                                    <x-icon name="check-badge" class="w-4 h-4" />{{ __('Finalized') }}</button>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        @endif

    </div>

    {{-- ─────────────────────────────────────── Controls/Footer ────────────────── --}}
    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/50 rounded-[3px] border border-gray-100 dark:border-gray-800">
        <div>
            @if($step > 1)
                <button type="button" wire:click="prevStep" class="btn-secondary flex items-center gap-1.5 text-xs font-semibold">
                    <x-icon name="arrow-left" class="w-4 h-4" />{{ __('Back') }}</button>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <button type="button" wire:click="cancel" class="btn-secondary text-xs font-semibold">{{ __('Cancel') }}</button>
            @if($step === 1)
                <button type="button" wire:click="nextStep" class="btn-primary flex items-center gap-1.5 text-xs font-semibold">{{ __('Next') }}<x-icon name="arrow-right" class="w-4 h-4" />
                </button>
            @elseif($step === 2)
                <button type="button" wire:click="nextStep" class="btn-secondary flex items-center gap-1.5 text-xs font-semibold">{{ __('Skip to Matrix') }}<x-icon name="arrow-right" class="w-4 h-4" />
                </button>
            @else
                <button type="button" wire:click="save" class="btn-primary flex items-center gap-1.5 text-xs font-semibold" wire:loading.attr="disabled">
                    <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                    <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                    <span wire:loading.remove>{{ $isEdit ? 'Update Cost Sheet' : 'Save Cost Sheet' }}</span>
                    <span wire:loading>{{ __('Processing...') }}</span>
                </button>
            @endif
        </div>
    </div>

</div>
