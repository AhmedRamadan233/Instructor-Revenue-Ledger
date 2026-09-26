<?php

namespace App\Livewire\Teachers;

use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Student;
use Livewire\Attributes\Title;

#[Title('My Students')]
class Students extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    public function render()
    {
        $query = Student::query()
            ->with('user')
            ->withCount([
                'consumptionSessions as sessions_count' => function ($sessionQuery): void {
                    $sessionQuery->whereHas('course');
                },
            ])
            ->search($this->search);

        $this->applySorting($query, ['created_at', 'id']);

        return view('livewire.teachers.students.index', [
            'students' => $query->paginate(10),
        ]);
    }
}
