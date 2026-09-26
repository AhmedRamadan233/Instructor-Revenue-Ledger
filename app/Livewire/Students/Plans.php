<?php

namespace App\Livewire\Students;

use App\Actions\Subscriptions\SubscribeStudentToPlanOption;
use App\Enums\PlanType;
use App\Enums\SubscriptionStatus;
use App\Livewire\Requests\Students\SubscribeRequest;
use App\Models\Plan;
use App\Models\PlanOption;
use App\Models\Student;
use App\Models\Subscription;
use App\Support\AuthActor;
use Livewire\Attributes\Title;

#[Title('Browse Plans')]
class Plans extends __AbstractStudentComponent
{
    public int $selectedType;

    public int $planOptionId = 0;

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

        $student = Student::query()->withoutGlobalScopes()->findOrFail($studentId);
        $planOption = PlanOption::query()->with('plan')->findOrFail($planOptionId);

        $action->handle($student, $planOption);

        session()->flash('success', 'Subscription activated successfully.');
        $this->redirectRoute('student.subscriptions', navigate: true);
    }

    public function render()
    {
        $hasActive = Subscription::query()
            ->status(SubscriptionStatus::Active)
            ->where(function ($query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->exists();

        $plans = Plan::query()
            ->where('is_active', true)
            ->with(['options' => fn ($query) => $query->where('is_active', true)->orderBy('duration_months')])
            ->orderBy('name')
            ->get();

        return view('livewire.students.plans.index', [
            'plans' => $plans,
            'planTypes' => PlanType::cases(),
            'hasActive' => $hasActive,
        ]);
    }
}
