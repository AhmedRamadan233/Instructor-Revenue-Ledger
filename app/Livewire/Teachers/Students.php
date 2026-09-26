<?php

namespace App\Livewire\Teachers;

use App\Livewire\Concerns\InteractsWithTable;
use App\Repo\InterFace\StudentRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('My Students')]
class Students extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    private StudentRepositoryInterface $students;

    public function boot(StudentRepositoryInterface $students): void
    {
        $this->students = $students;
    }

    public function render()
    {
        return view('livewire.teachers.students.index', [
            'students' => $this->students->forTable(
                relations: ['user'],
                scopes: [
                    'search' => [$this->search],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'id'],
                modify: function ($query): void {
                    $query->withCount([
                        'consumptionSessions as sessions_count' => function ($sessionQuery): void {
                            $sessionQuery->whereHas('course');
                        },
                    ]);
                },
            ),
        ]);
    }
}
