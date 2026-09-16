<div class="space-y-4 animate-fade-in pb-20 flex flex-col h-screen max-h-screen">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 flex-shrink-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Leads Kanban') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Drag and drop leads between stages') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('crm.leads') }}" class="btn-secondary">
                <x-icon name="list-bullet" class="w-4 h-4" />{{ __('List View') }}</a>
            @can('create leads')
            <a href="{{ route('crm.leads') }}" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Lead') }}</a>
            @endcan
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 overflow-x-auto overflow-y-hidden" x-data="kanbanBoard()">
        <div class="flex gap-4 h-full pb-6" style="min-width: max-content;">
            
            @foreach($statuses as $status)
                @php
                    $columnLeads = $leadsByStatus->get($status->value, collect());
                @endphp
                
                <div class="flex flex-col bg-gray-50 dark:bg-gray-800/40 rounded-[3px] border border-gray-200 dark:border-gray-800 w-80 max-h-full flex-shrink-0">
                    <!-- Column Header -->
                    <div class="p-3 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-gray-900/50 rounded-t-lg flex-shrink-0">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-{{ $status->color() }}-500 shadow-sm"></div>
                            <h3 class="font-bold text-gray-800 dark:text-gray-200">{{ $status->label() }}</h3>
                        </div>
                        <span class="text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full">
                            {{ $columnLeads->count() }}
                        </span>
                    </div>

                    <!-- Column Body / Sortable Area -->
                    <div class="flex-1 p-2 overflow-y-auto kanban-column min-h-[200px]" 
                         data-status="{{ $status->value }}">
                        
                        @foreach($columnLeads as $lead)
                            <div class="kanban-item bg-card p-3 rounded-[3px] shadow-sm border border-gray-200 dark:border-gray-700 mb-2 cursor-grab active:cursor-grabbing hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors"
                                 data-id="{{ $lead->id }}">
                                
                                <div class="flex justify-between items-start mb-2">
                                    <a href="{{ route('crm.detail', $lead->id) }}" class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600 transition-colors text-sm">
                                        {{ $lead->name }}
                                    </a>
                                </div>
                                
                                @if($lead->company)<p class="text-xs text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1">
                                        <x-icon name="building-office" class="w-3 h-3" />
                                        {{ $lead->company }}
                                    </p>
                                @endif
                                
                                @if($lead->tour)<p class="text-xs text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded inline-block mb-3">
                                        {{ $lead->tour->name }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-800">
                                    <div class="text-xs font-medium text-gray-500">
                                        {{ $lead->estimated_budget ? '$' . number_format($lead->estimated_budget, 2) : '—' }}
                                    </div>
                                    @if($lead->assignee)<div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 flex items-center justify-center text-[10px] font-bold" title="{{ $lead->assignee->name }}">
                                            {{ substr($lead->assignee->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    <!-- Load SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    @script
    <script>
        Alpine.data('kanbanBoard', () => ({
            init() {
                const columns = document.querySelectorAll('.kanban-column');
                columns.forEach(col => {
                    new Sortable(col, {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        dragClass: 'cursor-grabbing',
                        disabled: {{ auth()->user()->can('change lead status') ? 'false' : 'true' }},
                        onEnd: (evt) => {
                            if (evt.to !== evt.from) {
                                const leadId = evt.item.dataset.id;
                                const newStatus = evt.to.dataset.status;
                                
                                // Call livewire component method to update status
                                $wire.updateStatus(leadId, newStatus);
                            }
                        }
                    });
                });
            }
        }));
    </script>
    @endscript
</div>

