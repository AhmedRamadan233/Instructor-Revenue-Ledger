<?php

namespace App\Livewire\Guests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;

#[Title('Login')]
class Login extends __AbstractGuestComponent
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        $this->redirectRoute($user->dashboardRouteName(), navigate: true);
    }

    public function render()
    {
        return view('livewire.guests.login');
    }
}
