<?php

namespace App\Livewire\Students;

use App\Actions\Subscriptions\SubscribeStudentToPlanOption;
use App\Enums\PlanType;
use App\Enums\SubscriptionStatus;
use App\Livewire\Requests\Students\SubscribeRequest;
use App\Repo\InterFace\PlanOptionRepositoryInterface;
use App\Repo\InterFace\PlanRepositoryInterface;
use App\Repo\InterFace\StudentRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use App\Support\AuthActor;
use Livewire\Attributes\Title;

#[Title('Browse Plans')]
class Plans extends __AbstractStudentComponent
{
    private PlanRepositoryInterface $plans;

    private PlanOptionRepositoryInterface $planOptions;

    private StudentRepositoryInterface $students;

    private SubscriptionRepositoryInterface $subscriptions;

    public int $selectedType;

    public int $planOptionId = 0;

    public function boot(
        PlanRepositoryInterface $plans,
        PlanOptionRepositoryInterface $planOptions,
        StudentRepositoryInterface $students,
        SubscriptionRepositoryInterface $subscriptions,
    ): void {
        $this->plans = $plans;
        $this->planOptions = $planOptions;
        $this->students = $students;
        $this->subscriptions = $subscriptions;
    }

    public function mount(): void
    {
        $this->selectedType = PlanType::Monthly->value;
    }

    public function subscribe(int $planOptionId, SubscribeStudentToPlanOption $action): void
    {
        $this->planOptionId = $planOptionId;
        $this->validate(SubscribeRequest::rules());

        $studentId = AuthActor::studentId();
        abort_if($studentId === null, 403);

        $student = $this->students->getById($studentId, withoutGlobalScopes: true);
        $planOption = $this->planOptions->getById($planOptionId, relations: ['plan']);

        $action->handle($student, $planOption);

        session()->flash('success', 'Subscription activated successfully.');
        $this->redirectRoute('student.subscriptions', navigate: true);
    }

    public function render()
    {
        $hasActive = $this->subscriptions->query()
            ->status(SubscriptionStatus::Active)
            ->where(function ($query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->exists();

        $plans = $this->plans->getWith(
            relations: [
                'options' => fn ($query) => $query->where('is_active', true)->orderBy('duration_months'),
            ],
            conditions: ['is_active' => true],
            orderBy: 'name',
        );

        return view('livewire.students.plans.index', [
            'plans' => $plans,
            'planTypes' => PlanType::cases(),
            'hasActive' => $hasActive,
        ]);
    }
}
