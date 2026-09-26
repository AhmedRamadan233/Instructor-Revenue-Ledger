<?php

namespace App\Livewire\Teachers;

use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Course;
use App\Models\Student;
use Livewire\Attributes\Title;

#[Title('Course Students')]
class CourseStudents extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    public Course $course;

    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    public function render()
    {
        $query = Student::query()
            ->with('user')
            ->forCourse($this->course->id)
            ->withCount([
                'consumptionSessions as sessions_count' => function ($sessionQuery): void {
                    $sessionQuery->where('course_id', $this->course->id);
                },
            ])
            ->search($this->search);

        $this->applySorting($query, ['created_at', 'id']);

        return view('livewire.teachers.course-students.index', [
            'course' => $this->course,
            'students' => $query->paginate(10),
        ]);
    }
}
