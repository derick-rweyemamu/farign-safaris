<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Title('Profile Settings')]
class ProfileSettings extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public string $activeTab = 'profile';

    // Profile fields
    #[Validate('required|string|max:100')]
    public string $name       = '';
    public string $email      = ''; // Email is read-only for clients/employees in some contexts
    #[Validate('nullable|string|max:30')]
    public string $phone      = '';
    
    // Address fields (Shared/Mapped)
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $address       = ''; // For employee

    // Personal Info
    public $date_of_birth;
    public string $gender = '';

    // Emergency Contact (Shared)
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $emergency_contact_relationship = '';

    // Billing Info (Client only)
    public string $billing_name = '';
    public string $billing_email = '';
    public string $billing_phone = '';
    public string $billing_address = '';
    public string $billing_postal_code = '';
    public string $billing_city = '';
    public string $billing_country = '';

    // Additional identifiers
    public string $employee_id_code = '';
    public string $loyalty_membership_number = '';
    public string $membership_tier = '';

    // Admin/Staff specific (Optional, keeping them if needed)
    #[Validate('nullable|string|max:100')]
    public string $position   = '';
    #[Validate('nullable|string|max:100')]
    public string $department = '';
    #[Validate('nullable|string|max:255')]
    public string $location   = '';
    #[Validate('nullable|url|max:255')]
    public string $website    = '';
    #[Validate('nullable|string|max:200')]
    public string $bio        = '';

    // Password fields
    public string $currentPassword = '';
    public string $newPassword     = '';
    public string $confirmPassword = '';

    // Avatar & Signature
    #[Validate('nullable|image|max:2048')]
    public $avatar = null;
    #[Validate('nullable|image|max:2048')]
    public $signature = null;
    #[Validate('nullable|string|max:500000|regex:/^data:image\/(png|jpeg|webp);base64,/')]
    public $signatureDataUrl = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name       = $user->name;
        $this->email      = $user->email;

        // Load data based on role/relationship
        if ($user->hasRole('Client')) {
            $client = $user->client;
            if ($client) {
                $this->phone         = $client->phone ?? '';
                $this->address_line1 = $client->address_line1 ?? '';
                $this->address_line2 = $client->address_line2 ?? '';
                $this->date_of_birth = $client->date_of_birth?->format('Y-m-d');
                $this->gender        = $client->gender?->value ?? ($client->gender ?? '');
                
                $this->emergency_contact_name = $client->emergency_contact_name ?? '';
                $this->emergency_contact_phone = $client->emergency_contact_phone ?? '';
                $this->emergency_contact_relationship = $client->emergency_contact_relationship ?? '';

                $this->billing_name  = $client->billing_name ?? '';
                $this->billing_email = $client->billing_email ?? '';
                $this->billing_phone = $client->billing_phone ?? '';
                $this->billing_address = $client->billing_address ?? '';
                $this->billing_postal_code = $client->billing_postal_code ?? '';
                $this->billing_city  = $client->billing_city ?? '';
                $this->billing_country = $client->billing_country ?? '';

                $this->loyalty_membership_number = $client->loyalty_membership_number ?? '';
                $this->membership_tier = $client->membership_tier ?? '';
            }
        } else {
            // Admin or Other
            $this->phone      = $user->profile?->phone ?? '';
            $this->position   = $user->profile?->position ?? '';
            $this->department = $user->profile?->department ?? '';
            $this->location   = $user->profile?->location ?? '';
            $this->website    = $user->profile?->website ?? '';
            $this->bio        = $user->profile?->bio ?? '';
        }
    }

    public function saveProfile(): void
    {
        $this->validateOnly('name');
        
        $user = auth()->user();

        // Update name
        $user->update(['name' => $this->name]);

        // Handle avatar
        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $user->profile()->updateOrCreate([], ['avatar' => $path]);
        }


        if ($user->hasRole('Client')) {
            $user->client()->update([
                'phone'         => $this->phone,
                'address_line1' => $this->address_line1,
                'address_line2' => $this->address_line2,
                'date_of_birth' => $this->date_of_birth,
                'gender'        => $this->gender,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'emergency_contact_relationship' => $this->emergency_contact_relationship,
                'billing_name'  => $this->billing_name,
                'billing_email' => $this->billing_email,
                'billing_phone' => $this->billing_phone,
                'billing_address' => $this->billing_address,
                'billing_postal_code' => $this->billing_postal_code,
                'billing_city'  => $this->billing_city,
                'billing_country' => $this->billing_country,
            ]);
        } else {
            // Admin/Staff profile
            $user->profile()->updateOrCreate([], [
                'phone'      => $this->phone,
                'position'   => $this->position,
                'department' => $this->department,
                'location'   => $this->location,
                'website'    => $this->website,
                'bio'        => $this->bio,
            ]);
        }

        ActivityLog::record('profile.updated', "User updated their profile");
        $this->dispatch('notify', message: __('Profile updated successfully!'), type: 'success');
    }

    public function savePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword'     => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        if (!password_verify($this->currentPassword, auth()->user()->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update(['password' => bcrypt($this->newPassword)]);

        $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);

        ActivityLog::record('password.changed', "User changed their password");
        $this->dispatch('notify', message: __('Password changed successfully!'), type: 'success');
    }

    // --- 2FA Properties & Methods ---
    public string $twoFactorSetupSecret = '';
    public string $twoFactorQrCodeUrl = '';
    public string $twoFactorCode = '';
    public bool $showTwoFactorSetup = false;
    public string $setupType = '';

    public function initiateTwoFactorSetup($type): void
    {
        $this->authorize('enable disable 2FA');
        $this->setupType = $type;
        $this->twoFactorCode = '';

        if ($type === 'app') {
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $this->twoFactorSetupSecret = $google2fa->generateSecretKey();
            
            $bacon = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($bacon);
            
            $qrUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                auth()->user()->email,
                $this->twoFactorSetupSecret
            );
            
            $this->twoFactorQrCodeUrl = $writer->writeString($qrUrl);
        } elseif ($type === 'email') {
            $this->twoFactorSetupSecret = sprintf("%06d", mt_rand(100000, 999999));
            \Illuminate\Support\Facades\Cache::put('2fa_setup_' . auth()->id(), $this->twoFactorSetupSecret, now()->addMinutes(10));
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)->send(new \App\Mail\TwoFactorCodeMail($this->twoFactorSetupSecret));
        }

        $this->showTwoFactorSetup = true;
    }

    public function confirmTwoFactor(): void
    {
        $this->authorize('enable disable 2FA');
        $this->validate(['twoFactorCode' => 'required']);

        $isValid = false;
        if ($this->setupType === 'app') {
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $isValid = $google2fa->verifyKey($this->twoFactorSetupSecret, $this->twoFactorCode);
        } elseif ($this->setupType === 'email') {
            $cachedCode = \Illuminate\Support\Facades\Cache::get('2fa_setup_' . auth()->id());
            if ($cachedCode && $cachedCode === $this->twoFactorCode) {
                $isValid = true;
                \Illuminate\Support\Facades\Cache::forget('2fa_setup_' . auth()->id());
            }
        }

        if (!$isValid) {
            $this->addError('twoFactorCode', 'The code is invalid or has expired.');
            return;
        }

        auth()->user()->update([
            'two_factor_type' => $this->setupType,
            'two_factor_secret' => $this->setupType === 'app' ? encrypt($this->twoFactorSetupSecret) : null,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->showTwoFactorSetup = false;
        $this->twoFactorCode = '';
        
        ActivityLog::record('security.2fa_enabled', "User enabled 2FA ({$this->setupType})");
        $this->dispatch('notify', message: __('Two-Factor Authentication enabled successfully!'), type: 'success');
    }

    public function disableTwoFactor(): void
    {
        $this->authorize('enable disable 2FA');
        auth()->user()->update([
            'two_factor_type' => null,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        ActivityLog::record('security.2fa_disabled', "User disabled 2FA");
        $this->dispatch('notify', message: __('Two-Factor Authentication has been disabled.'), type: 'info');
    }

    public function render()
    {
        $appraisals = collect();


        $layout = (auth()->user()->client || auth()->user()->hasRole('client')) ? 'layouts.client' : 'layouts.app';
        return view('livewire.admin.settings.profile-settings', compact('appraisals'))
            ->layout($layout);
    }
}
