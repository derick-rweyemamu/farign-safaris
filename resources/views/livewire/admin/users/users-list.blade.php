<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Users Management') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $users->total() }}
                {{ __('total system users') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            @can('view users')
                <button wire:click="openReportModal" class="btn-secondary" id="btn-report-users">
                    <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}
                </button>
            @endcan
            @can('create users')
                <button wire:click="openCreate" class="btn-primary" id="btn-create-user">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('Add User') }}
                </button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Users Report') }}" :show="$showReportModal">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Role') }}</label>
            <select wire:model="roleFilter" class="form-input text-sm">
                <option value="">{{ __('All Roles') }}</option>
                @foreach($allRoles as $role)
                    <option value="{{ $role->name }}">{{ ucwords($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
            <select wire:model="statusFilter" class="form-input text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach($statusOptions as $val => $lbl)
                    <option value="{{ $val }}">{{ $lbl }}</option>
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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Reset User Password') }}</h3>
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
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('New Password') }}
                                *</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="newPassword" class="form-input flex-1"
                                    placeholder="{{ __('Min 8 characters') }}">
                                <button type="button" wire:click="generatePassword"
                                    class="btn-secondary whitespace-nowrap !py-1"
                                    title="{{ __('Generate random password') }}">
                                    <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Generate') }}
                                </button>
                            </div>
                            @error('newPassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 rounded-[3px] p-3 border border-amber-100 dark:border-amber-900/30">
                            <div class="flex gap-3">
                                <x-icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0" />
                                <p class="text-xs text-amber-800 dark:text-amber-300 text-left">
                                    {{ __('The user will be able to log in with this new password immediately.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                        <button type="button" wire:click="$set('showPasswordModal', false)"
                            class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('Update Password') }}</span>
                            <span wire:loading>{{ __('Updating...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm overflow-hidden flex flex-col shadow-2xl">
                <div class="p-6 text-center">
                    <div
                        class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mx-auto flex items-center justify-center mb-4">
                        <x-icon name="trash" class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Delete User Account') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        {{ __('Are you sure you want to permanently delete this user? This action cannot be undone and will revoke all access.') }}
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" wire:click="$set('showDeleteModal', false)" class="btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" wire:click="deleteUser"
                            class="btn-primary !bg-red-600 hover:!bg-red-700 !border-red-600 text-white"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('Yes, Delete') }}</span>
                            <span wire:loading>{{ __('Deleting...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page, Role filter, and Status filter -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Role filter -->
                <select wire:model.live="roleFilter" class="form-input w-full sm:w-36 text-xs py-1.5">
                    <option value="">{{ __('All Roles') }}</option>
                    @foreach($allRoles as $role)
                        <option value="{{ $role->name }}">{{ ucwords($role->name) }}</option>
                    @endforeach
                </select>

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input w-full sm:w-36 text-xs py-1.5">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statusOptions as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Right side - Search bar -->
            <div class="w-full sm:w-80 relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search users by name, email, phone...') }}"
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
                                {{ __('User') }}
                                @if($sortBy === 'name')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}"
                                        class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Role(s)') }}</th>
                        <th>{{ __('Department / Contact') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>
                            <button wire:click="sort('created_at')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                {{ __('Joined') }}
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
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="animate-fade-in">
                            <!-- User Name & Avatar -->
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($user->profile?->avatar)
                                        <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}"
                                            class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-gray-700" />
                                    @else
                                        <div class="avatar-placeholder gradient-blue w-9 h-9 text-xs font-bold rounded-full">
                                            {{ $user->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                                {{ $user->name }}
                                            </p>
                                            @if($user->id === auth()->id())
                                                <span
                                                    class="text-[10px] bg-brand-light text-brand-primary px-1.5 py-0.5 rounded font-medium">
                                                    {{ __('You') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                        @if($user->profile?->position)
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-0.5">
                                                {{ $user->profile->position }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Roles -->
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        @php
                                            $rName = strtolower($role->name);
                                            $roleClass = match ($rName) {
                                                'super admin' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                                'admin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                                'manager' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                                'editor' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                                'tourguide' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300 border-orange-200 dark:border-orange-800',
                                                'client' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
                                                default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $roleClass }}">
                                            {{ ucwords($role->name) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">{{ __('No role') }}</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Contact & Department -->
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    @if($user->profile?->phone)
                                        <div class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300">
                                            <x-icon name="phone" class="w-3.5 h-3.5 text-gray-400" />
                                            <span>{{ $user->profile->phone }}</span>
                                        </div>
                                    @endif
                                    @if($user->profile?->department)
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $user->profile->department }}
                                        </div>
                                    @endif
                                    @if(!$user->profile?->phone && !$user->profile?->department)
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td>
                                @php
                                    $st = strtolower($user->profile?->status ?? 'active');
                                @endphp
                                <span
                                    class="badge flex items-center gap-1.5 w-fit
                                            {{ $st === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : '' }}
                                            {{ $st === 'inactive' ? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                            {{ $st === 'suspended' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : '' }}
                                            {{ $st === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}">
                                    <span class="status-dot
                                                {{ $st === 'active' ? 'online' : '' }}
                                                {{ $st === 'inactive' ? 'offline' : '' }}
                                                {{ $st === 'suspended' ? 'offline' : '' }}
                                                {{ $st === 'pending' ? 'away' : '' }}">
                                    </span>
                                    {{ ucfirst($st) }}
                                </span>
                            </td>

                            <!-- Joined -->
                            <td class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $user->created_at?->format('M d, Y') ?? '—' }}
                            </td>

                            <!-- Actions -->
                            <td>
                                <x-admin.action-dropdown>
                                    @can('reset user password')
                                        <button wire:click="openPasswordModal({{ $user->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                                            title="{{ __('Change password') }}">
                                            <x-icon name="key" class="w-4 h-4" />
                                        </button>
                                    @endcan

                                    @if($user->two_factor_secret)
                                        <button wire:click="disable2FA({{ $user->id }})"
                                            wire:confirm="{{ __('Are you sure you want to disable Two-Factor Authentication for this user?') }}"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="{{ __('Disable Two-Factor Authentication') }}">
                                            <x-icon name="shield-exclamation" class="w-4 h-4" />
                                        </button>
                                    @endif

                                    @can('edit users')
                                        <button wire:click="openEdit({{ $user->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit user') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button wire:click="confirmStatusToggle({{ $user->id }})"
                                                class="p-2 rounded-[3px] text-gray-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors"
                                                title="{{ ($user->profile?->status ?? 'active') === 'active' ? __('Mark as Inactive') : __('Mark as Active') }}">
                                                <x-icon
                                                    name="{{ ($user->profile?->status ?? 'active') === 'active' ? 'no-symbol' : 'check-circle' }}"
                                                    class="w-4 h-4" />
                                            </button>
                                        @endif
                                    @endcan

                                    @can('delete users')
                                        @if($user->id !== auth()->id())
                                            <button wire:click="confirmDelete({{ $user->id }})"
                                                class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                                title="{{ __('Delete user') }}">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        @endif
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <x-icon name="users" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No users found') }}
                                </p>
                                @if($search || $roleFilter || $statusFilter)
                                    <p class="text-xs text-gray-400 mt-1">{{ __('Try adjusting your search or filters') }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit User Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $editingId ? __('Edit User') : __('Create New User') }}
                        </h3>
                        <p class="text-sm text-gray-400 mt-0.5">
                            {{ $editingId ? __('Update user profile, permissions, and status') : __('Add a new user with credentials and roles to the system') }}
                        </p>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Form -->
                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-5 overflow-y-auto flex-1">

                        <!-- Account & Authentication Section -->
                        <div>
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="user" class="w-4 h-4" />{{ __('Account & Authentication') }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Full Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Full Name') }} *
                                    </label>
                                    <input type="text" wire:model="name" class="form-input"
                                        placeholder="{{ __('e.g. John Doe') }}">
                                    @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Email Address') }} *
                                    </label>
                                    <input type="email" wire:model="email" class="form-input"
                                        placeholder="john@mwangwisafaris.com">
                                    @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Password') }} {{ $editingId ? __('(leave blank to keep current)') : '*' }}
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="password" class="form-input flex-1"
                                            placeholder="{{ $editingId ? '••••••••' : __('Min 8 characters') }}">
                                        <button type="button" wire:click="generateInitialPassword"
                                            class="btn-secondary whitespace-nowrap !py-1"
                                            title="{{ __('Generate random password') }}">
                                            <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Generate') }}
                                        </button>
                                    </div>
                                    @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Account Status') }} *
                                    </label>
                                    <select wire:model="status" class="form-input">
                                        @foreach($statusOptions as $val => $lbl)
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Role Selection -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Assigned System Roles') }} *
                                    </label>
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        @foreach($allRoles as $role)
                                                                    <label
                                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-[3px] border text-xs font-medium cursor-pointer transition-colors
                                                                                                                {{ in_array($role->name, $selectedRoles)
                                            ? 'bg-brand-primary/10 border-brand-primary text-brand-primary dark:bg-brand-primary/20 dark:border-brand-primary dark:text-brand-light'
                                            : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                                                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}"
                                                                            class="rounded text-brand-primary focus:ring-brand-primary">
                                                                        <span>{{ ucwords($role->name) }}</span>
                                                                    </label>
                                        @endforeach
                                    </div>
                                    @error('selectedRoles')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Profile & Contact Information -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <h4
                                class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-icon name="briefcase" class="w-4 h-4" />{{ __('Profile & Work Information') }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Phone Number') }}
                                    </label>
                                    <input type="text" wire:model="phone" class="form-input" placeholder="+255 700 000 000">
                                    @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Department -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Department') }}
                                    </label>
                                    <input type="text" wire:model="department" class="form-input"
                                        placeholder="{{ __('e.g. Operations, Sales, Guide') }}">
                                    @error('department')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Position / Job Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Position / Job Title') }}
                                    </label>
                                    <input type="text" wire:model="position" class="form-input"
                                        placeholder="{{ __('e.g. Senior Safari Guide') }}">
                                    @error('position')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Location -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Location / Office') }}
                                    </label>
                                    <input type="text" wire:model="location" class="form-input"
                                        placeholder="{{ __('e.g. Arusha, Tanzania') }}">
                                    @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Avatar Upload -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Profile Photo / Avatar') }}
                                    </label>
                                    <div class="flex items-center gap-4">
                                        @if($avatar)
                                            <img src="{{ $avatar->temporaryUrl() }}"
                                                class="w-14 h-14 rounded-full object-cover border border-gray-200 dark:border-gray-700 shadow-sm" />
                                        @elseif($existingAvatar)
                                            <img src="{{ asset('storage/' . $existingAvatar) }}"
                                                class="w-14 h-14 rounded-full object-cover border border-gray-200 dark:border-gray-700 shadow-sm" />
                                        @else
                                            <div
                                                class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 border border-gray-200 dark:border-gray-700">
                                                <x-icon name="user" class="w-6 h-6" />
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <input type="file" wire:model="avatar" accept="image/*"
                                                class="form-input text-xs py-1.5">
                                            <p class="text-[11px] text-gray-400 mt-1">{{ __('PNG, JPG or WebP up to 2MB') }}
                                            </p>
                                            @error('avatar')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Bio / Notes -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('Bio / Internal Notes') }}
                                    </label>
                                    <textarea wire:model="bio" rows="3" class="form-input"
                                        placeholder="{{ __('Optional notes or bio description...') }}"></textarea>
                                    @error('bio')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 p-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editingId ? __('Save Changes') : __('Create User') }}</span>
                            <span wire:loading>{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>