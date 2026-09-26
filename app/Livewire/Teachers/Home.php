<?php

namespace App\Livewire\Teachers;

use App\Models\Course;
use App\Models\RevenueAllocation;
use App\Models\Student;
use App\Models\TeacherLedgerEntry;
use Livewire\Attributes\Title;

#[Title('Teacher Dashboard')]
class Home extends __AbstractTeacherComponent
{
    public function render()
    {
        return view('livewire.teachers.home.index', [
            'coursesCount' => Course::query()->count(),
            'studentsCount' => Student::query()->count(),
            'allocationsCount' => RevenueAllocation::query()->count(),
            'ledgerCount' => TeacherLedgerEntry::query()->count(),
            'totalAllocated' => (float) RevenueAllocation::query()->sum('allocated_amount'),
            'recentCourses' => Course::query()
                ->withCount('students')
                ->latest()
                ->limit(3)
                ->get(),
            'recentAllocations' => RevenueAllocation::query()
                ->with('revenuePeriod')
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }
}
