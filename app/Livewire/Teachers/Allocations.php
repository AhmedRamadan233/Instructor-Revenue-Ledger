<?php

namespace App\Livewire\Teachers;

use App\Livewire\Concerns\InteractsWithTable;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Revenue Allocations')]
class Allocations extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    private RevenueAllocationRepositoryInterface $allocations;

    public function boot(RevenueAllocationRepositoryInterface $allocations): void
    {
        $this->allocations = $allocations;
    }

    public function render()
    {
        return view('livewire.teachers.allocations.index', [
            'allocations' => $this->allocations->forTable(
                relations: ['revenuePeriod', 'subscription'],
                scopes: [
                    'search' => [$this->search],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: [
                    'created_at',
                    'allocated_amount',
                    'consumption_seconds',
                    'currency',
                ],
            ),
        ]);
    }
}
