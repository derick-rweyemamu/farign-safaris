<?php

namespace App\Livewire\Admin\Roles;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('admin::layouts.app')]
#[Title('Roles & Permissions')]
class RolesList extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search      = '';
    public bool   $showModal   = false;
    public bool   $showDeleteModal = false;
    public ?int   $editingId   = null;
    public ?int   $deletingId  = null;
    public int    $perPage  = 12;

    public string $name        = '';
    public array  $selectedPermissions = [];
    public bool   $selectAll = false;
    
    public array $systemRoles = ['super admin', 'manager', 'editor', 'staff', 'tourguide', 'client'];

    protected array $rules = [
        'name'                  => 'required|string|max:100',
        'selectedPermissions'   => 'array',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create roles');
        $this->reset(['name', 'selectedPermissions', 'editingId', 'selectAll']);
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit roles');
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->name === 'super admin') {
            $this->dispatch('notify', message: __('The super admin role cannot be edited.'), type: 'error');
            return;
        }

        $this->editingId             = $id;
        $this->name                  = $role->name;
        $this->selectedPermissions   = $role->permissions->pluck('name')->toArray();
        $this->updatedSelectedPermissions(); // Sync Select All state
        $this->showModal             = true;
        $this->resetErrorBag();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedPermissions = Permission::pluck('name')->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    public function updatedSelectedPermissions(): void
    {
        $totalPermissions = Permission::count();
        $this->selectAll = count($this->selectedPermissions) === $totalPermissions && $totalPermissions > 0;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit roles' : 'create roles');
        $this->validate();

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            
            if ($role->name === 'super admin') {
                $this->dispatch('notify', message: __('Unauthorized action.'), type: 'error');
                return;
            }

            $role->update(['name' => $this->name]);
            $role->syncPermissions($this->selectedPermissions);

            ActivityLog::record('role.updated', "Updated role: {$role->name}");
            $this->dispatch('notify', message: __('Role updated successfully!'), type: 'success');
        } else {
            $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);
            $role->syncPermissions($this->selectedPermissions);

            ActivityLog::record('role.created', "Created role: {$role->name}");
            $this->dispatch('notify', message: __('Role created successfully!'), type: 'success');
        }

        $this->showModal = false;
    }

    public function toggleAllPermissions(): void
    {
        $totalPermissions = Permission::count();
        if (count($this->selectedPermissions) >= $totalPermissions && $totalPermissions > 0) {
            $this->selectedPermissions = [];
        } else {
            $this->selectedPermissions = Permission::pluck('name')->toArray();
        }
    }

    public function toggleGroup(array $permissions): void
    {
        $allSelected = empty(array_diff($permissions, $this->selectedPermissions));

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $permissions));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $permissions)));
        }
        
        $this->updatedSelectedPermissions();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete roles');
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRole(): void
    {
        $this->authorize('delete roles');
        $role = Role::findOrFail($this->deletingId);

        if (in_array($role->name, $this->systemRoles)) {
            $this->dispatch('notify', message: __('This is a system role and cannot be deleted.'), type: 'error');
            $this->showDeleteModal = false;
            return;
        }

        if ($role->users()->count() > 0) {
            $this->dispatch('notify', message: __('Cannot delete role with assigned users.'), type: 'error');
            $this->showDeleteModal = false;
            return;
        }

        $name = $role->name;
        $role->delete();

        ActivityLog::record('role.deleted', "Deleted role: {$name}");
        $this->dispatch('notify', message: __('Role deleted.'), type: 'success');

        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function formatPermissionName(string $name): string
    {
        return str_replace('_', ' ', $name);
    }

    public function getGroupHeader(string $group): string
    {
        $mappings = [
            'area' => 'Client Area',
            '2fa' => 'Two-Factor Auth (2FA)',
            'loss' => 'Profit & Loss',
            'detail' => 'Details',
            'details' => 'Details',
            'setting' => 'Settings',
            'settings' => 'Settings',
            'plans' => 'Meal Plans',
            'posts' => 'CMS Posts',
            'pages' => 'CMS Pages',
            'roles' => 'Roles & Permissions',
            'activities' => 'Activities & Logs',
            'clients' => 'Clients',
            'employees' => 'Employees & HR',
            'leads' => 'CRM Leads',
            'estimates' => 'Estimates',
            'invoices' => 'Invoices',
            'invoice expenses' => 'Invoice Expenses',
            'expenses' => 'General Expenses',
            'expense types' => 'Expense Types',
            'payments' => 'Payments',
            'products' => 'Products',
            'product categories' => 'Product Categories',
            'destinations' => 'Destinations',
            'accommodations' => 'Accommodations',
            'tour activities' => 'Tour Activities',
            'transports' => 'Transports',
            'meal plans' => 'Meal Plans',
            'itineraries' => 'Itineraries',
            'departments' => 'Departments',
            'designations' => 'Designations',
            'holidays' => 'Holidays',
            'leaves' => 'Leaves',
            'payroll' => 'Payroll',
            'countries' => 'Countries',
            'cities' => 'Cities',
            'currencies' => 'Currencies',
        ];

        $key = strtolower(trim($group));
        $name = $mappings[$key] ?? $group;

        return ucwords(str_replace('_', ' ', $name)) . ' Permissions';
    }

    public function render()
    {
        $this->authorize('view roles');
        $roles = Role::withCount('users', 'permissions')
            ->where('name', '!=', 'super admin')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->paginate($this->perPage);

        $allPermissions = Permission::orderBy('name')->get()->groupBy(function ($perm) {
            $name = strtolower($perm->name);

            if (str_contains($name, 'client area')) {
                return 'area';
            }
            if (str_contains($name, 'client') || str_contains($name, 'clients')) {
                return 'clients';
            }
            if (str_contains($name, 'employee') || str_contains($name, 'employees')) {
                return 'employees';
            }
            if (str_contains($name, 'lead') || str_contains($name, 'leads')) {
                return 'leads';
            }
            if (str_contains($name, 'estimate') || str_contains($name, 'estimates')) {
                return 'estimates';
            }
            if (str_contains($name, 'invoice expense') || str_contains($name, 'invoice expenses')) {
                return 'invoice expenses';
            }
            if (str_contains($name, 'invoice') || str_contains($name, 'invoices')) {
                return 'invoices';
            }
            if (str_contains($name, 'expense type') || str_contains($name, 'expense types')) {
                return 'expense types';
            }
            if (str_contains($name, 'expense') || str_contains($name, 'expenses')) {
                return 'expenses';
            }
            if (str_contains($name, 'payment') || str_contains($name, 'payments')) {
                return 'payments';
            }
            if (str_contains($name, 'product category') || str_contains($name, 'product categories')) {
                return 'product categories';
            }
            if (str_contains($name, 'product') || str_contains($name, 'products')) {
                return 'products';
            }
            if (str_contains($name, 'destination') || str_contains($name, 'destinations')) {
                return 'destinations';
            }
            if (str_contains($name, 'accommodation') || str_contains($name, 'accommodations')) {
                return 'accommodations';
            }
            if (str_contains($name, 'tour activit')) {
                return 'tour activities';
            }
            if (str_contains($name, 'transport') || str_contains($name, 'transports')) {
                return 'transports';
            }
            if (str_contains($name, 'meal plan') || str_contains($name, 'meal plans')) {
                return 'meal plans';
            }
            if (str_contains($name, 'itinerary') || str_contains($name, 'itineraries')) {
                return 'itineraries';
            }
            if (str_contains($name, 'department') || str_contains($name, 'departments')) {
                return 'departments';
            }
            if (str_contains($name, 'designation') || str_contains($name, 'designations')) {
                return 'designations';
            }
            if (str_contains($name, 'holiday') || str_contains($name, 'holidays')) {
                return 'holidays';
            }
            if (str_contains($name, 'leave') || str_contains($name, 'leaves')) {
                return 'leaves';
            }
            if (str_contains($name, 'payroll')) {
                return 'payroll';
            }
            if (str_contains($name, 'country') || str_contains($name, 'countries')) {
                return 'countries';
            }
            if (str_contains($name, 'city') || str_contains($name, 'cities')) {
                return 'cities';
            }
            if (str_contains($name, 'currency') || str_contains($name, 'currencies')) {
                return 'currencies';
            }
            if (str_contains($name, 'post') || str_contains($name, 'posts')) {
                return 'posts';
            }
            if (str_contains($name, 'page') || str_contains($name, 'pages')) {
                return 'pages';
            }
            if (str_contains($name, 'role') || str_contains($name, 'roles')) {
                return 'roles';
            }
            if (str_contains($name, 'activit') || str_contains($name, 'logs')) {
                return 'activities';
            }
            if (str_contains($name, 'setting') || str_contains($name, 'settings')) {
                return 'settings';
            }

            $parts = explode(' ', $perm->name);
            return end($parts) ?: 'general';
        });

        return view('livewire.admin.roles.roles-list', compact('roles', 'allPermissions'));
    }
}
