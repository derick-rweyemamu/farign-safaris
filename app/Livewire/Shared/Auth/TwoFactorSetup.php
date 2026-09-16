<?php

namespace App\Livewire\Shared\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('admin::layouts.guest')]
#[Title('Setup Two-Factor Authentication')]
class TwoFactorSetup extends Component
{
    public int $step = 1;
    public string $email = '';
    public string $setupType = ''; // 'app' or 'email'
    public string $twoFactorCode = '';
    
    public string $twoFactorSetupSecret = '';
    public string $twoFactorQrCodeUrl = '';
    public array $recoveryCodes = [];

    public function mount(): void
    {
        if (!session()->has('auth.2fa_setup_id')) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::find(session('auth.2fa_setup_id'));
        if (!$user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }
    }

    public function confirmEmail(): void
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $user = User::find(session('auth.2fa_setup_id'));

        if (!$user || $user->email !== $this->email) {
            $this->addError('email', __('The email address does not match our records.'));
            return;
        }

        $this->step = 2;
    }

    public function selectType(string $type): void
    {
        $this->setupType = $type;
        $this->step = 3;

        $user = User::find(session('auth.2fa_setup_id'));

        if ($type === 'app') {
            $google2fa = new Google2FA();
            $this->twoFactorSetupSecret = $google2fa->generateSecretKey();
            
            $bacon = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($bacon);
            
            $qrUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $this->twoFactorSetupSecret
            );
            
            $this->twoFactorQrCodeUrl = $writer->writeString($qrUrl);

            $this->recoveryCodes = collect(range(1, 8))->map(function () {
                return strtoupper(\Illuminate\Support\Str::random(4) . '-' . \Illuminate\Support\Str::random(4));
            })->toArray();
        } elseif ($type === 'email') {
            $this->twoFactorSetupSecret = sprintf("%06d", mt_rand(100000, 999999));
            Cache::put('2fa_setup_' . $user->id, $this->twoFactorSetupSecret, now()->addMinutes(10));
            Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($this->twoFactorSetupSecret));
        }
    }

    public function verify(): void
    {
        $this->validate([
            'twoFactorCode' => 'required|string',
        ]);

        $userId = session('auth.2fa_setup_id');
        $user = User::find($userId);

        if (!$user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $isValid = false;

        if ($this->setupType === 'app') {
            $google2fa = new Google2FA();
            $isValid = $google2fa->verifyKey($this->twoFactorSetupSecret, $this->twoFactorCode);
        } elseif ($this->setupType === 'email') {
            $cachedCode = Cache::get('2fa_setup_' . $userId);
            if ($cachedCode && $cachedCode === $this->twoFactorCode) {
                $isValid = true;
                Cache::forget('2fa_setup_' . $userId);
            }
        }

        if (!$isValid) {
            $this->addError('twoFactorCode', __('The provided authentication code is incorrect or has expired.'));
            return;
        }

        // Save 2FA to user
        $user->update([
            'two_factor_type' => $this->setupType,
            'two_factor_secret' => $this->setupType === 'app' ? encrypt($this->twoFactorSetupSecret) : null,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $this->setupType === 'app' ? $this->recoveryCodes : null,
        ]);

        // Login the user
        $remember = session('auth.2fa_remember', false);
        Auth::login($user, $remember);
        request()->session()->regenerate();

        // Clear session data
        session()->forget(['auth.2fa_setup_id', 'auth.2fa_remember']);

        // Update last login
        $user->profile()->updateOrCreate([], [
            'last_login_at' => now(),
            'status'        => 'active',
        ]);

        ActivityLog::record('auth.login', 'User signed in and configured 2FA');

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function backToTypeSelection(): void
    {
        $this->step = 2;
        $this->setupType = '';
        $this->twoFactorCode = '';
    }

    public function render()
    {
        return view('livewire.shared.auth.two-factor-setup');
    }
}
