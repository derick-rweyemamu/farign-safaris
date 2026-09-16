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
#[Title('Two-Factor Authentication')]
class TwoFactorChallenge extends Component
{
    public string $code = '';
    public string $type = '';
    public bool $useRecoveryCode = false;

    public function mount(): void
    {
        if (!session()->has('auth.2fa_id')) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::find(session('auth.2fa_id'));
        if (!$user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->type = $user->two_factor_type;
    }

    public function verify(): void
    {
        $this->validate([
            'code' => 'required|string',
        ]);

        $userId = session('auth.2fa_id');
        $user = User::find($userId);

        if (!$user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $key = '2fa_attempts_' . $userId . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('code', __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]));
            return;
        }

        $isValid = false;

        if ($this->useRecoveryCode) {
            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            if (in_array($this->code, $recoveryCodes)) {
                $isValid = true;
                // Remove the used recovery code
                $user->update([
                    'two_factor_recovery_codes' => array_values(array_diff($recoveryCodes, [$this->code]))
                ]);
            }
        } elseif ($this->type === 'email') {
            $cachedCode = Cache::get('2fa_code_' . $userId);
            if ($cachedCode && $cachedCode === $this->code) {
                $isValid = true;
                Cache::forget('2fa_code_' . $userId);
            }
        } elseif ($this->type === 'app') {
            $google2fa = new Google2FA();
            $isValid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $this->code);
        }

        if (!$isValid) {
            RateLimiter::hit($key, 60);
            $this->addError('code', __('The provided authentication code is incorrect or has expired.'));
            return;
        }

        RateLimiter::clear($key);

        // Login the user
        $remember = session('auth.2fa_remember', false);
        Auth::login($user, $remember);
        request()->session()->regenerate();

        // Clear 2FA session data
        session()->forget(['auth.2fa_id', 'auth.2fa_remember']);

        // Update last login
        $user->profile()->updateOrCreate([], [
            'last_login_at' => now(),
            'status'        => 'active',
        ]);

        ActivityLog::record('auth.login', 'User signed in via 2FA');

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function resendCode(): void
    {
        $userId = session('auth.2fa_id');
        $user = User::find($userId);

        if ($user && $user->two_factor_type === 'email') {
            $key = '2fa_resend_' . $userId;

            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                $this->addError('code', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $seconds]));
                return;
            }

            RateLimiter::hit($key, 60);

            $code = sprintf("%06d", mt_rand(100000, 999999));
            Cache::put('2fa_code_' . $user->id, $code, now()->addMinutes(10));
            Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($code));

            session()->flash('status', __('A new verification code has been sent to your email.'));
        }
    }

    public function toggleRecovery(): void
    {
        $this->useRecoveryCode = !$this->useRecoveryCode;
        $this->code = '';
    }

    public function fallbackToEmail(): void
    {
        $this->type = 'email';
        $this->useRecoveryCode = false;
        $this->resendCode();
    }

    public function render()
    {
        return view('livewire.shared.auth.two-factor-challenge');
    }
}
