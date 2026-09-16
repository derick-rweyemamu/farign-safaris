<div class="space-y-4 animate-fade-in w-full">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Company Settings') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage system configurations and company profiles') }}</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800/50 p-1 rounded-[3px] w-fit overflow-x-auto max-w-full">
        @foreach([
            ['key' => 'general', 'label' => 'General', 'icon' => 'building-office'],
            ['key' => 'media', 'label' => 'Logos & Media', 'icon' => 'photo'],
            ['key' => 'invoice', 'label' => 'Invoices', 'icon' => 'document-text'],
            ['key' => 'estimate', 'label' => 'Estimates', 'icon' => 'document-chart-bar'],
            ['key' => 'bank', 'label' => 'Bank Details', 'icon' => 'banknotes'],
            ['key' => 'financial', 'label' => 'Financial', 'icon' => 'receipt-percent'],
            ['key' => 'social', 'label' => 'Social Links', 'icon' => 'share'],
            ['key' => 'security', 'label' => 'Security', 'icon' => 'shield-check'],
            ['key' => 'meta_leads', 'label' => 'Meta Lead Ads', 'icon' => 'globe-alt']
        ] as $tab)
        <button wire:click="$set('activeTab', '{{ $tab['key'] }}')" 
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-[3px] transition-all whitespace-nowrap {{ $activeTab === $tab['key'] ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            <x-icon name="{{ $tab['icon'] }}" class="w-4 h-4" />
            {{ $tab['label'] }}
        </button>
        @endforeach
    </div>

    <!-- General Tab -->
    @if($activeTab === 'general')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-5">{{ __('Company Information') }}</h3>

            <form wire:submit="saveGeneral" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">App Name *</label>
                        <input type="text" wire:model="app_name" class="form-input">
                        @error('app_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company Name *</label>
                        <input type="text" wire:model="company_name" class="form-input">
                        @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address *</label>
                        <input type="email" wire:model="email" class="form-input">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone *</label>
                        <input type="text" wire:model="phone" class="form-input">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Website') }}</label>
                        <input type="text" wire:model="website" class="form-input">
                        @error('website') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('TIN Number') }}</label>
                        <input type="text" wire:model="tin" class="form-input">
                        @error('tin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('VRN Number') }}</label>
                        <input type="text" wire:model="vrn" class="form-input">
                        @error('vrn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <x-admin.rich-text wire:model="address" label="{{ __('Address *') }}" />
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    @can('edit general settings')
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveGeneral" />
                        <span wire:loading.remove wire:target="saveGeneral">{{ __('Save Changes') }}</span>
                        <span wire:loading wire:target="saveGeneral">{{ __('Saving...') }}</span>
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Logos & Media Tab -->
    @if($activeTab === 'media')
    <div class="space-y-6">
        <form wire:submit="saveGeneral" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 1. Brand Logos -->
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-icon name="photo" class="w-5 h-5 text-indigo-500" />
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Brand Logos & Icons') }}</h3>
                    </div>
                    
                    @php
                        $logoFields = [
                            ['label' => 'Main Logo', 'model' => 'new_company_logo', 'preview' => 'company_logo', 'desc' => 'Primary header logo'],
                            ['label' => 'Dark Mode Logo', 'model' => 'new_dark_logo', 'preview' => 'dark_logo', 'desc' => 'Displayed on dark backgrounds'],
                            ['label' => 'White / Light Logo', 'model' => 'new_white_logo', 'preview' => 'white_logo', 'desc' => 'Displayed on dark navigation bars'],
                            ['label' => 'Favicon', 'model' => 'new_favicon', 'preview' => 'favicon', 'desc' => 'Browser tab icon (32 × 32 px)'],
                        ];
                    @endphp

                    @foreach($logoFields as $field)
                    <div class="border-b border-gray-100 dark:border-gray-800 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $field['label'] }}</label>
                            <span class="text-[11px] text-gray-400">{{ $field['desc'] }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-[3px] flex items-center justify-center border border-gray-200 dark:border-gray-700 overflow-hidden shrink-0">
                                @if(${$field['model']} && method_exists(${$field['model']}, 'isPreviewable') && ${$field['model']}->isPreviewable())<img src="{{ ${$field['model']}->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                @elseif(${$field['model']})
                                    <div class="flex flex-col items-center justify-center h-full w-full bg-red-50 text-red-400">
                                        <x-icon name="exclamation-circle" class="w-5 h-5" />
                                    </div>
                                @elseif(${$field['preview']})
                                    <img src="{{ asset('storage/' . ${$field['preview']}) }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <x-icon name="photo" class="w-6 h-6 text-gray-400" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" wire:model="{{ $field['model'] }}" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-[3px] file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400" accept="image/*">
                                @error($field['model']) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- 2. Display & Content Images -->
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-icon name="document-duplicate" class="w-5 h-5 text-indigo-500" />
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Content & Tour Display Images') }}</h3>
                    </div>

                    @php
                        $displayFields = [
                            ['label' => 'Tour Hero Image', 'model' => 'new_tour_hero_image', 'preview' => 'tour_hero_image', 'desc' => 'Default hero background on tour pages'],
                            ['label' => 'Tour Fallback Image', 'model' => 'new_tour_fallback_image', 'preview' => 'tour_fallback_image', 'desc' => 'Fallback image when tour photo is missing'],
                            ['label' => 'Article Hero Image', 'model' => 'new_article_hero_image', 'preview' => 'article_hero_image', 'desc' => 'Default hero background for articles'],
                        ];
                    @endphp

                    @foreach($displayFields as $field)
                    <div class="border-b border-gray-100 dark:border-gray-800 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $field['label'] }}</label>
                            <span class="text-[11px] text-gray-400">{{ $field['desc'] }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-[3px] flex items-center justify-center border border-gray-200 dark:border-gray-700 overflow-hidden shrink-0">
                                @if(${$field['model']} && method_exists(${$field['model']}, 'isPreviewable') && ${$field['model']}->isPreviewable())<img src="{{ ${$field['model']}->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                @elseif(${$field['model']})
                                    <div class="flex flex-col items-center justify-center h-full w-full bg-red-50 text-red-400">
                                        <x-icon name="exclamation-circle" class="w-5 h-5" />
                                    </div>
                                @elseif(${$field['preview']})
                                    <img src="{{ asset('storage/' . ${$field['preview']}) }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <x-icon name="photo" class="w-6 h-6 text-gray-400" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" wire:model="{{ $field['model'] }}" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-[3px] file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400" accept="image/*">
                                @error($field['model']) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            <!-- 3. System Cover & PDF Images -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Proposal Cover BG Image -->
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2 mb-3">
                        <x-icon name="document-text" class="w-4 h-4 text-indigo-500" />
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Proposal Cover Background') }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">Full-bleed background image on the heading/cover page of PDF proposals. Recommended: landscape, ≥ 1200 × 800 px.</p>

                    <div class="w-full rounded-[3px] overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-900 mb-3" style="height: 140px; position: relative;">
                        @if($new_proposal_heading_bg_image && method_exists($new_proposal_heading_bg_image, 'isPreviewable') && $new_proposal_heading_bg_image->isPreviewable())<img src="{{ $new_proposal_heading_bg_image->temporaryUrl() }}" class="w-full h-full object-cover opacity-80">
                            <span class="absolute bottom-2 right-2 text-[10px] bg-black/60 text-white px-2 py-0.5 rounded">New — not saved yet</span>
                        @elseif($new_proposal_heading_bg_image)
                            <div class="flex flex-col items-center justify-center h-full gap-2 text-red-500 bg-red-50/10">
                                <x-icon name="exclamation-circle" class="w-8 h-8" />
                                <span class="text-xs">{{ __('Invalid image file') }}</span>
                            </div>
                        @elseif($proposal_heading_bg_image)
                            <img src="{{ asset('storage/' . $proposal_heading_bg_image) }}" class="w-full h-full object-cover opacity-80">
                        @else
                            <div class="flex flex-col items-center justify-center h-full gap-2 text-gray-500">
                                <x-icon name="photo" class="w-8 h-8" />
                                <span class="text-xs">{{ __('No background set') }}</span>
                            </div>
                        @endif
                    </div>

                    <input type="file" wire:model="new_proposal_heading_bg_image"
                           class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-[3px] file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400"
                           accept="image/*">
                    @error('new_proposal_heading_bg_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auth / Login Cover Image -->
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2 mb-3">
                        <x-icon name="lock-closed" class="w-4 h-4 text-indigo-500" />
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Auth / Login Cover Image') }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">Featured image displayed on the left panel of Login and Registration pages. Recommended: portrait/landscape, ≥ 1200 × 1200 px.</p>

                    <div class="w-full rounded-[3px] overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-900 mb-3" style="height: 140px; position: relative;">
                        @if($new_auth_image && method_exists($new_auth_image, 'isPreviewable') && $new_auth_image->isPreviewable())<img src="{{ $new_auth_image->temporaryUrl() }}" class="w-full h-full object-cover opacity-80">
                            <span class="absolute bottom-2 right-2 text-[10px] bg-black/60 text-white px-2 py-0.5 rounded">New — not saved yet</span>
                        @elseif($new_auth_image)
                            <div class="flex flex-col items-center justify-center h-full gap-2 text-red-500 bg-red-50/10">
                                <x-icon name="exclamation-circle" class="w-8 h-8" />
                                <span class="text-xs">{{ __('Invalid image file') }}</span>
                            </div>
                        @elseif($auth_image)
                            <img src="{{ asset('storage/' . $auth_image) }}" class="w-full h-full object-cover opacity-80">
                        @else
                            <div class="flex flex-col items-center justify-center h-full gap-2 text-gray-500">
                                <x-icon name="photo" class="w-8 h-8" />
                                <span class="text-xs">{{ __('No auth image set (Using default safari theme)') }}</span>
                            </div>
                        @endif
                    </div>

                    <input type="file" wire:model="new_auth_image"
                           class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-[3px] file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400"
                           accept="image/*">
                    @error('new_auth_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Floating Save Bar for Media Tab -->
            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                @can('edit general settings')
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveGeneral" />
                    <span wire:loading.remove wire:target="saveGeneral">{{ __('Save Media Settings') }}</span>
                    <span wire:loading wire:target="saveGeneral">{{ __('Saving...') }}</span>
                </button>
                @endcan
            </div>
        </form>
    </div>
    @endif

    <!-- Invoice Tab -->
    @if($activeTab === 'invoice')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                    <x-icon name="document-text" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Invoice Settings') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Configure how invoices are generated') }}</p>
                </div>
            </div>

            <form wire:submit="saveInvoice" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prefix *</label>
                        <input type="text" wire:model="invoice_prefix" class="form-input" placeholder="{{ __('INV-') }}">
                        @error('invoice_prefix') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Due Days *</label>
                        <input type="number" wire:model="invoice_due_days" class="form-input" min="0">
                        @error('invoice_due_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="invoice_send_reminder" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Automatically send reminders') }}</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-5 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div>
                        <x-admin.rich-text wire:model="invoice_terms" label="{{ __('Default Terms') }}" />
                    </div>
                    <div>
                        <x-admin.rich-text wire:model="invoice_note" label="{{ __('Default Note') }}" />
                    </div>
                    <div>
                        <x-admin.rich-text wire:model="invoice_footer" label="{{ __('Footer Information') }}" />
                    </div>
                </div>

                <div class="flex justify-end">
                    @can('edit invoices settings')
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveInvoice">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveInvoice" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveInvoice" />
                        <span wire:loading.remove wire:target="saveInvoice">{{ __('Save Invoice Settings') }}</span>
                        <span wire:loading wire:target="saveInvoice">{{ __('Saving...') }}</span>
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Estimate Tab -->
    @if($activeTab === 'estimate')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                    <x-icon name="document-chart-bar" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Estimate Settings') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Configure how estimates are handled') }}</p>
                </div>
            </div>

            <form wire:submit="saveEstimate" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prefix *</label>
                        <input type="text" wire:model="estimate_prefix" class="form-input" placeholder="{{ __('EST-') }}">
                        @error('estimate_prefix') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Valid Days *</label>
                        <input type="number" wire:model="estimate_due_days" class="form-input" min="0">
                        @error('estimate_due_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="estimate_convert_to_invoice" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Allow automatic conversion to invoice on acceptance') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="estimate_send_reminder" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Automatically send reminders') }}</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-5 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div>
                        <x-admin.rich-text wire:model="estimate_terms" label="{{ __('Default Terms') }}" />
                    </div>
                    <div>
                        <x-admin.rich-text wire:model="estimate_note" label="{{ __('Default Note') }}" />
                    </div>
                    <div>
                        <x-admin.rich-text wire:model="estimate_footer" label="{{ __('Footer Information') }}" />
                    </div>
                </div>

                <div class="flex justify-end">
                    @can('edit estimates settings')
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveEstimate">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveEstimate" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveEstimate" />
                        <span wire:loading.remove wire:target="saveEstimate">{{ __('Save Estimate Settings') }}</span>
                        <span wire:loading wire:target="saveEstimate">{{ __('Saving...') }}</span>
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Bank Tab -->
    @if($activeTab === 'bank')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                    <x-icon name="banknotes" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Bank Details') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Information displayed on invoices') }}</p>
                </div>
            </div>

            <form wire:submit="saveBank" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Bank Name') }}</label>
                        <input type="text" wire:model="bank_name" class="form-input">
                        @error('bank_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Account Name') }}</label>
                        <input type="text" wire:model="bank_account_name" class="form-input">
                        @error('bank_account_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Account Number') }}</label>
                        <input type="text" wire:model="bank_account_number" class="form-input">
                        @error('bank_account_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Branch') }}</label>
                        <input type="text" wire:model="bank_branch" class="form-input">
                        @error('bank_branch') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('SWIFT / BIC Code') }}</label>
                        <input type="text" wire:model="bank_swift" class="form-input">
                        @error('bank_swift') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    @can('edit bank details')
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveBank">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveBank" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveBank" />
                        <span wire:loading.remove wire:target="saveBank">{{ __('Save Bank Details') }}</span>
                        <span wire:loading wire:target="saveBank">{{ __('Saving...') }}</span>
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Financial Tab -->
    @if($activeTab === 'financial')
    <div class="w-full space-y-4">
        
        <form wire:submit="saveFinancial">
            <!-- Currency Settings -->
            <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800 mb-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Currency Configuration') }}</h3>
                    <a href="{{ route('settings.currencies') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1" wire:navigate>
                        <x-icon name="cog-6-tooth" class="w-3 h-3" />{{ __('Manage Currencies') }}</a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Base Currency *</label>
                        <select wire:model="currency_base" class="form-input">
                            <option value="">{{ __('Select Base Currency') }}</option>
                            @foreach(\App\Models\Currency::active()->get() as $currency)
                                <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }} ({{ $currency->symbol }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 italic mt-1">{{ __('This is your primary accounting currency. All reports will be generated using this currency.') }}</p>
                        @error('currency_base') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Transaction Currency *</label>
                        <select wire:model="currency_default" class="form-input">
                            <option value="">{{ __('Select Default Currency') }}</option>
                            @foreach(\App\Models\Currency::active()->get() as $currency)
                                <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }} ({{ $currency->symbol }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 italic mt-1">{{ __('This currency will be pre-selected when creating new estimates or invoices.') }}</p>
                        @error('currency_default') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <!-- Taxes Settings -->
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('Taxes Configuration') }}</h3>
                    
                    <div class="space-y-4">
                        @foreach($taxes as $index => $tax)
                        <div class="flex gap-2 items-start">
                            <div class="flex-1">
                                <input type="text" wire:model="taxes.{{ $index }}.name" class="form-input py-1.5 text-sm" placeholder="{{ __('Tax Name (e.g. VAT)') }}">
                                @error('taxes.'.$index.'.name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <div class="w-24">
                                <input type="number" step="0.01" wire:model="taxes.{{ $index }}.rate" class="form-input py-1.5 text-sm" placeholder="{{ __('Rate %') }}">
                                @error('taxes.'.$index.'.rate') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <button type="button" wire:click="removeTax({{ $index }})" class="text-red-500 hover:text-red-700 p-2 mt-0.5" title="{{ __('Delete Company Setting') }}">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        @endforeach
                    </div>
                    
                    <button type="button" wire:click="addTax" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                        <x-icon name="plus" class="w-4 h-4" />{{ __('Add Tax') }}</button>
                </div>

                <!-- Discounts Settings -->
                <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('Discounts Configuration') }}</h3>
                    
                    <div class="space-y-4">
                        @foreach($discounts as $index => $discount)
                        <div class="flex gap-2 items-start">
                            <div class="flex-1">
                                <input type="text" wire:model="discounts.{{ $index }}.name" class="form-input py-1.5 text-sm" placeholder="{{ __('Discount Name') }}">
                                @error('discounts.'.$index.'.name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                           
                            <div class="w-24">
                            <input type="number" step="0.01" wire:model="discounts.{{ $index }}.value" class="form-input py-1.5 text-sm" placeholder="00">
                                @error('discounts.'.$index.'.value') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <button type="button" wire:click="removeDiscount({{ $index }})" class="text-red-500 hover:text-red-700 p-2 mt-0.5" title="{{ __('Delete Company Setting') }}">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        @endforeach
                    </div>
                    
                    <button type="button" wire:click="addDiscount" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                        <x-icon name="plus" class="w-4 h-4" />{{ __('Add Discount') }}</button>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveFinancial">
                    <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveFinancial" />
                    <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveFinancial" />
                    <span wire:loading.remove wire:target="saveFinancial">{{ __('Save Financial Settings') }}</span>
                    <span wire:loading wire:target="saveFinancial">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Social Links Tab -->
    @if($activeTab === 'social')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                    <x-icon name="share" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Social Links') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Configure your social media links for the frontend') }}</p>
                </div>
            </div>

            <form wire:submit="saveSocial" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Facebook URL') }}</label>
                        <input type="text" wire:model="facebook_url" class="form-input" placeholder="https://facebook.com/...">
                        @error('facebook_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Instagram URL') }}</label>
                        <input type="text" wire:model="instagram_url" class="form-input" placeholder="https://instagram.com/...">
                        @error('instagram_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('YouTube URL') }}</label>
                        <input type="text" wire:model="youtube_url" class="form-input" placeholder="https://youtube.com/...">
                        @error('youtube_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('X (Twitter) URL') }}</label>
                        <input type="text" wire:model="x_url" class="form-input" placeholder="https://x.com/...">
                        @error('x_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('TikTok URL') }}</label>
                        <input type="text" wire:model="tiktok_url" class="form-input" placeholder="https://tiktok.com/...">
                        @error('tiktok_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('WhatsApp Number') }}</label>
                        <input type="text" wire:model="whatsapp_number" class="form-input" placeholder="e.g. +1234567890">
                        @error('whatsapp_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    @can('edit general settings')
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveSocial">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveSocial" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveSocial" />
                        <span wire:loading.remove wire:target="saveSocial">{{ __('Save Social Links') }}</span>
                        <span wire:loading wire:target="saveSocial">{{ __('Saving...') }}</span>
                    </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Security Tab -->
    @if($activeTab === 'security')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                    <x-icon name="shield-check" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Security Settings') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Configure global security policies') }}</p>
                </div>
            </div>

            <form wire:submit="saveSecurity" class="space-y-4">
                <div class="space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer p-4 border border-gray-200 dark:border-gray-700 rounded-[3px] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="pt-0.5">
                            <input type="checkbox" wire:model="force_2fa" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-600 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('Force Two-Factor Authentication (2FA)') }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('When enabled, all users will be required to set up Two-Factor Authentication before accessing the system.') }}</span>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveSecurity">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveSecurity" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveSecurity" />
                        <span wire:loading.remove wire:target="saveSecurity">{{ __('Save Security Settings') }}</span>
                        <span wire:loading wire:target="saveSecurity">{{ __('Saving...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Meta Lead Ads Tab -->
    @if($activeTab === 'meta_leads')
    <div class="w-full">
        <div class="bg-card rounded-[3px] p-6 shadow-sm border border-gray-100 dark:border-gray-800 space-y-6">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Meta (Facebook/Instagram) Lead Ads Integration') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Connect your Facebook &amp; Instagram Lead Ads to automatically capture new leads as booking inquiries and newsletter subscribers.') }}</p>
            </div>

            <!-- Webhook URL Box -->
            <div class="bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-[3px] p-4">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">{{ __('Your Webhook URL (Copy to Meta App Dashboard)') }}</span>
                        <code class="block text-sm font-mono text-indigo-950 dark:text-indigo-200 mt-1 select-all">{{ url('/webhooks/meta-leads') }}</code>
                    </div>
                    <button type="button" 
                            x-data 
                            @click="navigator.clipboard.writeText('{{ url('/webhooks/meta-leads') }}'); $dispatch('notify', { message: 'Webhook URL copied to clipboard!', type: 'success' })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-[3px] transition-colors shadow-sm">
                        <x-icon name="clipboard-document" class="w-4 h-4" />{{ __('Copy URL') }}</button>
                </div>
            </div>

            <form wire:submit="saveMetaLeads" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Meta App ID') }}</label>
                        <input type="text" wire:model="meta_app_id" class="form-input" placeholder="e.g. 123456789012345">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Found in Meta App Dashboard &gt; App Settings &gt; Basic.') }}</p>
                        @error('meta_app_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Meta App Secret') }}</label>
                        <input type="password" wire:model="meta_app_secret" class="form-input" placeholder="••••••••••••••••••••••••••••••••">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Used to verify incoming webhook signatures (SHA-256 HMAC).') }}</p>
                        @error('meta_app_secret') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Meta Webhook Verify Token') }}</label>
                        <input type="text" wire:model="meta_webhook_verify_token" class="form-input" placeholder="{{ __('Create your custom secret token (e.g. safaris_meta_secret_2026)') }}">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Enter this exact same token into Facebook when registering the Webhook.') }}</p>
                        @error('meta_webhook_verify_token') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Meta Page Access Token (Long-lived)') }}</label>
                        <textarea wire:model="meta_page_access_token" rows="3" class="form-input" placeholder="{{ __('EAA...') }}"></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Requires 'leads_retrieval', 'pages_show_list', and 'pages_manage_ads' permissions to fetch lead details.</p>
                        @error('meta_page_access_token') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveMetaLeads">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="saveMetaLeads" />
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="saveMetaLeads" />
                        <span wire:loading.remove wire:target="saveMetaLeads">{{ __('Save Meta Settings') }}</span>
                        <span wire:loading wire:target="saveMetaLeads">{{ __('Saving...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

