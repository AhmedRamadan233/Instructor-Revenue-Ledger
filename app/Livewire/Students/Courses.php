<?php

namespace App\Livewire\Students;

use App\Livewire\Concerns\InteractsWithTable;
use App\Repo\InterFace\CourseRepositoryInterface;
use App\Support\AuthActor;
use Livewire\Attributes\Title;

#[Title('Courses')]
class Courses extends __AbstractStudentComponent
{
    use InteractsWithTable;

    private CourseRepositoryInterface $courses;

    public function boot(CourseRepositoryInterface $courses): void
    {
        $this->courses = $courses;
    }

    public function render()
    {
        $studentId = AuthActor::studentId();

        return view('livewire.students.courses.index', [
            'courses' => $this->courses->forTable(
                relations: [
                    'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
                ],
                scopes: [
                    'published' => [],
                    'search' => [$this->search],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: [
                    'created_at',
                    'title',
                    'updated_at',
                ],
                defaultSort: 'title',
                modify: function ($query) use ($studentId): void {
                    $query->withSum([
                        'consumptionSessions as watched_seconds' => function ($sessionQuery) use ($studentId): void {
                            if ($studentId === null) {
                                $sessionQuery->whereRaw('0 = 1');

                                return;
                            }

                            $sessionQuery->where('student_id', $studentId);
                        },
                    ], 'watch_seconds');
                },
            ),
        ]);
    }
}
