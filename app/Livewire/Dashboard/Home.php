<?php

namespace App\Livewire\Dashboard;

use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\PlanRepositoryInterface;
use App\Repo\InterFace\SettingRepositoryInterface;
use App\Repo\InterFace\StudentRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Manager Dashboard')]
class Home extends __AbstractManagerComponent
{
    private SettingRepositoryInterface $settings;

    private StudentRepositoryInterface $students;

    private TeacherRepositoryInterface $teachers;

    private PlanRepositoryInterface $plans;

    private CourseRepositoryInterface $courses;

    public function boot(
        SettingRepositoryInterface $settings,
        StudentRepositoryInterface $students,
        TeacherRepositoryInterface $teachers,
        PlanRepositoryInterface $plans,
        CourseRepositoryInterface $courses,
    ): void {
        $this->settings = $settings;
        $this->students = $students;
        $this->teachers = $teachers;
        $this->plans = $plans;
        $this->courses = $courses;
    }

    public function render()
    {
        $platformPercentage = (float) ($this->settings->first('key', 'platform_revenue_percentage')?->value ?? 20);

        return view('livewire.dashboard.home.index', [
            'studentsCount' => $this->students->count(withoutGlobalScopes: true),
            'teachersCount' => $this->teachers->count(withoutGlobalScopes: true),
            'plansCount' => $this->plans->count(['is_active' => true]),
            'coursesCount' => $this->courses->count(withoutGlobalScopes: true),
            'platformPercentage' => $platformPercentage,
            'teacherPoolPercentage' => max(0, 100 - $platformPercentage),
            'recentCourses' => $this->courses->take(
                limit: 5,
                relations: [
                    'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
                ],
                orderBy: 'created_at',
                direction: 'desc',
                withoutGlobalScopes: true,
            ),
        ]);
    }
}
