<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Newsletter Management') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage subscribers and send broadcast campaigns') }}</p>
        </div>
        @if($activeTab === 'subscribers')
            <div class="flex items-center gap-3">
                @canany(['create newsletters', 'edit newsletters'])
                    <button wire:click="$set('showImportModal', true)" class="btn-secondary">
                        <x-icon name="arrow-up-tray" class="w-4 h-4" />{{ __('Import CSV/Excel') }}
                    </button>
                    <button wire:click="createSubscriber" class="btn-primary">
                        <x-icon name="plus" class="w-4 h-4" />{{ __('Add Subscriber') }}
                    </button>
                @endcanany
            </div>
        @endif
    </div>

    <!-- Tabs Navigation -->
    <div class="flex flex-wrap items-center gap-1 bg-gray-100 dark:bg-gray-800/50 p-1 rounded-[3px] w-fit mb-6">
        <button wire:click="switchTab('subscribers')"
            class="px-4 py-2 text-sm font-medium rounded-[3px] transition-all {{ $activeTab === 'subscribers' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">{{ __('Subscribers') }}</button>
        <button wire:click="switchTab('broadcast')"
            class="px-4 py-2 text-sm font-medium rounded-[3px] transition-all {{ $activeTab === 'broadcast' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">{{ __('New Broadcast') }}</button>
        <button wire:click="switchTab('history')"
            class="px-4 py-2 text-sm font-medium rounded-[3px] transition-all {{ $activeTab === 'history' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">{{ __('Campaign History') }}</button>
    </div>

    <!-- Main Content -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">


        @if($activeTab === 'subscribers')
            <!-- Subscriber Filters and Stats -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 space-y-4 bg-gray-50/50 dark:bg-gray-800/30">
                
                <!-- Stats (Top) -->
                <div class="flex gap-4 items-center overflow-x-auto pb-2 sm:pb-0">
                    <div class="text-sm whitespace-nowrap">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Total:') }}</span> <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</span>
                    </div>
                    <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="text-sm whitespace-nowrap">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Active:') }}</span> <span class="text-green-600 dark:text-green-400 font-semibold">{{ $stats['active'] }}</span>
                    </div>
                    <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="text-sm whitespace-nowrap">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Unsubscribed:') }}</span> <span class="text-red-600 dark:text-red-400 font-semibold">{{ $stats['unsubscribed'] }}</span>
                    </div>
                </div>

                <!-- Filters & Search (Bottom) -->
                <div class="flex flex-col sm:flex-row gap-3 w-full justify-between items-start sm:items-center">
                    
                    <!-- Left Group (Per Page + Status) -->
                    <div class="flex gap-3 w-full sm:w-auto">
                        <!-- Per Page -->
                        <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-full sm:w-auto sm:min-w-[100px]">
                            <option value="10">10 / Page</option>
                            <option value="20">20 / Page</option>
                            <option value="50">50 / Page</option>
                            <option value="100">100 / Page</option>
                        </select>
                        
                        <!-- Filter -->
                        <select wire:model.live="subscriberStatusFilter" class="form-input text-xs py-1.5 px-2 w-full sm:w-auto sm:min-w-[120px]">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="unsubscribed">{{ __('Unsubscribed') }}</option>
                        </select>
                    </div>

                    <!-- Search (Right) -->
                    <div class="relative w-full sm:w-auto shrink-0">
                        <input type="text" wire:model.live.debounce.300ms="subscriberSearch" placeholder="{{ __('Search by...') }}"
                            class="form-input text-xs py-1.5 !pl-8 w-full sm:w-56 md:w-64">
                        <x-icon name="magnifying-glass"
                            class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Subscribed At') }}</th>
                            @canany(['edit newsletters', 'delete newsletters'])
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $subscriber->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $subscriber->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400' }}">
                                        {{ ucfirst($subscriber->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $subscriber->created_at->format('M d, Y H:i') }}</td>
                                @canany(['edit newsletters', 'delete newsletters'])
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <x-admin.action-dropdown>
                                        @can('edit newsletters')
                                        <button wire:click="editSubscriber({{ $subscriber->id }})" title="{{ __('Edit') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button wire:click="toggleSubscriberStatus({{ $subscriber->id }})" title="{{ $subscriber->status === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                            @if($subscriber->status === 'active')
                                                <x-icon name="x-circle" class="w-4 h-4" />
                                            @else
                                                <x-icon name="check-circle" class="w-4 h-4" />
                                            @endif
                                        </button>
                                        @endcan
                                        @can('delete newsletters')
                                        <button wire:click="deleteSubscriber({{ $subscriber->id }})" wire:confirm="Are you sure you want to delete this subscriber?" title="{{ __('Delete') }}">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                        @endcan
                                    </x-admin.action-dropdown>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">{{ __('No subscribers yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-800">
                    {{ $subscribers->links() }}
                </div>
            </div>
        @elseif($activeTab === 'broadcast')
            <div class="p-6">
                @if($editingCampaignId)
                    <div class="mb-4 p-3 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded flex items-center justify-between">
                        <span class="text-sm text-indigo-700 dark:text-indigo-300 font-medium">{{ __('You are currently editing a draft campaign.') }}</span>
                        <button type="button" wire:click="$set('editingCampaignId', null); $set('subject', ''); $set('body', '');" class="text-xs text-indigo-600 dark:text-indigo-400 underline hover:text-indigo-800">{{ __('Cancel / Clear') }}</button>
                    </div>
                @endif
                <form wire:submit="sendBroadcast">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Subject') }}</label>
                        <input type="text" wire:model="subject" class="form-input" required placeholder="{{ __('Newsletter Subject') }}">
                        @error('subject') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <x-admin.rich-text wire:model="body" label="{{ __('Body') }}" height="400" />
                        @error('body') <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="saveDraft" class="btn-secondary">
                            <span wire:loading.remove wire:target="saveDraft">{{ __('Save as Draft') }}</span>
                            <span wire:loading wire:target="saveDraft">{{ __('Saving...') }}</span>
                        </button>
                        <button type="submit" class="btn-primary">
                            <span wire:loading.remove wire:target="sendBroadcast">{{ __('Send Broadcast') }}</span>
                            <span wire:loading wire:target="sendBroadcast">{{ __('Queueing...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        @elseif($activeTab === 'history')
            <div>
                <!-- Filters -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-3 justify-between items-center bg-gray-50/50 dark:bg-gray-800/30">
                    <div class="w-full sm:w-72 relative">
                        <input type="text" wire:model.live.debounce.300ms="campaignSearch" placeholder="{{ __('Search by subject...') }}"
                            class="form-input text-xs py-1.5 !pl-8 w-full">
                        <x-icon name="magnifying-glass"
                            class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                    </div>
                    <select wire:model.live="campaignStatusFilter" class="form-input text-xs py-1.5 px-2 w-full sm:w-40">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="sent">{{ __('Sent') }}</option>
                        <option value="sending">{{ __('Sending') }}</option>
                        <option value="draft">{{ __('Draft') }}</option>
                    </select>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Subject') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Recipients') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Sent At') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse($campaigns as $campaign)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $campaign->subject }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $campaign->status === 'sent' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400' : ($campaign->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400') }}">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $campaign->recipients_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $campaign->sent_at ? $campaign->sent_at->format('M d, Y H:i') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-admin.action-dropdown>
                                            <button wire:click="viewCampaign({{ $campaign->id }})" title="{{ __('Preview Campaign') }}">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </button>
                                            @if($campaign->status === 'draft')
                                                @can('edit newsletters')
                                                <button wire:click="editCampaign({{ $campaign->id }})" title="{{ __('Edit Draft') }}">
                                                    <x-icon name="pencil-square" class="w-4 h-4" />
                                                </button>
                                                @endcan
                                            @else
                                                @can('create newsletters')
                                                <button wire:click="duplicateCampaign({{ $campaign->id }})" title="{{ __('Duplicate Campaign') }}">
                                                    <x-icon name="document-duplicate" class="w-4 h-4" />
                                                </button>
                                                <button wire:click="confirmResend({{ $campaign->id }})" title="{{ __('Resend Campaign') }}">
                                                    <x-icon name="paper-airplane" class="w-4 h-4" />
                                                </button>
                                                @endcan
                                            @endif
                                            @can('delete newsletters')
                                            <button wire:click="deleteCampaign({{ $campaign->id }})" wire:confirm="Are you sure you want to delete this campaign?" title="{{ __('Delete') }}">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                            @endcan
                                        </x-admin.action-dropdown>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">{{ __('No campaigns found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-800">
                    {{ $campaigns->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Subscriber Modal -->
    @if($showSubscriberModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showSubscriberModal', false)">
            <div class="modal-box max-w-lg flex flex-col overflow-hidden">
                <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEditSubscriber ? 'Edit Subscriber' : 'Add New Subscriber' }}
                    </h3>
                    <button wire:click="$set('showSubscriberModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="saveSubscriber" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address *</label>
                            <input type="email" wire:model="subscriberEmail" class="form-input" required
                                placeholder="e.g. subscriber@example.com">
                            @error('subscriberEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status *</label>
                            <select wire:model="subscriberStatus" class="form-input">
                                <option value="active">{{ __('Active') }}</option>
                                <option value="unsubscribed">{{ __('Unsubscribed') }}</option>
                            </select>
                            @error('subscriberStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showSubscriberModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveSubscriber" />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveSubscriber" />
                            <span wire:loading.remove wire:target="saveSubscriber">{{ $isEditSubscriber ? 'Update Subscriber' : 'Add Subscriber' }}</span>
                            <span wire:loading wire:target="saveSubscriber">{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Campaign Preview Modal -->
    @if($showCampaignModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showCampaignModal', false)">
            <div class="modal-box max-w-2xl flex flex-col overflow-hidden">
                <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $previewSubject }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Status:') }}<span class="capitalize font-medium">{{ $previewStatus }}</span> |
                            Recipients: <span class="font-medium">{{ $previewRecipients }}</span> |
                            Sent: <span class="font-medium">{{ $previewSentAt }}</span>
                        </p>
                    </div>
                    <button wire:click="$set('showCampaignModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 overflow-y-auto flex-1 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 prose dark:prose-invert max-w-none">
                    {!! $previewBody !!}
                </div>

                <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="$set('showCampaignModal', false)" class="btn-secondary">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    @endif
    <!-- Resend Confirmation Modal -->
    @if($showResendModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showResendModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="paper-airplane" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Resend Campaign?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to resend this exact campaign to all active subscribers?') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showResendModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="resendCampaign" class="btn-primary" wire:loading.attr="disabled">{{ __('Resend') }}</button>
                </div>
            </div>
        </div>
    @endif
    <!-- Import Modal -->
    @if($showImportModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showImportModal', false)">
            <div class="modal-box max-w-lg flex flex-col overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Import Subscribers') }}</h3>
                    <button wire:click="$set('showImportModal', false)"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>
                <form wire:submit.prevent="importSubscribers">
                    <div class="p-6 space-y-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Upload an Excel or CSV file. The system will automatically search each row for a valid email address and import it.') }}
                        </p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('File (CSV, Excel)') }}</label>
                            <input type="file" id="importFile" wire:model="importFile" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            @error('importFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            
                            <div wire:loading wire:target="importFile" class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">
                                {{ __('Uploading...') }}
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('showImportModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="importSubscribers">{{ __('Import') }}</span>
                            <span wire:loading wire:target="importSubscribers">{{ __('Importing...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
