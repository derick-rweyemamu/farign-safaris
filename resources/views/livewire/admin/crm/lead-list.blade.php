<div class="space-y-4 animate-fade-in pb-20">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Leads Pipeline') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage your inquiries and potential clients') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('crm.kanban') }}" class="btn-secondary">
                <x-icon name="squares-2x2" class="w-4 h-4" />{{ __('Kanban View') }}</a>
            @can('create leads')
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Add Lead') }}</button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                </select>

                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search leads...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Lead Info') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Assigned To') }}</th>
                        <th>{{ __('Interested In') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr class="animate-fade-in hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $lead->name }}</span>
                                    @if($lead->company)<span class="text-xs text-gray-500">{{ $lead->company }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    @if($lead->email)<a href="mailto:{{ $lead->email }}"
                                            class="hover:text-indigo-600 flex items-center gap-1">
                                            <x-icon name="envelope" class="w-3 h-3" /> {{ $lead->email }}
                                        </a>
                                    @endif
                                    @if($lead->phone)<a href="tel:{{ $lead->phone }}" class="hover:text-indigo-600 flex items-center gap-1">
                                            <x-icon name="phone" class="w-3 h-3" /> {{ $lead->phone }}
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $lead->status->color() }}-100 text-{{ $lead->status->color() }}-800 dark:bg-{{ $lead->status->color() }}-900/30 dark:text-{{ $lead->status->color() }}-400">
                                    {{ $lead->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if($lead->assignee)<div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-bold">
                                            {{ substr($lead->assignee->name, 0, 2) }}
                                        </div>
                                        <span
                                            class="text-sm text-gray-700 dark:text-gray-300">{{ $lead->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">{{ __('Unassigned') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($lead->tour)<span class="text-sm text-gray-700 dark:text-gray-300">{{ $lead->tour->name }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">{{ __('General Inquiry') }}</span>
                                @endif
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    <a href="{{ route('crm.detail', $lead->id) }}"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                        title="{{ __('View Details') }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    @can('edit leads')
                                    <button wire:click="edit({{ $lead->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete leads')
                                    <button wire:click="confirmDelete({{ $lead->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Delete') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <x-icon name="users"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No leads found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $leads->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Lead' : 'Add New Lead' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden min-h-0">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1 min-h-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name
                                    *</label>
                                <input type="text" wire:model="name" class="form-input">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Company') }}</label>
                                <input type="text" wire:model="company" class="form-input">
                                @error('company') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Email') }}</label>
                                <input type="email" wire:model="email" class="form-input">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Phone') }}</label>
                                <input type="text" wire:model="phone" class="form-input">
                                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Pipeline Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status
                                    *</label>
                                <select wire:model="status" class="form-input">
                                    @foreach($statuses as $st)
                                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Source') }}</label>
                                <input type="text" wire:model="source" class="form-input"
                                    placeholder="{{ __('e.g. Website, Referral') }}">
                                @error('source') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Estimated
                                    Budget') }}</label>
                                <input type="number" step="0.01" wire:model="estimated_budget" class="form-input">
                                @error('estimated_budget') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Est. Travel
                                    Date') }}</label>
                                <input type="date" wire:model="travel_date" class="form-input">
                                @error('travel_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Relations -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Assign
                                    To') }}</label>
                                <select wire:model="assigned_to" class="form-input">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_to') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Interested
                                    Tour') }}</label>
                                <select wire:model="tour_id" class="form-input">
                                    <option value="">{{ __('General Inquiry') }}</option>
                                    @foreach($tours as $tour)
                                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                                @error('tour_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary">
                            <span wire:loading.remove>{{ $isEdit ? 'Update Lead' : 'Create Lead' }}</span>
                            <span wire:loading>{{ __('Processing...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Lead?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this lead? This cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteLead" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
