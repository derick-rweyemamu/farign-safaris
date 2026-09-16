<?php

namespace App\Livewire\Admin\Clients;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\User;
use App\Models\City;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasReports;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Clients Management')]
class ClientsList extends Component
{
    use WithPagination, HasReports, AuthorizesRequests;

    public string $search = '';
    public string $statusFilter = '';
    public string $tierFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Modal state
    public bool $showModal = false;
    public bool $showStatusModal = false;
    public bool $showPasswordModal = false;
    public ?int $editingId = null;
    public ?int $togglingId = null;
    public string $newPassword = '';

    // Form fields
    public string $salutation = '';
    public string $gender = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $phone = '';
    public string $status = 'active';
    public string $membership_tier = 'bronze';
    public ?string $address_line1 = '';
    public string $address_line2 = '';
    public ?int $country_id = null;
    public ?int $city_id = null;

    // Additional fields
    public $date_of_birth;
    public $billing_name;
    public $billing_email;
    public $billing_phone;
    public $billing_address;
    public $billing_postal_code;
    public $billing_city;
    public $billing_country;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $emergency_contact_relationship;
    public $preferences = [];
    public $dietary_restrictions = [];
    public $medical_conditions = [];
    public $passport_number;
    public $passport_expiry_date;
    public $passport_country;
    public $loyalty_membership_number;
    public $loyalty_points = 0;
    public $notes;

