<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Clients Management') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $clients->total() }} total clients</p>
        </div>
        <div class="flex items-center gap-3">
            @can('view client reports')
                <button wire:click="openReportModal" class="btn-secondary" id="btn-report-clients">
                    <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            @can('create clients')
                <button wire:click="openCreate" class="btn-primary" id="btn-create-client">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('Add Client') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Clients Report') }}" :show="$showReportModal">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
            <select wire:model="statusFilter" class="form-input text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach(App\Enums\ClientStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Membership Tier') }}</label>
            <select wire:model="tierFilter" class="form-input text-sm">
                <option value="">{{ __('All Tiers') }}</option>
                @foreach(App\Enums\MembershipTier::cases() as $tier)
                    <option value="{{ $tier->value }}">{{ $tier->label() }}</option>
                @endforeach
            </select>
        </div>
    </x-admin.report-modal>

    <!-- Password Update Modal -->
    @if($showPasswordModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showPasswordModal', false)">
            <div class="modal-box max-w-sm overflow-hidden flex flex-col shadow-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Reset Password') }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('Set a new password for the user') }}</p>
                    </div>
                    <button wire:click="$set('showPasswordModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="updatePassword">
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password
                                *</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="newPassword" class="form-input flex-1"
                                    placeholder="{{ __('Min 8 characters') }}">
                                <button type="button" wire:click="generatePassword"
                                    class="btn-secondary whitespace-nowrap !py-1" title="{{ __('Generate random password') }}">
                                    <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Generate') }}</button>
                            </div>
                            @error('newPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 rounded-[3px] p-3 border border-amber-100 dark:border-amber-900/30">
                            <div class="flex gap-3">
                                <x-icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0" />
                                <p class="text-xs text-amber-800 dark:text-amber-300 text-left">{{ __('The user will be able to log in with this new password immediately.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                        <button type="button" wire:click="$set('showPasswordModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('Update Password') }}</span>
                            <span wire:loading>{{ __('Updating...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page and Status (small widths) -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input w-full sm:w-40">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by...') }}"
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
                        <th>
                            <button wire:click="sort('name')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Client
                                @if($sortBy === 'name')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}"
                                        class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="">{{ __('Contact') }}</th>
                        <th class="">{{ __('Status') }}</th>
                        <th class="">{{ __('City') }}</th>
                        <th>
                            <button wire:click="sort('created_at')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Joined
                                @if($sortBy === 'created_at')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}"
                                        class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr wire:key="client-{{ $client->id }}" class="animate-fade-in">
                            <!-- Client -->
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar-placeholder gradient-blue w-9 h-9 text-xs">
                                        {{ substr($client->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-100">
                                            @if($client->salutation)<span
                                                    class="text-gray-400 font-normal mr-0.5">{{ $client->salutation->value }}</span>
                                            @endif
                                            {{ $client->name }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $client->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <!-- Contact -->
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $client->phone ?? '—' }}
                            </td>
                            <!-- Status -->
                            <td class="">
                                @php $statusValue = $client->status->value ?? 'active'; @endphp
                                <span
                                    class="badge flex items-center gap-1.5 w-fit
                                                                                                                                                                                                {{ $statusValue === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : '' }}
                                                                                                                                                                                                {{ $statusValue === 'inactive' ? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                                                                                                                                                                                {{ $statusValue === 'suspended' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : '' }}
                                                                                                                                                                                                {{ $statusValue === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}">
                                    <span
                                        class="status-dot
                                                                                                                                                                                                    {{ $statusValue === 'active' ? 'online' : '' }}
                                                                                                                                                                                                    {{ $statusValue === 'inactive' ? 'offline' : '' }}
                                                                                                                                                                                                    {{ $statusValue === 'suspended' ? 'offline' : '' }}
                                                                                                                                                                                                    {{ $statusValue === 'pending' ? 'away' : '' }}">
                                    </span>
                                    {{ $client->status->label() ?? ucfirst($statusValue) }}
                                </span>
                            </td>
                            <!-- City -->
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $client->city?->name ?? '—' }}
                            </td>
                            <!-- Joined -->
                            <td class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $client->created_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <!-- Actions -->
                            <td>
                                <x-admin.action-dropdown>
                                    @can('reset client password')
                                        <button wire:click="openPasswordModal({{ $client->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                                            title="{{ __('Change password') }}">
                                            <x-icon name="key" class="w-4 h-4" />
                                        </button>
                                        @if($client->user && $client->user->two_factor_secret)
                                        <button wire:click="disable2FA({{ $client->id }})" wire:confirm="Are you sure you want to disable 2FA for this user?"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="{{ __('Disable Two-Factor Authentication') }}">
                                            <x-icon name="shield-exclamation" class="w-4 h-4" />
                                        </button>
                                        @endif
                                    @endcan
                                    @can('edit clients')
                                        <button wire:click="openEdit({{ $client->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit client') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('edit clients')
                                        <button wire:click="confirmStatusToggle({{ $client->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors"
                                            title="{{ $client->status->value === 'active' ? __('Mark as Inactive') : __('Mark as Active') }}">
                                            <x-icon name="{{ $client->status->value === 'active' ? 'no-symbol' : 'check-circle' }}" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <x-icon name="users"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No clients found') }}</p>
                                @if($search)
                                    <p class="text-xs text-gray-400 mt-1">{{ __('Try adjusting your search or filters') }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $editingId ? 'Edit Client' : 'Create New Client' }}
                        </h3>
                        <p class="text-sm text-gray-400 mt-0.5">
                            {{ $editingId ? 'Update client details' : 'Add a new client and user account to the system' }}
                        </p>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Form -->
                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <!-- General Info Section -->
                        <div>
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="user" class="w-4 h-4" />{{ __('General Information') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Salutation -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Salutation
                                        *</label>
                                    <select wire:model="salutation" class="form-input">
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach($salutationOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('salutation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full
                                        Name *</label>
                                    <input type="text" wire:model="name" class="form-input" placeholder="{{ __('John Doe') }}">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email
                                        Address *</label>
                                    <input type="email" wire:model="email" class="form-input"
                                        placeholder="john@example.com">
                                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        User Account Password {{ $editingId ? '(leave blank to keep current)' : '*' }}
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="password" class="form-input flex-1"
                                            placeholder="{{ $editingId ? '••••••••' : 'Min 8 characters' }}">
                                        <button type="button" wire:click="generateInitialPassword"
                                            class="btn-secondary whitespace-nowrap !py-1" title="{{ __('Generate random password') }}">
                                            <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Generate') }}</button>
                                    </div>
                                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Phone') }}</label>
                                    <input type="text" wire:model="phone" class="form-input"
                                        placeholder="+1 (555) 000-0000">
                                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- DOB -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Date of
                                        Birth') }}</label>
                                    <input type="date" wire:model="date_of_birth" class="form-input">
                                    @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Gender') }}</label>
                                    <select wire:model="gender" class="form-input">
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach($genderOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status
                                        *</label>
                                    <select wire:model="status" class="form-input">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('TIN
                                        Number') }}</label>
                                    <input type="text" wire:model="tin" class="form-input">
                                    @error('tin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('VAT/VRN
                                        Number') }}</label>
                                    <input type="text" wire:model="vrn" class="form-input">
                                    @error('vrn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="map-pin" class="w-4 h-4" />{{ __('Address Information') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Address
                                        Line 1') }}</label>
                                    <input type="text" wire:model="address_line1" class="form-input"
                                        placeholder="{{ __('123 Main St') }}">
                                    @error('address_line1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Address
                                        Line 2') }}</label>
                                    <input type="text" wire:model="address_line2" class="form-input" placeholder="{{ __('Apt 4B') }}">
                                    @error('address_line2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Country (for filtering) -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Country') }}</label>
                                    <select wire:model.live="country_id" class="form-input">
                                        <option value="">{{ __('Select country...') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- City -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('City') }}</label>
                                    <select wire:model="city_id" class="form-input" {{ !$country_id ? 'disabled' : '' }}>
                                        <option value="">{{ __('Select city...') }}</option>
                                        @foreach($cities as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    @if(!$country_id)
                                        <p class="text-[10px] text-gray-400 mt-1 italic text-right">{{ __('Please select a country
                                            first') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Passport Section -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="check-badge" class="w-4 h-4" />{{ __('Passport & Travel') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Passport
                                        Number') }}</label>
                                    <input type="text" wire:model="passport_number" class="form-input"
                                        placeholder="{{ __('ABC123456') }}">
                                    @error('passport_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Passport
                                        Expiry') }}</label>
                                    <input type="date" wire:model="passport_expiry_date" class="form-input">
                                    @error('passport_expiry_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Passport
                                        Country') }}</label>
                                    <input type="text" wire:model="passport_country" class="form-input"
                                        placeholder="{{ __('e.g. United Kingdom') }}">
                                    @error('passport_country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="phone" class="w-4 h-4" />{{ __('Emergency Contact') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Contact
                                        Name') }}</label>
                                    <input type="text" wire:model="emergency_contact_name" class="form-input"
                                        placeholder="{{ __('Jane Doe') }}">
                                    @error('emergency_contact_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Contact
                                        Phone') }}</label>
                                    <input type="text" wire:model="emergency_contact_phone" class="form-input"
                                        placeholder="+1 (555) 000-0000">
                                    @error('emergency_contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Relationship') }}</label>
                                    <input type="text" wire:model="emergency_contact_relationship" class="form-input"
                                        placeholder="{{ __('Spouse, Parent, etc.') }}">
                                    @error('emergency_contact_relationship') <p class="text-red-500 text-xs mt-1">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Billing Information -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="credit-card" class="w-4 h-4" />{{ __('Billing Information') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing
                                        Name') }}</label>
                                    <input type="text" wire:model="billing_name" class="form-input"
                                        placeholder="{{ __('Company or Individual Name') }}">
                                    @error('billing_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing
                                        Email') }}</label>
                                    <input type="email" wire:model="billing_email" class="form-input"
                                        placeholder="billing@example.com">
                                    @error('billing_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing
                                        Phone') }}</label>
                                    <input type="text" wire:model="billing_phone" class="form-input"
                                        placeholder="+1 (555) 000-0000">
                                    @error('billing_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing
                                        Address') }}</label>
                                    <textarea wire:model="billing_address" class="form-input min-h-[80px]"
                                        placeholder="{{ __('Full billing address...') }}"></textarea>
                                    @error('billing_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Postal
                                        Code') }}</label>
                                    <input type="text" wire:model="billing_postal_code" class="form-input"
                                        placeholder="10001">
                                    @error('billing_postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing
                                        City') }}</label>
                                    <input type="text" wire:model="billing_city" class="form-input" placeholder="{{ __('New York') }}">
                                    @error('billing_city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Billing
                                        Country') }}</label>
                                    <input type="text" wire:model="billing_country" class="form-input"
                                        placeholder="{{ __('United States') }}">
                                    @error('billing_country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="clipboard-list" class="w-4 h-4" />{{ __('Preferences & Notes') }}</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Loyalty
                                        Membership #</label>
                                    <input type="text" wire:model="loyalty_membership_number" class="form-input"
                                        placeholder="{{ __('GOLD-12345') }}">
                                    @error('loyalty_membership_number') <p class="text-red-500 text-xs mt-1">{{ $message }}
                                    </p> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Loyalty
                                            Points') }}</label>
                                        <input type="number" wire:model="loyalty_points" class="form-input">
                                        @error('loyalty_points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Membership
                                            Tier') }}</label>
                                        <select wire:model="membership_tier" class="form-input">
                                            @foreach($tierOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('membership_tier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Internal
                                        Notes') }}</label>
                                    <textarea wire:model="notes" class="form-input min-h-[100px]"
                                        placeholder="{{ __('Internal staff notes about this client...') }}"></textarea>
                                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <span wire:loading.remove>{{ $editingId ? 'Update Client' : 'Create Client' }}</span>
                            <span wire:loading>{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Status Confirmation Modal -->
    @if($showStatusModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showStatusModal', false)">
            <div class="modal-box max-w-sm">
                <div class="p-5 text-center">
                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="arrow-path" class="w-8 h-8 text-orange-600 dark:text-orange-400" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Change Client Status?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to change the active status of this client? They can be reactivated later.') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showStatusModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button wire:click="toggleClientStatus" class="btn-primary" title="{{ __('Change Status') }}">
                            <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Change Status') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
