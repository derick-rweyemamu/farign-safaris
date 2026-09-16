<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Email Configuration') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage server settings and email templates') }}</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800/50 p-1 rounded-[3px] w-fit">
        <button wire:click="$set('activeTab', 'smtp')"
            class="px-4 py-2 text-sm font-medium rounded-[3px] transition-all {{ $activeTab === 'smtp' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">{{ __('SMTP Configuration') }}</button>
        <button wire:click="$set('activeTab', 'templates')"
            class="px-4 py-2 text-sm font-medium rounded-[3px] transition-all {{ $activeTab === 'templates' ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">{{ __('Email Templates') }}</button>
    </div>

    <!-- Main Content -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800">
        <form wire:submit.prevent="save">

            @if($activeTab === 'smtp')
                <!-- SMTP Settings -->
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                                <x-icon name="envelope" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-sm">{{ __('SMTP
                                    Server Settings') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Configure your mail server details') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Mail Driver
                                    / Transport') }}</label>
                                <select wire:model="mail_transport" class="form-input">
                                    <option value="smtp">{{ __('SMTP') }}</option>
                                    <option value="mailgun">{{ __('Mailgun') }}</option>
                                    <option value="postmark">{{ __('Postmark') }}</option>
                                    <option value="sendmail">{{ __('Sendmail') }}</option>
                                </select>
                                @error('mail_transport') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Mail
                                    Host') }}</label>
                                <input type="text" wire:model="mail_host" class="form-input" placeholder="{{ __('smtp.mailtrap.io') }}">
                                @error('mail_host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Mail
                                    Port') }}</label>
                                <input type="number" wire:model="mail_port" class="form-input" placeholder="587">
                                @error('mail_port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Encryption') }}</label>
                                <select wire:model="mail_encryption" class="form-input">
                                    <option value="">{{ __('None') }}</option>
                                    <option value="tls">{{ __('TLS') }}</option>
                                    <option value="ssl">{{ __('SSL') }}</option>
                                </select>
                                @error('mail_encryption') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Username') }}</label>
                                <input type="text" wire:model="mail_username" class="form-input"
                                    placeholder="{{ __('SMTP Username') }}">
                                @error('mail_username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Password') }}</label>
                                <input type="password" wire:model="mail_password" class="form-input"
                                    placeholder="{{ __('SMTP Password') }}">
                                @error('mail_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sender Settings -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                                <x-icon name="user-circle" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-sm">{{ __('Sender
                                    Information') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Configure how emails appear to
                                    recipients') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('From
                                    Name') }}</label>
                                <input type="text" wire:model="mail_from_name" class="form-input"
                                    placeholder="{{ __('My ERP System') }}">
                                @error('mail_from_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('From
                                    Address') }}</label>
                                <input type="email" wire:model="mail_from_address" class="form-input"
                                    placeholder="noreply@example.com">
                                @error('mail_from_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'templates')
                <!-- Email Templates -->
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                            <x-icon name="document-text" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-sm">{{ __('Email
                                Templates') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Customize the subject and body of emails
                                sent to clients') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Estimate Template -->
                        <div class="space-y-4">
                            <h4
                                class="text-sm font-bold text-gray-700 dark:text-gray-300 border-l-2 border-indigo-500 pl-3">{{ __('Estimation Template') }}</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Estimate
                                        Email Subject') }}</label>
                                    <input type="text" wire:model="estimate_subject" class="form-input">
                                    @error('estimate_subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Estimate
                                        Email Body') }}</label>
                                    <textarea wire:model="estimate_body" rows="6" class="form-input"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Available placeholders: {client_name},
                                        {estimate_number}, {company_name}</p>
                                    @error('estimate_body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Template -->
                        <div class="space-y-4">
                            <h4
                                class="text-sm font-bold text-gray-700 dark:text-gray-300 border-l-2 border-indigo-500 pl-3">{{ __('Invoice Template') }}</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Invoice
                                        Email Subject') }}</label>
                                    <input type="text" wire:model="invoice_subject" class="form-input">
                                    @error('invoice_subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Invoice
                                        Email Body') }}</label>
                                    <textarea wire:model="invoice_body" rows="6" class="form-input"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Available placeholders: {client_name},
                                        {invoice_number}, {company_name}</p>
                                    @error('invoice_body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Itinerary Template -->
                        <div class="space-y-4 mt-8">
                            <h4
                                class="text-sm font-bold text-gray-700 dark:text-gray-300 border-l-2 border-indigo-500 pl-3">{{ __('Itinerary Template') }}</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Itinerary
                                        Email Subject') }}</label>
                                    <input type="text" wire:model="itinerary_subject" class="form-input">
                                    @error('itinerary_subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Itinerary
                                        Email Body') }}</label>
                                    <textarea wire:model="itinerary_body" rows="6" class="form-input"></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Available placeholders: {client_name},
                                        {itinerary_name}, {company_name}</p>
                                    @error('itinerary_body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Footer / Submit -->
            <div
                class="p-6 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                @if($activeTab === 'smtp')
                    <button type="button" wire:click="testConnection" class="btn-secondary px-8"
                        wire:loading.attr="disabled" wire:target="testConnection">
                        <x-icon name="paper-airplane" class="w-4 h-4" wire:loading.remove
                            wire:target="testConnection" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading
                            wire:target="testConnection" />
                        <span class="ml-2">{{ __('Test Configuration') }}</span>
                    </button>
                @endif

                <button type="submit" class="btn-primary px-8" wire:loading.attr="disabled" wire:target="save">
                    <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                    <span wire:loading.remove wire:target="save">{{ __('Save Changes') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Helper Info -->
    <div
        class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-[3px] p-4 flex gap-4">
        <x-icon name="information-circle" class="w-5 h-5 text-blue-500 flex-shrink-0" />
        <div class="text-sm text-blue-700 dark:text-blue-300">
            <p class="font-bold">{{ __('Pro Tip:') }}</p>
            <p class="mt-1">Changes to SMTP settings will take effect immediately and are synced with your system
                environment (.env) file. Template changes affect all future emails sent from the system.</p>
        </div>
    </div>
</div>
