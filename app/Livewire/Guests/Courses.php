<?php

namespace App\Livewire\Guests;

use App\Models\Course;
use Livewire\Attributes\Title;

#[Title('Courses')]
class Courses extends __AbstractGuestComponent
{
    public string $search = '';

    public function render()
    {
        $courses = Course::query()
            ->withoutGlobalScopes()
            ->published()
            ->search($this->search)
            ->with([
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ])
            ->latest()
            ->get();

        return view('livewire.guests.courses', [
            'courses' => $courses,
        ]);
    }
}
