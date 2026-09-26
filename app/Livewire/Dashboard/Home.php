<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use Livewire\Attributes\Title;

#[Title('Manager Dashboard')]
class Home extends __AbstractManagerComponent
{
    public function render()
    {
        $platformPercentage = (float) (Setting::query()
            ->where('key', 'platform_revenue_percentage')
            ->value('value') ?? 20);

        return view('livewire.dashboard.home.index', [
            'studentsCount' => Student::query()->withoutGlobalScopes()->count(),
            'teachersCount' => Teacher::query()->withoutGlobalScopes()->count(),
            'plansCount' => Plan::query()->where('is_active', true)->count(),
            'coursesCount' => Course::query()->withoutGlobalScopes()->count(),
            'platformPercentage' => $platformPercentage,
            'teacherPoolPercentage' => max(0, 100 - $platformPercentage),
            'recentCourses' => Course::query()
                ->withoutGlobalScopes()
                ->with(['teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user')])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
