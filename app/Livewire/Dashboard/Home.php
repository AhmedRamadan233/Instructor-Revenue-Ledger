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
        return view('livewire.dashboard.home', [
            'studentsCount' => Student::query()->withoutGlobalScopes()->count(),
            'teachersCount' => Teacher::query()->withoutGlobalScopes()->count(),
            'plansCount' => Plan::query()->where('is_active', true)->count(),
            'coursesCount' => Course::query()->withoutGlobalScopes()->count(),
            'settings' => Setting::query()->orderBy('key')->get(),
            'recentCourses' => Course::query()
                ->withoutGlobalScopes()
                ->with(['teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user')])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
