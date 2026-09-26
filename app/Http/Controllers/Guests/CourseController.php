<?php

namespace App\Http\Controllers\Guests;

use App\Enums\CourseStatus;
use App\Http\Controllers\__AbstractGuestController;
use App\Models\Course;
use Illuminate\View\View;

class CourseController extends __AbstractGuestController
{
    public function __invoke(): View
    {
        $courses = Course::query()
            ->withoutGlobalScopes()
            ->where('status', CourseStatus::Published)
            ->with([
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ])
            ->latest()
            ->get();

        return view('guests.courses', [
            'courses' => $courses,
        ]);
    }
}
