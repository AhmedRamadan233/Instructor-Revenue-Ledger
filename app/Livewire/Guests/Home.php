<?php

namespace App\Livewire\Guests;

use Livewire\Attributes\Title;

#[Title('Home')]
class Home extends __AbstractGuestComponent
{
    public function render()
    {
        return view('livewire.guests.home');
    }
}
