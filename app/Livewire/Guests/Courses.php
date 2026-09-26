<?php

namespace App\Livewire\Guests;

use App\Enums\CourseStatus;
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
            ->where('status', CourseStatus::Published)
            ->with([
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ])
            ->when(
                filled($this->search),
                fn ($query) => $query->where(function ($query): void {
                    $query->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                })
            )
            ->latest()
            ->get();

        return view('livewire.guests.courses', [
            'courses' => $courses,
        ]);
    }
}
