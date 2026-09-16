<?php

namespace App\Livewire\Shared\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin::layouts.guest')]
#[Title('Client Registration')]
class ClientRegister extends Component
{
    public string $name              = '';
    public string $email             = '';
    public string $phone             = '';
    public string $address_line1     = '';
    public string $password          = '';
    public string $salutation        = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'salutation'    => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\Salutation::class)],
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:30',
            'address_line1' => 'required|string|max:255',
            'password'      => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(12)->mixedCase()->numbers()],
        ];
    }

    public function register(): void
    {
        $this->validate();

        $user = DB::transaction(function () {
            // 1. Create the User
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => bcrypt($this->password),
            ]);

            // Assign default role (client)
            if (\Spatie\Permission\Models\Role::where('name', 'client')->exists()) {
                $user->assignRole('client');
            }

            // Create profile for user
            $user->profile()->create(['status' => 'active', 'last_login_at' => now(), 'phone' => $this->phone]);

            // 2. Create the Client Profile
            $user->client()->create([
                'salutation'    => $this->salutation,
                'name'          => $this->name,
                'email'         => $this->email,
                'phone'         => $this->phone,
                'address_line1' => $this->address_line1,
                'status'        => 'active',
            ]);

            return $user;
        });

        Auth::login($user);
        request()->session()->regenerate();

        ActivityLog::record('auth.register', "New client registered: {$user->email}");

        $this->redirect(route('client.dashboard'), navigate: true);
    }

    public function render()
    {
        $salutationOptions = \App\Enums\Salutation::options();
        return view('livewire.shared.auth.client-register', compact('salutationOptions'));
    }
}