    protected function rules(): array
    {
        $passwordRules = $this->editingId
            ? 'nullable|min:8'
            : 'required|min:8';

        $clientRuleId = $this->editingId ?? 'NULL';

        return [
            'salutation' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\Salutation::class)],
            'name' => 'required|string|max:100',
            // User email uniqueness is tricky when editing, but we sync client & user email
            'email' => 'required|email|unique:clients,email,' . $clientRuleId,
            'password' => $passwordRules,
            'phone' => 'nullable|string|max:30',
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\ClientStatus::class)],
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'date_of_birth' => 'nullable|date',
            'gender' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\Gender::class)],
            'billing_name' => 'nullable|string|max:255',
            'billing_email' => 'nullable|email',
            'billing_phone' => 'nullable|string|max:30',
            'billing_address' => 'nullable|string',
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_city' => 'nullable|string|max:100',
            'billing_country' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'preferences' => 'nullable|array',
            'dietary_restrictions' => 'nullable|array',
            'medical_conditions' => 'nullable|array',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date',
            'passport_country' => 'nullable|string|max:100',
            'loyalty_membership_number' => 'nullable|string|max:50',
            'loyalty_points' => 'nullable|numeric',
            'membership_tier' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\MembershipTier::class)],
            'notes' => 'nullable|string',
        ];
    }

    public function updatedSearch(): void
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

    public function updatedCountryId(): void
    {
        $this->city_id = null;
    }

    public function openCreate(): void
    {
        $this->authorize('create clients');
        $this->reset([
            'salutation',
            'name',
            'email',
            'password',
            'phone',
            'status',
            'address_line1',
            'address_line2',
            'country_id',
            'city_id',
            'editingId',
            'date_of_birth',
            'gender',
            'billing_name',
            'billing_email',
            'billing_phone',
            'billing_address',
            'billing_postal_code',
            'billing_city',
            'billing_country',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relationship',
            'preferences',
            'dietary_restrictions',
            'medical_conditions',
            'passport_number',
            'passport_expiry_date',
            'passport_country',
            'loyalty_membership_number',
            'loyalty_points',
            'membership_tier',
            'notes'
        ]);
        $this->status = \App\Enums\ClientStatus::ACTIVE->value;
        $this->membership_tier = \App\Enums\MembershipTier::BRONZE->value;
        $this->preferences = [];
        $this->dietary_restrictions = [];
        $this->medical_conditions = [];
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit clients');
        $client = Client::findOrFail($id);

        $this->editingId = $id;
        $this->salutation = $client->salutation?->value ?? '';
        $this->name = $client->name;
        $this->email = $client->email;
        $this->password = '';
        $this->phone = $client->phone ?? '';
        $this->status = $client->status->value;
        $this->address_line1 = $client->address_line1 ?? '';
        $this->address_line2 = $client->address_line2 ?? '';
        $this->city_id = $client->city_id;
        $this->country_id = $client->city?->country_id;

        $this->date_of_birth = $client->date_of_birth ? \Illuminate\Support\Carbon::parse($client->date_of_birth)->format('Y-m-d') : null;
        $this->gender = $client->gender->value ?? '';
        $this->billing_name = $client->billing_name;
        $this->billing_email = $client->billing_email;
        $this->billing_phone = $client->billing_phone;
        $this->billing_address = $client->billing_address;
        $this->billing_postal_code = $client->billing_postal_code;
        $this->billing_city = $client->billing_city;
        $this->billing_country = $client->billing_country;
        $this->emergency_contact_name = $client->emergency_contact_name;
        $this->emergency_contact_phone = $client->emergency_contact_phone;
        $this->emergency_contact_relationship = $client->emergency_contact_relationship;
        $this->preferences = $client->preferences ?? [];
        $this->dietary_restrictions = $client->dietary_restrictions ?? [];
        $this->medical_conditions = $client->medical_conditions ?? [];
        $this->passport_number = $client->passport_number;
        $this->passport_expiry_date = $client->passport_expiry_date ? \Illuminate\Support\Carbon::parse($client->passport_expiry_date)->format('Y-m-d') : null;
        $this->passport_country = $client->passport_country;
        $this->loyalty_membership_number = $client->loyalty_membership_number;
        $this->loyalty_points = $client->loyalty_points;
        $this->membership_tier = $client->membership_tier?->value;
        $this->notes = $client->notes;

        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit clients' : 'create clients');
        $this->validate();

        // Also ensure user email is unique if it's changing
        if ($this->editingId) {
            $client = Client::findOrFail($this->editingId);
            if ($this->email !== $client->email) {
                $this->validate(['email' => 'unique:users,email,' . $client->user_id]);
            }
        } else {
            $this->validate(['email' => 'unique:users,email']);
        }

        DB::transaction(function () {
            $data = [
                'salutation' => $this->salutation,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2,
                'city_id' => $this->city_id,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'billing_name' => $this->billing_name,
                'billing_email' => $this->billing_email,
                'billing_phone' => $this->billing_phone,
                'billing_address' => $this->billing_address,
                'billing_postal_code' => $this->billing_postal_code,
                'billing_city' => $this->billing_city,
                'billing_country' => $this->billing_country,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'emergency_contact_relationship' => $this->emergency_contact_relationship,
                'preferences' => $this->preferences,
                'dietary_restrictions' => $this->dietary_restrictions,
                'medical_conditions' => $this->medical_conditions,
                'passport_number' => $this->passport_number,
                'passport_expiry_date' => $this->passport_expiry_date,
                'passport_country' => $this->passport_country,
                'loyalty_membership_number' => $this->loyalty_membership_number,
                'loyalty_points' => $this->loyalty_points,
                'membership_tier' => $this->membership_tier,
                'notes' => $this->notes,
            ];

            if ($this->editingId) {
                $client = Client::findOrFail($this->editingId);
                $client->update($data);

                $user = $client->user;
                if ($user) {
                    $userData = [
                        'name' => $this->name,
                        'email' => $this->email,
                    ];
                    if ($this->password) {
                        $userData['password'] = bcrypt($this->password);
                    }
                    $user->update($userData);
                }

                ActivityLog::record('client.updated', "Updated client: {$client->email}");
                $this->dispatch('notify', message: __('Client updated successfully!'), type: 'success');

                // If password was manually changed during edit, send email
                if ($this->password && $user) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($user->name, $this->password));
                    } catch (\Exception $e) {
                    }
                }
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => bcrypt($this->password),
                ]);
                $user->assignRole('client');

                $client = $user->client()->create($data);

                ActivityLog::record('client.created', "Created client: {$client->email}");
                $this->dispatch('notify', message: __('Client created successfully!'), type: 'success');

                // For new clients, always send the password
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($user->name, $this->password));
                } catch (\Exception $e) {
                }
            }
        });

        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmStatusToggle(int $id): void
    {
        $this->authorize('edit clients');
        $this->togglingId = $id;
        $this->showStatusModal = true;
    }

    public function toggleClientStatus(): void
    {
        $this->authorize('edit clients');
        $client = Client::findOrFail($this->togglingId);
        
        if ($client->status->value === 'active') {
            $client->update(['status' => 'inactive']);
            ActivityLog::record('client.deactivated', "Deactivated client: {$client->email}");
            $this->dispatch('notify', message: __('Client marked as inactive.'), type: 'success');
        } else {
            $client->update(['status' => 'active']);
            ActivityLog::record('client.activated', "Activated client: {$client->email}");
            $this->dispatch('notify', message: __('Client marked as active.'), type: 'success');
        }

        $this->showStatusModal = false;
        $this->togglingId = null;
        $this->resetPage();
    }

    public function openPasswordModal(int $id): void
    {
        $this->authorize('reset client password');
        $this->editingId = $id;
        $this->newPassword = '';
        $this->showPasswordModal = true;
        $this->resetErrorBag();
    }

    public function disable2FA(int $id): void
    {
        $this->authorize('reset client password');
        $client = Client::findOrFail($id);
        $user = $client->user;

        if ($user && $user->two_factor_secret) {
            $user->update([
                'two_factor_type' => null,
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null,
                'two_factor_recovery_codes' => null,
            ]);
            ActivityLog::record('client.2fa_disabled', "Admin disabled 2FA for client: {$client->email}");
            $this->dispatch('notify', message: __('Two-Factor Authentication disabled for') . $client->name, type: 'success');
        } else {
            $this->dispatch('notify', message: __('Two-Factor Authentication is not enabled for this user.'), type: 'info');
        }
    }

    public function generatePassword(): void
    {
        $this->newPassword = \Illuminate\Support\Str::random(12);
    }

    public function generateInitialPassword(): void
    {
        $this->password = \Illuminate\Support\Str::random(12);
    }

    public function updatePassword(): void
    {
        $this->authorize('reset client password');
        $this->validate([
            'newPassword' => 'required|min:8',
        ]);

        $client = Client::findOrFail($this->editingId);
        $user = $client->user;

        if ($user) {
            $user->update([
                'password' => bcrypt($this->newPassword),
            ]);

            ActivityLog::record('client.password_reset', "Admin reset password for client: {$client->email}");

            // Send email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($user->name, $this->newPassword));
                $this->dispatch('notify', message: __('Password updated and email sent!'), type: 'success');
            } catch (\Exception $e) {
                $this->dispatch('notify', message: __('Password updated, but email failed to send.'), type: 'warning');
            }
        } else {
            $this->dispatch('notify', message: __('No linked user account found for this client.'), type: 'error');
        }

        $this->showPasswordModal = false;
        $this->editingId = null;
        $this->newPassword = '';
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'id' => 'ID',
            'salutation' => 'Salutation',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'status' => 'Status',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'address_line1' => 'Address Line 1',
            'address_line2' => 'Address Line 2',
            'city' => 'City',
            'country' => 'Country',
            'billing_name' => 'Billing Name',
            'billing_email' => 'Billing Email',
            'billing_phone' => 'Billing Phone',
            'billing_address' => 'Billing Address',
            'billing_city' => 'Billing City',
            'billing_postal_code' => 'Billing Postal Code',
            'billing_country' => 'Billing Country',
            'emergency_contact_name' => 'Emergency Contact Name',
            'emergency_contact_phone' => 'Emergency Contact Phone',
            'emergency_contact_relationship' => 'Emergency Relationship',
            'passport_number' => 'Passport Number',
            'passport_country' => 'Passport Country',
            'passport_expiry_date' => 'Passport Expiry Date',
            'membership_tier' => 'Membership Tier',
            'loyalty_membership_number' => 'Loyalty Card #',
            'loyalty_points' => 'Loyalty Points',
            'preferences' => 'Preferences',
            'dietary_restrictions' => 'Dietary Restrictions',
            'medical_conditions' => 'Medical Conditions',
            'notes' => 'Notes',
            'created_at' => 'Joined Date',
        ];
    }

    public function openReportModal(): void
    {
        $this->authorize('view client reports');
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['name', 'email', 'phone', 'status', 'membership_tier', 'created_at'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        $this->authorize('view client reports');
        [$start, $end] = $this->getReportDateRange();

        $query = Client::with(['city.country'])
            ->whereBetween('created_at', [$start, $end])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->tierFilter, fn($q) => $q->where('membership_tier', $this->tierFilter));

        // Sort the selected columns to match the order of availableColumns definition
        $availableKeys = array_keys($this->availableColumns);
        $orderedSelectedColumns = array_values(array_intersect($availableKeys, $this->selectedColumns));

        $headers = collect($this->availableColumns)->only($orderedSelectedColumns)->values()->toArray();

        $rowMapper = function ($client) use ($orderedSelectedColumns) {
            $row = [];
            foreach ($orderedSelectedColumns as $col) {
                $row[] = match ($col) {
                    // Basic Information
                    'id' => $client->id,
                    'salutation' => $client->salutation?->label() ?? 'N/A',
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'status' => $client->status?->label() ?? 'N/A',

                    // Personal Information
                    'gender' => $client->gender?->label() ?? 'N/A',
                    'date_of_birth' => $client->date_of_birth
                    ? $client->date_of_birth->format('d M Y')
                    : 'N/A',

                    // Address Information
                    'address_line1' => $client->address_line1,
                    'address_line2' => $client->address_line2,
                    'city' => $client->city?->name ?? 'N/A',
                    'country' => $client->city?->country?->name ?? 'N/A',

                    // Billing Information
                    'billing_name' => $client->billing_name,
                    'billing_email' => $client->billing_email,
                    'billing_phone' => $client->billing_phone,
                    'billing_address' => $client->billing_address,
                    'billing_city' => $client->billing_city,
                    'billing_postal_code' => $client->billing_postal_code,
                    'billing_country' => $client->billing_country,

                    // Emergency Contact
                    'emergency_contact_name' => $client->emergency_contact_name,
                    'emergency_contact_phone' => $client->emergency_contact_phone,
                    'emergency_contact_relationship' => $client->emergency_contact_relationship,

                    // Passport & Travel Information
                    'passport_number' => $client->passport_number,
                    'passport_country' => $client->passport_country,
                    'passport_expiry_date' => $client->passport_expiry_date
                    ? $client->passport_expiry_date->format('d M Y')
                    : 'N/A',

                    // Membership & Loyalty
                    'membership_tier' => $client->membership_tier?->label() ?? 'N/A',
                    'loyalty_membership_number' => $client->loyalty_membership_number ?? 'N/A',
                    'loyalty_points' => $client->loyalty_points ?? 0,

                    // Preferences & Health
                    'preferences' => is_array($client->preferences) ? implode(', ', $client->preferences) : ($client->preferences ?? 'N/A'),
                    'dietary_restrictions' => is_array($client->dietary_restrictions) ? implode(', ', $client->dietary_restrictions) : ($client->dietary_restrictions ?? 'N/A'),
                    'medical_conditions' => is_array($client->medical_conditions) ? implode(', ', $client->medical_conditions) : ($client->medical_conditions ?? 'N/A'),

                    // Additional Notes
                    'notes' => $client->notes ?? 'N/A',
                    'created_at' => $client->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Clients_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Clients_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view clients');
        $clients = Client::with('city')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(
                    fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when(
                $this->statusFilter,
                fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $countries = \App\Models\Country::orderBy('name')->get();
        $cities = $this->country_id
            ? City::where('country_id', $this->country_id)->orderBy('name')->get()
            : collect();

        $statusOptions = \App\Enums\ClientStatus::options();
        $tierOptions = \App\Enums\MembershipTier::options();
        $salutationOptions = \App\Enums\Salutation::options();
        $genderOptions = \App\Enums\Gender::options();

        return view('livewire.admin.clients.clients-list', compact('clients', 'countries', 'cities', 'statusOptions', 'tierOptions', 'salutationOptions', 'genderOptions'));
    }
}
