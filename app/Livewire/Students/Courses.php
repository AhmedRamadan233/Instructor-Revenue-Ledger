<?php

namespace App\Livewire\Students;

use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Course;
use Livewire\Attributes\Title;

#[Title('Courses')]
class Courses extends __AbstractStudentComponent
{
    use InteractsWithTable;

    public function render()
    {
        $query = Course::query()
            ->published()
            ->with([
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ])
            ->search($this->search);

        $this->applySorting($query, [
            'created_at',
            'title',
            'updated_at',
        ], 'title');

        return view('livewire.students.courses', [
            'courses' => $query->paginate(10),
        ]);
    }
}
