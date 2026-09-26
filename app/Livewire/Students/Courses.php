<?php

namespace App\Livewire\Students;

use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Course;
use App\Support\AuthActor;
use Livewire\Attributes\Title;

#[Title('Courses')]
class Courses extends __AbstractStudentComponent
{
    use InteractsWithTable;

    public function render()
    {
        $studentId = AuthActor::studentId();

        $query = Course::query()
            ->published()
            ->with([
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ])
            ->withSum([
                'consumptionSessions as watched_seconds' => function ($sessionQuery) use ($studentId): void {
                    if ($studentId === null) {
                        $sessionQuery->whereRaw('0 = 1');

                        return;
                    }

                    $sessionQuery->where('student_id', $studentId);
                },
            ], 'watch_seconds')
            ->search($this->search);

        $this->applySorting($query, [
            'created_at',
            'title',
            'updated_at',
        ], 'title');

        return view('livewire.students.courses.index', [
            'courses' => $query->paginate(10),
        ]);
    }
}
