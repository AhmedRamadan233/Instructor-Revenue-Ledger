<?php

namespace App\Livewire\Guests;

use App\Enums\PlanType;
use App\Models\Plan;
use Livewire\Attributes\Title;

#[Title('Plans')]
class Plans extends __AbstractGuestComponent
{
    public int $selectedType;

    public function mount(): void
    {
        $this->selectedType = PlanType::Monthly->value;
    }

    public function render()
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->with(['options' => fn ($query) => $query->where('is_active', true)->orderBy('duration_months')])
            ->orderBy('name')
            ->get();

        return view('livewire.guests.plans', [
            'plans' => $plans,
            'planTypes' => PlanType::cases(),
        ]);
    }
}
