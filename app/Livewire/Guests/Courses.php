<?php

namespace App\Livewire\Guests;

use App\Repo\InterFace\CourseRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Courses')]
class Courses extends __AbstractGuestComponent
{
    private CourseRepositoryInterface $courses;

    public string $search = '';

    public function boot(CourseRepositoryInterface $courses): void
    {
        $this->courses = $courses;
    }

    public function render()
    {
        $courses = $this->courses->getWith(
            relations: [
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ],
            scopes: [
                'published' => [],
                'search' => [$this->search],
            ],
            orderBy: 'created_at',
            direction: 'desc',
            withoutGlobalScopes: true,
        );

        return view('livewire.guests.courses', [
            'courses' => $courses,
        ]);
    }
}
