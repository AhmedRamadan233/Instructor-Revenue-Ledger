<?php

namespace App\Livewire\Students;

use App\Enums\SubscriptionStatus;
use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Student Dashboard')]
class Home extends __AbstractStudentComponent
{
    private SubscriptionRepositoryInterface $subscriptions;

    private CourseRepositoryInterface $courses;

    public function boot(
        SubscriptionRepositoryInterface $subscriptions,
        CourseRepositoryInterface $courses,
    ): void {
        $this->subscriptions = $subscriptions;
        $this->courses = $courses;
    }

    public function render()
    {
        return view('livewire.students.home.index', [
            'subscriptionsCount' => $this->subscriptions->count(),
            'activeSubscriptionsCount' => $this->subscriptions->query()
                ->status(SubscriptionStatus::Active)
                ->count(),
            'coursesCount' => $this->courses->query()->published()->count(),
            'recentSubscriptions' => $this->subscriptions->getWith(
                relations: ['planOption.plan'],
                orderBy: 'created_at',
                direction: 'desc',
                limit: 3,
            ),
            'recentCourses' => $this->courses->getWith(
                relations: [
                    'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
                ],
                scopes: ['published' => []],
                orderBy: 'created_at',
                direction: 'desc',
                limit: 3,
            ),
        ]);
    }
}
