<div class="space-y-6 animate-fade-in pb-20">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('crm.leads') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">{{ __('Leads Pipeline') }}</a>
                <x-icon name="chevron-right" class="w-3 h-3 text-gray-400" />
                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ __('Lead Details') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lead->name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $lead->status->color() }}-100 text-{{ $lead->status->color() }}-800 dark:bg-{{ $lead->status->color() }}-900/30 dark:text-{{ $lead->status->color() }}-400">
                    {{ $lead->status->label() }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(!$lead->client_id)
                @can('change lead status')
                <button wire:click="convertToClient" wire:confirm="Are you sure you want to convert this lead to a Client?" class="btn-primary bg-green-600 hover:bg-green-700 focus:ring-green-500 border-transparent text-white">
                    <x-icon name="user-plus" class="w-4 h-4" />{{ __('Convert to Client') }}</button>
                @endcan
            @else
                <a href="{{ route('clients.index') }}" class="btn-secondary text-green-600 dark:text-green-400 border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/20">
                    <x-icon name="check-circle" class="w-4 h-4" />{{ __('Converted (View Client)') }}</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Details -->
        <div class="space-y-6">
            <!-- Info Card -->
            <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-800 pb-2">{{ __('Lead Information') }}</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Company / Organization') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $lead->company ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Email Address') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            @if($lead->email)<a href="mailto:{{ $lead->email }}" class="text-indigo-600 hover:text-indigo-800">{{ $lead->email }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Phone Number') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            @if($lead->phone)<a href="tel:{{ $lead->phone }}" class="text-indigo-600 hover:text-indigo-800">{{ $lead->phone }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Source') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $lead->source ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Assigned To') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            @if($lead->assignee)<div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center justify-center text-[10px] font-bold">
                                        {{ substr($lead->assignee->name, 0, 2) }}
                                    </div>
                                    <span>{{ $lead->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 italic">{{ __('Unassigned') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Trip Interest -->
            <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-800 pb-2">{{ __('Trip Interest') }}</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Interested Tour') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            @if($lead->tour)<a href="{{ route('admin.tours.index') }}" class="text-indigo-600 hover:underline flex items-center gap-1">
                                    <x-icon name="map" class="w-4 h-4" /> {{ $lead->tour->name }}
                                </a>
                            @else
                                <span class="text-gray-400">{{ __('General Inquiry') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Est. Travel Date') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $lead->travel_date ? $lead->travel_date->format('F d, Y') : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Estimated Budget') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $lead->estimated_budget ? '$' . number_format($lead->estimated_budget, 2) : '—' }}
                        </dd>
                    </div>
                    @if($lead->notes)<div class="col-span-full mt-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <dt class="text-gray-500 dark:text-gray-400 mb-2">{{ __('Request Details') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white whitespace-pre-line text-sm bg-gray-50 dark:bg-gray-800/50 p-3 rounded-[3px] border border-gray-100 dark:border-gray-800/50 leading-relaxed">
                            {{ $lead->notes }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Right Column: Timeline & Notes -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Pipeline Progress -->
            <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-5">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ __('Pipeline Status') }}</h3>
                
                <div class="relative">
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-100 dark:bg-gray-800">
                        @php
                            $currentIndex = array_search($lead->status, $statuses);
                            $total = count($statuses) - 1; // excluding 'lost' from linear progression optionally
                            // Let's just calculate a rough percentage
                            $percent = $lead->status === App\Enums\LeadStatus::LOST ? 100 : ($currentIndex / ($total - 1)) * 100;
                            $barColor = $lead->status === App\Enums\LeadStatus::LOST ? 'bg-red-500' : 'bg-indigo-500';
                        @endphp
                        <div style="width: {{ $percent }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $barColor }} transition-all duration-500"></div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mt-2">
                        @foreach($statuses as $st)
                            @if($st !== App\Enums\LeadStatus::LOST)
                                @php
                                    $isActive = $lead->status === $st;
                                    $color = $st->color();
                                    
                                    // Set dynamic classes based on status and active state
                                    $btnClasses = match($color) {
                                        'blue' => $isActive 
                                            ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:border-blue-500 dark:hover:bg-blue-600' 
                                            : 'bg-white hover:bg-blue-50/50 text-gray-700 border-gray-200 hover:border-blue-300 dark:bg-gray-800 dark:hover:bg-blue-950/20 dark:text-gray-300 dark:border-gray-700 dark:hover:border-blue-800',
                                        'amber' => $isActive 
                                            ? 'bg-amber-600 text-white border-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:border-amber-500 dark:hover:bg-amber-600' 
                                            : 'bg-white hover:bg-amber-50/50 text-gray-700 border-gray-200 hover:border-amber-300 dark:bg-gray-800 dark:hover:bg-amber-950/20 dark:text-gray-300 dark:border-gray-700 dark:hover:border-amber-800',
                                        'purple' => $isActive 
                                            ? 'bg-purple-600 text-white border-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:border-purple-500 dark:hover:bg-purple-600' 
                                            : 'bg-white hover:bg-purple-50/50 text-gray-700 border-gray-200 hover:border-purple-300 dark:bg-gray-800 dark:hover:bg-purple-950/20 dark:text-gray-300 dark:border-gray-700 dark:hover:border-purple-800',
                                        'indigo' => $isActive 
                                            ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:border-indigo-500 dark:hover:bg-indigo-600' 
                                            : 'bg-white hover:bg-indigo-50/50 text-gray-700 border-gray-200 hover:border-indigo-300 dark:bg-gray-800 dark:hover:bg-indigo-950/20 dark:text-gray-300 dark:border-gray-700 dark:hover:border-indigo-800',
                                        'green' => $isActive 
                                            ? 'bg-green-600 text-white border-green-600 hover:bg-green-700 dark:bg-green-500 dark:border-green-500 dark:hover:bg-green-600' 
                                            : 'bg-white hover:bg-green-50/50 text-gray-700 border-gray-200 hover:border-green-300 dark:bg-gray-800 dark:hover:bg-green-950/20 dark:text-gray-300 dark:border-gray-700 dark:hover:border-green-800',
                                        default => $isActive 
                                            ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' 
                                            : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50',
                                    };
                                    
                                    $dotColor = match($color) {
                                        'blue' => 'bg-blue-500',
                                        'amber' => 'bg-amber-500',
                                        'purple' => 'bg-purple-500',
                                        'indigo' => 'bg-indigo-500',
                                        'green' => 'bg-green-500',
                                        default => 'bg-indigo-500',
                                    };
                                @endphp
                                @can('change lead status')
                                <button wire:click="updateStatus('{{ $st->value }}')" 
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-[3px] border transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $btnClasses }}">
                                    <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-white animate-pulse' : $dotColor }}"></span>
                                    {{ $st->label() }}
                                </button>
                                @else
                                <div class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-[3px] border transition-all duration-200 shadow-sm {{ $btnClasses }} opacity-75 cursor-default">
                                    <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-white' : $dotColor }}"></span>
                                    {{ $st->label() }}
                                </div>
                                @endcan
                            @endif
                        @endforeach
                    </div>
                    @if($lead->status !== App\Enums\LeadStatus::LOST)
                        @can('change lead status')
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                            <button wire:click="updateStatus('lost')" class="group px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:text-white bg-white hover:bg-red-600 dark:bg-gray-800 dark:hover:bg-red-600 border border-red-200 dark:border-red-900/50 hover:border-red-600 dark:hover:border-red-600 rounded-[3px] transition-all duration-200 shadow-sm flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 group-hover:bg-white transition-colors duration-200"></span>{{ __('Mark as Lost') }}</button>
                        </div>
                        @endcan
                    @endif
                </div>
            </div>

            <!-- Activity Composer -->
            @can('log lead activities')
            <div class="bg-white dark:bg-gray-900 rounded-[3px] shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="flex border-b border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('noteType', 'note')" class="flex-1 py-3 text-sm font-medium border-b-2 {{ $noteType === 'note' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">{{ __('Note') }}</button>
                    <button wire:click="$set('noteType', 'call')" class="flex-1 py-3 text-sm font-medium border-b-2 {{ $noteType === 'call' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">{{ __('Log Call') }}</button>
                    <button wire:click="$set('noteType', 'email')" class="flex-1 py-3 text-sm font-medium border-b-2 {{ $noteType === 'email' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">{{ __('Log Email') }}</button>
                    <button wire:click="$set('noteType', 'meeting')" class="flex-1 py-3 text-sm font-medium border-b-2 {{ $noteType === 'meeting' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">{{ __('Meeting') }}</button>
                </div>
                <div class="p-4 bg-gray-50/50 dark:bg-gray-800/20">
                    <x-admin.rich-text wire:model="noteContent" id="note-composer" />
                    @error('noteContent') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <div class="mt-3 flex justify-end">
                        <button wire:click="addNote" class="btn-primary text-sm py-1.5 px-4" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="addNote">{{ __('Save Activity') }}</span>
                            <span wire:loading wire:target="addNote">{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            @endcan

            <!-- Activity Timeline -->
            <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-6">{{ __('Activity Timeline') }}</h3>
                
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @forelse($lead->activities()->latest()->get() as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)<span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-900 
                                                @if($activity->type === 'note') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                                @elseif($activity->type === 'call') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($activity->type === 'email') bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400
                                                @elseif($activity->type === 'meeting') bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                                                @else bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400
                                                @endif">
                                                
                                                @if($activity->type === 'note')
                                                    <x-icon name="document-text" class="w-4 h-4" />
                                                @elseif($activity->type === 'call')
                                                    <x-icon name="phone" class="w-4 h-4" />
                                                @elseif($activity->type === 'email')
                                                    <x-icon name="envelope" class="w-4 h-4" />
                                                @elseif($activity->type === 'meeting')
                                                    <x-icon name="users" class="w-4 h-4" />
                                                @else
                                                    <x-icon name="arrow-path" class="w-4 h-4" />
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 flex justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                                    @if($activity->type === 'status_change')
                                                        {{ $activity->content }} <span class="font-medium text-gray-900 dark:text-white">by {{ $activity->user->name ?? 'System' }}</span>
                                                    @else
                                                        <span class="font-medium text-gray-900 dark:text-white">{{ $activity->user->name ?? 'User' }}</span> logged a {{ $activity->type }}
                                                    @endif
                                                </p>
                                                @if($activity->type !== 'status_change')
                                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-[3px] prose dark:prose-invert max-w-none">{!! $activity->content !!}</div>
                                                @endif
                                            </div>
                                            <div class="text-right text-xs whitespace-nowrap text-gray-500 dark:text-gray-400 flex flex-col items-end gap-1">
                                                <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time>
                                                @if($activity->type !== 'status_change')
                                                    @can('delete lead activities')
                                                    <button wire:click="deleteActivity({{ $activity->id }})" wire:confirm="Are you sure you want to delete this activity?" class="text-red-500 hover:text-red-700 transition-colors p-1" title="{{ __('Delete Activity') }}">
                                                        <x-icon name="trash" class="w-3 h-3" />
                                                    </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <div class="text-center py-8">
                                <x-icon name="clock" class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No activities logged yet.') }}</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
            
        </div>
    </div>
</div>

