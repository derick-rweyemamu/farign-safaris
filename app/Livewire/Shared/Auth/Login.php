<?php

namespace App\Livewire\Shared\Auth;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

#[Layout('admin::layouts.guest')]
#[Title('Sign In')]
class Login extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login.' . Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', __('Too many login attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]));
            return;
        }

        $user = \App\Models\User::where('email', $this->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($key, 60);
            $this->addError('email', __('Invalid credentials. Please try again.'));
            return;
        }

        RateLimiter::clear($key);



        // Check Client status
        if ($user->client && $user->client->status !== \App\Enums\ClientStatus::ACTIVE) {
            $statusLabel = $user->client->status->label() ?? 'inactive';
            $this->addError('email', __('Your client account is currently :status. Please contact support.', ['status' => strtolower($statusLabel)]));
            return;
        }

        // Check Profile status
        if ($user->profile && $user->profile->status === 'inactive') {
            $this->addError('email', __('Your account is currently inactive. Please contact support.'));
            return;
        }

        $settings = app(\App\Settings\GeneralSetting::class);
        if ($settings->force_2fa && (!$user->two_factor_type || !$user->two_factor_confirmed_at)) {
            session()->put('auth.2fa_setup_id', $user->id);
            session()->put('auth.2fa_remember', $this->remember);
            $this->redirect(route('2fa.setup'), navigate: true);
            return;
        }

        // Check if 2FA is enabled
        if ($user->two_factor_type && $user->two_factor_confirmed_at) {
            session()->put('auth.2fa_id', $user->id);
            session()->put('auth.2fa_remember', $this->remember);

            // Send Email OTP if applicable
            if ($user->two_factor_type === 'email') {
                $code = sprintf("%06d", mt_rand(100000, 999999));
                \Illuminate\Support\Facades\Cache::put('2fa_code_' . $user->id, $code, now()->addMinutes(10));
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($code));
            }

            $this->redirect(route('2fa.challenge'), navigate: true);
            return;
        }

        // Standard Login
        Auth::login($user, $this->remember);
        request()->session()->regenerate();

        // Update last login
        $user->profile()->updateOrCreate([], [
            'last_login_at' => now(),
        ]);

        ActivityLog::record('auth.login', 'User signed in');

        if ($user->client) {
            $this->redirect(route('client.dashboard'), navigate: true);
            return;
        }

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.shared.auth.login');
    }
}
