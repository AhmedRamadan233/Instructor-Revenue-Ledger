<?php

namespace App\Livewire\Teachers;

use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\SettingRepositoryInterface;
use App\Repo\InterFace\StudentRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Teacher Dashboard')]
class Home extends __AbstractTeacherComponent
{
    private SettingRepositoryInterface $settings;

    private CourseRepositoryInterface $courses;

    private StudentRepositoryInterface $students;

    private RevenueAllocationRepositoryInterface $allocations;

    private TeacherLedgerEntryRepositoryInterface $ledgerEntries;

    public function boot(
        SettingRepositoryInterface $settings,
        CourseRepositoryInterface $courses,
        StudentRepositoryInterface $students,
        RevenueAllocationRepositoryInterface $allocations,
        TeacherLedgerEntryRepositoryInterface $ledgerEntries,
    ): void {
        $this->settings = $settings;
        $this->courses = $courses;
        $this->students = $students;
        $this->allocations = $allocations;
        $this->ledgerEntries = $ledgerEntries;
    }

    public function render()
    {
        $platformPercentage = (float) ($this->settings->first(
            'key',
            'platform_revenue_percentage',
            withoutGlobalScopes: true,
        )?->value ?? 20);

        return view('livewire.teachers.home.index', [
            'platformPercentage' => $platformPercentage,
            'teacherPoolPercentage' => max(0, 100 - $platformPercentage),
            'coursesCount' => $this->courses->count(),
            'studentsCount' => $this->students->count(),
            'allocationsCount' => $this->allocations->count(),
            'ledgerCount' => $this->ledgerEntries->count(),
            'totalAllocated' => $this->allocations->sum('allocated_amount'),
            'recentCourses' => $this->courses->getWith(
                orderBy: 'created_at',
                direction: 'desc',
                modify: fn ($query) => $query->withCount('students'),
                limit: 3,
            ),
            'recentAllocations' => $this->allocations->getWith(
                relations: ['revenuePeriod'],
                orderBy: 'created_at',
                direction: 'desc',
                limit: 3,
            ),
        ]);
    }
}
