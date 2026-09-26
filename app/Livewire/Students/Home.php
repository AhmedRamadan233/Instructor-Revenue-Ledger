<?php

namespace App\Livewire\Students;

use App\Enums\SubscriptionStatus;
use App\Models\Course;
use App\Models\Subscription;
use Livewire\Attributes\Title;

#[Title('Student Dashboard')]
class Home extends __AbstractStudentComponent
{
    public function render()
    {
        return view('livewire.students.home', [
            'subscriptionsCount' => Subscription::query()->count(),
            'activeSubscriptionsCount' => Subscription::query()
                ->status(SubscriptionStatus::Active)
                ->count(),
            'coursesCount' => Course::query()->published()->count(),
            'recentSubscriptions' => Subscription::query()
                ->with(['planOption.plan'])
                ->latest()
                ->limit(3)
                ->get(),
            'recentCourses' => Course::query()
                ->published()
                ->with([
                    'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
                ])
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }
}
