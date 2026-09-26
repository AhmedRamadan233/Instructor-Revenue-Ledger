<?php

namespace App\Livewire\Dashboard;

use App\Enums\PlanType;
use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Dashboard\PlanRequest;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Title('Plans')]
class Plans extends __AbstractManagerComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    #[Url(except: '')]
    public string $active = '';

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    /** @var array<string, array{price: string, is_active: bool}> */
    public array $options = [];

    public function mount(): void
    {
        $this->resetOptions();
    }

    public function updatedActive(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'active');
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isActive = true;
        $this->resetOptions();
        $this->showModal = true;
        $this->resetValidation();
    }

    public function edit(int $planId): void
    {
        $plan = Plan::query()->with('options')->findOrFail($planId);

        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->description = (string) $plan->description;
        $this->isActive = $plan->is_active;
        $this->resetOptions();

        foreach ($plan->options as $option) {
            $this->options[(string) $option->type->value] = [
                'price' => (string) $option->price,
                'is_active' => $option->is_active,
            ];
        }

        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate(PlanRequest::rules());

        $isEditing = $this->editingId !== null;

        DB::transaction(function () use ($validated): void {
            $payload = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'is_active' => $validated['isActive'],
            ];

            if ($this->editingId) {
                $plan = Plan::query()->findOrFail($this->editingId);
                $plan->update($payload);
            } else {
                $plan = Plan::query()->create($payload);
            }

            foreach (PlanType::cases() as $type) {
                $optionData = $validated['options'][(string) $type->value];

                $plan->options()->updateOrCreate(
                    ['type' => $type],
                    [
                        'price' => $optionData['price'],
                        'currency' => 'EGP',
                        'duration_months' => $type->durationMonths(),
                        'is_active' => (bool) $optionData['is_active'],
                    ]
                );
            }
        });

        $this->closeModal();
        session()->flash('success', $isEditing ? 'Plan updated.' : 'Plan created.');
    }

    public function delete(): void
    {
        try {
            Plan::query()->findOrFail($this->deletingId)->delete();
            $this->closeDeleteModal();
            session()->flash('success', 'Plan deleted.');
        } catch (Throwable) {
            $this->closeDeleteModal();
            session()->flash('error', 'Cannot delete this plan because it has related subscriptions.');
        }
    }

    protected function resetForm(): void
    {
        $this->reset('editingId', 'name', 'description', 'isActive');
        $this->resetOptions();
    }

    protected function resetOptions(): void
    {
        $this->options = [];

        foreach (PlanType::cases() as $type) {
            $this->options[(string) $type->value] = [
                'price' => '',
                'is_active' => true,
            ];
        }
    }

    public function render()
    {
        $query = Plan::query()
            ->withCount('options')
            ->search($this->search);

        if ($this->active !== '') {
            $query->active($this->active === '1');
        }

        $this->applySorting($query, ['created_at', 'name', 'is_active'], 'name');

        return view('livewire.dashboard.plans.index', [
            'plans' => $query->paginate(10),
            'planTypes' => PlanType::cases(),
        ]);
    }
}
