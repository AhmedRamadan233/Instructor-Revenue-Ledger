<?php

namespace App\Livewire\Guests;

use App\Enums\PlanType;
use App\Repo\InterFace\PlanRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Plans')]
class Plans extends __AbstractGuestComponent
{
    private PlanRepositoryInterface $plans;

    public int $selectedType;

    public function boot(PlanRepositoryInterface $plans): void
    {
        $this->plans = $plans;
    }

    public function mount(): void
    {
        $this->selectedType = PlanType::Monthly->value;
    }

    public function render()
    {
        $plans = $this->plans->getWith(
            relations: [
                'options' => fn ($query) => $query->where('is_active', true)->orderBy('duration_months'),
            ],
            conditions: ['is_active' => true],
            orderBy: 'name',
        );

        return view('livewire.guests.plans', [
            'plans' => $plans,
            'planTypes' => PlanType::cases(),
        ]);
    }
}
