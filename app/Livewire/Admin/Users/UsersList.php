<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\User;
use App\Traits\HasReports;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('admin::layouts.app')]
#[Title('Users Management')]
class UsersList extends Component
{
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Modal states
    public bool $showModal = false;
    public bool $showPasswordModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $passwordUserId = null;
    public ?int $deletingId = null;
    public string $newPassword = '';
    public bool $isEditingSuperAdmin = false;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];
    public string $status = 'active';
    public string $phone = '';
    public string $department = '';
    public string $position = '';
    public string $location = '';
    public string $bio = '';
    public $avatar = null;
    public ?string $existingAvatar = null;

    protected function rules(): array
    {
        $userId = $this->editingId ?? 'NULL';
        $passwordRule = $this->editingId ? 'nullable|min:8' : 'required|min:8';

        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $passwordRule,
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,name',
            'status' => 'required|string|in:active,inactive,pending,suspended',
            'phone' => 'nullable|string|max:30',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'selectedRoles.required' => 'Please select at least one role for this user.',
        'selectedRoles.min' => 'Please select at least one role for this user.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create users');
        $this->reset([
            'name',
            'email',
            'password',
            'selectedRoles',
            'status',
            'phone',
            'department',
            'position',
            'location',
            'bio',
            'avatar',
            'existingAvatar',
            'editingId',
        ]);
        $this->isEditingSuperAdmin = false;
        $this->status = 'active';
        $this->selectedRoles = ['staff'];
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit users');
        $user = User::with(['roles', 'profile'])->findOrFail($id);

        if ($user->hasRole('super admin')) {
            $this->dispatch('notify', message: __('Super Admin accounts cannot be managed here.'), type: 'error');
            return;
        }

        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->status = $user->profile?->status ?? 'active';
        $this->phone = $user->profile?->phone ?? '';
        $this->department = $user->profile?->department ?? '';
        $this->position = $user->profile?->position ?? '';
        $this->location = $user->profile?->location ?? '';
        $this->bio = $user->profile?->bio ?? '';
        $this->existingAvatar = $user->profile?->avatar;
        $this->avatar = null;

        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function generateInitialPassword(): void
    {
        $this->password = Str::password(12, true, true, false, false);
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit users' : 'create users');

        // Security: Prevent assigning super admin role under any circumstances
        if (in_array('super admin', $this->selectedRoles)) {
            $this->addError('selectedRoles', __('The Super Admin role is protected and cannot be assigned.'));
            return;
        }

        $this->validate();

        DB::transaction(function () {
            if ($this->editingId) {
                $user = User::findOrFail($this->editingId);

                if ($user->hasRole('super admin')) {
                    $this->addError('selectedRoles', __('Super Admin accounts cannot be edited from this panel.'));
                    return;
                }

                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                ];

                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }

                $user->update($userData);
                $user->syncRoles($this->selectedRoles);

                $profileData = [
                    'phone' => $this->phone,
                    'department' => $this->department,
                    'position' => $this->position,
                    'location' => $this->location,
                    'bio' => $this->bio,
                    'status' => $this->status,
                ];

                if ($this->avatar) {
                    $profileData['avatar'] = $this->avatar->store('avatars', 'public');
                }

                $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

                ActivityLog::record('user.updated', "Updated user: {$user->name} ({$user->email})");
                $this->dispatch('notify', message: __('User updated successfully!'), type: 'success');
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);

                $user->syncRoles($this->selectedRoles);

                $avatarPath = $this->avatar ? $this->avatar->store('avatars', 'public') : null;

                $user->profile()->create([
                    'phone' => $this->phone,
                    'department' => $this->department,
                    'position' => $this->position,
                    'location' => $this->location,
                    'bio' => $this->bio,
                    'status' => $this->status,
                    'avatar' => $avatarPath,
                ]);

                ActivityLog::record('user.created', "Created new user: {$user->name} ({$user->email})");
                $this->dispatch('notify', message: __('User created successfully!'), type: 'success');
            }

            $this->showModal = false;
        });
    }

    public function openPasswordModal(int $id): void
    {
        $this->authorize('reset user password');
        $user = User::findOrFail($id);
        if ($user->hasRole('super admin')) {
            $this->dispatch('notify', message: __('Super Admin passwords cannot be modified from this panel.'), type: 'error');
            return;
        }

        $this->passwordUserId = $id;
        $this->newPassword = '';
        $this->showPasswordModal = true;
        $this->resetErrorBag();
    }

    public function generatePassword(): void
    {
        $this->newPassword = Str::password(12, true, true, false, false);
    }

    public function updatePassword(): void
    {
        $this->authorize('reset user password');
        $this->validate([
            'newPassword' => 'required|min:8',
        ]);

        $user = User::findOrFail($this->passwordUserId);
        if ($user->hasRole('super admin')) {
            $this->dispatch('notify', message: __('Super Admin passwords cannot be modified from this panel.'), type: 'error');
            $this->showPasswordModal = false;
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        ActivityLog::record('user.password_reset', "Reset password for user: {$user->name} ({$user->email})");
        $this->dispatch('notify', message: __('Password updated successfully!'), type: 'success');
        $this->showPasswordModal = false;
    }

    public function confirmStatusToggle(int $id): void
    {
        $this->authorize('edit users');
        $user = User::with('profile')->findOrFail($id);

        if ($user->hasRole('super admin') || $user->id === auth()->id()) {
            $this->dispatch('notify', message: __('You cannot change this account status.'), type: 'error');
            return;
        }

        $currentStatus = $user->profile?->status ?? 'active';
        $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';

        $user->profile()->updateOrCreate(['user_id' => $user->id], ['status' => $newStatus]);

        ActivityLog::record('user.status_toggled', "Changed status of {$user->name} to {$newStatus}");
        $this->dispatch('notify', message: __("User status changed to :status.", ['status' => ucfirst($newStatus)]), type: 'success');
    }

    public function disable2FA(int $id): void
    {
        $this->authorize('edit users');
        $user = User::findOrFail($id);

        if ($user->hasRole('super admin')) {
            $this->dispatch('notify', message: __('Two-Factor Authentication for Super Admin cannot be disabled from here.'), type: 'error');
            return;
        }

        $user->update([
            'two_factor_type' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        ActivityLog::record('user.2fa_disabled', "Disabled 2FA for {$user->name}");
        $this->dispatch('notify', message: __('Two-Factor Authentication disabled for this user.'), type: 'success');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete users');
        $user = User::findOrFail($id);

        if ($user->hasRole('super admin') || $user->id === auth()->id()) {
            $this->dispatch('notify', message: __('This account cannot be deleted.'), type: 'error');
            return;
        }

        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        $this->authorize('delete users');
        if (!$this->deletingId) {
            return;
        }

        $user = User::findOrFail($this->deletingId);

        if ($user->hasRole('super admin') || $user->id === auth()->id()) {
            $this->dispatch('notify', message: __('This account cannot be deleted.'), type: 'error');
            $this->showDeleteModal = false;
            return;
        }

        $userName = $user->name;
        $userEmail = $user->email;

        DB::transaction(function () use ($user) {
            $user->profile()?->delete();
            $user->syncRoles([]);
            $user->delete();
        });

        ActivityLog::record('user.deleted', "Deleted user: {$userName} ({$userEmail})");
        $this->dispatch('notify', message: __('User deleted successfully.'), type: 'success');

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function generateReport()
    {
        $this->authorize('view users');

        [$start, $end] = $this->getReportDateRange();

        $query = User::with(['roles', 'profile'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'super admin'))
            ->whereBetween('created_at', [$start, $end])
            ->when($this->statusFilter, function ($q) {
                $q->whereHas('profile', fn($pq) => $pq->where('status', $this->statusFilter));
            })
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', fn($rq) => $rq->where('name', $this->roleFilter));
            })
            ->orderBy($this->sortBy, $this->sortDir);

        $headers = [
            'ID',
            'Full Name',
            'Email Address',
            'Roles',
            'Status',
            'Phone',
            'Department',
            'Position',
            'Location',
            'Joined Date',
        ];

        $rowMapper = function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->roles->pluck('name')->map(fn($r) => ucwords($r))->implode(', '),
                ucfirst($user->profile?->status ?? 'Active'),
                $user->profile?->phone ?? 'N/A',
                $user->profile?->department ?? 'N/A',
                $user->profile?->position ?? 'N/A',
                $user->profile?->location ?? 'N/A',
                $user->created_at?->format('M d, Y') ?? 'N/A',
            ];
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Users_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Users_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view users');

        $users = User::with(['roles', 'profile'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'super admin'))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhereHas('profile', function ($pq) {
                            $pq->where('phone', 'like', "%{$this->search}%")
                                ->orWhere('department', 'like', "%{$this->search}%")
                                ->orWhere('position', 'like', "%{$this->search}%")
                                ->orWhere('location', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', fn($rq) => $rq->where('name', $this->roleFilter));
            })
            ->when($this->statusFilter, function ($q) {
                $q->whereHas('profile', fn($pq) => $pq->where('status', $this->statusFilter));
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $allRoles = Role::where('name', '!=', 'super admin')->orderBy('name')->get();
        $assignableRoles = $allRoles;
        $statusOptions = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
            'suspended' => 'Suspended',
        ];

        return view('livewire.admin.users.users-list', compact('users', 'allRoles', 'assignableRoles', 'statusOptions'));
    }
}
