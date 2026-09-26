<?php

namespace App\Livewire\Teachers;

use App\Livewire\Concerns\InteractsWithTable;
use App\Models\RevenueAllocation;
use Livewire\Attributes\Title;

#[Title('Revenue Allocations')]
class Allocations extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    public function render()
    {
        $query = RevenueAllocation::query()
            ->with(['revenuePeriod', 'subscription'])
            ->search($this->search);

        $this->applySorting($query, [
            'created_at',
            'allocated_amount',
            'consumption_seconds',
            'currency',
        ]);

        return view('livewire.teachers.allocations.index', [
            'allocations' => $query->paginate(10),
        ]);
    }
}
