<?php

namespace App\Livewire\Dashboard;

use App\Enums\PlanType;
use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Dashboard\PlanRequest;
use App\Repo\InterFace\PlanOptionRepositoryInterface;
use App\Repo\InterFace\PlanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Title('Plans')]
class Plans extends __AbstractManagerComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    private PlanRepositoryInterface $plans;

    private PlanOptionRepositoryInterface $planOptions;

    #[Url(except: '')]
    public string $active = '';

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    /** @var array<string, array{price: string, is_active: bool}> */
    public array $options = [];

    public function boot(
        PlanRepositoryInterface $plans,
        PlanOptionRepositoryInterface $planOptions,
    ): void {
        $this->plans = $plans;
        $this->planOptions = $planOptions;
    }

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
        $plan = $this->plans->getById($planId, relations: ['options']);

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
                $this->plans->update($this->editingId, $payload);
                $planId = $this->editingId;
            } else {
                $planId = $this->plans->create($payload)->id;
            }

            foreach (PlanType::cases() as $type) {
                $optionData = $validated['options'][(string) $type->value];

                $this->planOptions->updateOrCreate(
                    [
                        'plan_id' => $planId,
                        'type' => $type,
                    ],
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
            $this->plans->delete($this->deletingId);
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
        return view('livewire.dashboard.plans.index', [
            'plans' => $this->plans->forTable(
                scopes: [
                    'search' => [$this->search],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'name', 'is_active'],
                defaultSort: 'name',
                modify: function ($query): void {
                    $query->withCount('options');

                    if ($this->active !== '') {
                        $query->active($this->active === '1');
                    }
                },
            ),
            'planTypes' => PlanType::cases(),
        ]);
    }
}
