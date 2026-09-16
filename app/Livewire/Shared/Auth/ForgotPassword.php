<?php

namespace App\Livewire\Shared\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Password;

#[Layout('admin::layouts.guest')]
#[Title('Forgot Password')]
class ForgotPassword extends Component
{
    public $email = '';

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We could not find a user with that email address.'
        ]);

        try {
            $status = Password::broker()->sendResetLink(['email' => $this->email]);

            if ($status === Password::RESET_LINK_SENT) {
                session()->flash('status', __($status));
                $this->email = '';
            } else {
                $this->addError('email', __($status));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Password reset email error: ' . $e->getMessage());
            $this->addError('email', 'Could not send reset link due to a mail server configuration issue. Please verify your Email/SMTP settings in Admin Settings → Email Settings.');
        }
    }

    public function render()
    {
        return view('livewire.shared.auth.forgot-password');
    }
}
