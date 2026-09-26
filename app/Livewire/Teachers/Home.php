<?php

namespace App\Livewire\Teachers;

use App\Models\Course;
use App\Models\RevenueAllocation;
use App\Models\TeacherLedgerEntry;
use Livewire\Attributes\Title;

#[Title('Teacher Dashboard')]
class Home extends __AbstractTeacherComponent
{
    public function render()
    {
        return view('livewire.teachers.home', [
            'courses' => Course::query()->latest()->get(),
            'allocations' => RevenueAllocation::query()
                ->with(['revenuePeriod', 'subscription'])
                ->latest()
                ->get(),
            'ledgerEntries' => TeacherLedgerEntry::query()
                ->with('revenuePeriod')
                ->latest('created_at')
                ->get(),
        ]);
    }
}
