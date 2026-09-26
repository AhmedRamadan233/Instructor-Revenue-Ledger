<?php

namespace App\Livewire\Students;

use App\Models\Course;
use App\Models\Subscription;
use Livewire\Attributes\Title;

#[Title('Student Dashboard')]
class Home extends __AbstractStudentComponent
{
    public function render()
    {
        return view('livewire.students.home', [
            'subscriptions' => Subscription::query()
                ->with(['planOption.plan'])
                ->latest()
                ->get(),
            'courses' => Course::query()
                ->published()
                ->with([
                    'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
                ])
                ->latest()
                ->get(),
        ]);
    }
}
