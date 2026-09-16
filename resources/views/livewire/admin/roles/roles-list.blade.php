<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Roles & Permissions') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $roles->total() }} roles defined</p>
        </div>
        @can('create roles')
        <button wire:click="openCreate" class="btn-primary" id="btn-create-role">
            <x-icon name="plus" class="w-4 h-4" />{{ __('Create Role') }}</button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page -->
            <div class="flex items-center gap-3">
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="12">12 / page</option>
                    <option value="24">24 / page</option>
                    <option value="48">48 / page</option>
                    <option value="96">96 / page</option>
                </select>
            </div>

            <!-- Right side - Search -->
            <div class="sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search roles...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
        @forelse($roles as $role)
            <div class="bg-card rounded-[3px] p-5 shadow-sm border border-gray-100 dark:border-gray-800
                                                hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 animate-fade-in-up"
                wire:key="role-{{ $role->id }}">

                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 gradient-blue rounded-[3px] flex items-center justify-center">
                            <x-icon name="shield-check" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white capitalize">{{ $role->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $role->guard_name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        @can('edit roles')
                        <button wire:click="openEdit({{ $role->id }})"
                            class="p-1.5 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                            <x-icon name="pencil-square" class="w-4 h-4" />
                        </button>
                        @endcan
                        @can('delete roles')
                            @if(!in_array($role->name, $systemRoles))
                            <button wire:click="confirmDelete({{ $role->id }})"
                                class="p-1.5 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                            @else
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-800 text-[10px] font-bold text-gray-400 rounded-[3px]" title="{{ __('System roles cannot be deleted') }}">{{ __('DEFAULT') }}</span>
                            @endif
                        @endcan
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-[3px] p-3 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->users_count }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('Users') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-[3px] p-3 text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->permissions_count }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('Permissions') }}</p>
                    </div>
                </div>

                <!-- User count bar -->
                <div class="progress-bar">
                    <div class="progress-bar-fill gradient-blue"
                        style="width: {{ $role->users_count > 0 ? min(100, ($role->users_count / max($roles->max('users_count'), 1)) * 100) : 0 }}%">
                    </div>
                </div>
            </div>
        @empty
            <div
                class="sm:col-span-2 xl:col-span-3 text-center py-16 bg-card rounded-[3px] border border-gray-100 dark:border-gray-800">
                <x-icon name="shield-check" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                <p class="text-sm font-medium text-gray-500">{{ __('No roles found') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())<div>{{ $roles->links() }}</div>
    @endif

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Role' : 'Create New Role' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <!-- Role name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role Name
                                *</label>
                            <input type="text" wire:model="name" class="form-input" placeholder="{{ __('e.g. manager, editor') }}"
                                id="input-role-name">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Permissions -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions
                                    *</label>
                                <label
                                    class="flex items-center gap-2 text-sm cursor-pointer hover:text-indigo-600 transition-colors">
                                    <input type="checkbox" wire:model.live="selectAll"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Select All Permissions') }}</span>
                                </label>
                            </div>

                            <div class="columns-1 md:columns-2 lg:columns-3 gap-4">
                                @forelse($allPermissions as $group => $permissions)
                                    <div
                                        class="break-inside-avoid bg-gray-50 dark:bg-gray-800/50 rounded-[3px] border border-gray-200 dark:border-gray-700 overflow-hidden mb-4">
                                        <div
                                            class="bg-gray-100 dark:bg-gray-800 px-3 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                            <h4
                                                class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                {{ $this->getGroupHeader($group) }}
                                            </h4>
                                            <input type="checkbox"
                                                wire:click="toggleGroup({{ json_encode($permissions->pluck('name')->toArray()) }})"
                                                {{ empty(array_diff($permissions->pluck('name')->toArray(), $selectedPermissions)) ? 'checked' : '' }}
                                                title="Select all {{ $group }} permissions"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm w-3.5 h-3.5 cursor-pointer">
                                        </div>
                                        <div class="p-3 flex flex-col gap-1.5">
                                            @foreach($permissions as $permission)
                                                <label
                                                    class="flex items-center gap-2 p-1.5 rounded hover:bg-white dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                                    <input type="checkbox" wire:model.live="selectedPermissions"
                                                        value="{{ $permission->name }}"
                                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                                                    <span
                                                        class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors capitalize">
                                                        {{ $this->formatPermissionName($permission->name) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-[3px]">
                                        <x-icon name="key"
                                            class="w-8 h-8 text-gray-300 dark:text-gray-700 mx-auto mb-2" />
                                        <p class="text-sm text-gray-400">{{ __('No permissions defined yet.') }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('Run the seeder to populate permissions.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 p-5 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $editingId ? 'Update Role' : 'Create Role' }}</span>
                            <span wire:loading>{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm">
                <div class="p-5 text-center">
                    <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="trash" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Role?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Roles with assigned users cannot be deleted.
                        Remove users from this role first.') }}</p>
                    <div class="flex justify-center gap-3 mt-6">
                        <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                        <button wire:click="deleteRole" class="btn-danger" title="{{ __('Delete Role') }}">
                            <x-icon name="trash" class="w-4 h-4" />{{ __('Delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
