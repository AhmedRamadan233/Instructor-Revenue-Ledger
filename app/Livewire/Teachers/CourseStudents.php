<?php

namespace App\Livewire\Teachers;

use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Course;
use App\Repo\InterFace\StudentRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Course Students')]
class CourseStudents extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    private StudentRepositoryInterface $students;

    public Course $course;

    public function boot(StudentRepositoryInterface $students): void
    {
        $this->students = $students;
    }

    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    public function render()
    {
        return view('livewire.teachers.course-students.index', [
            'course' => $this->course,
            'students' => $this->students->forTable(
                relations: ['user'],
                scopes: [
                    'forCourse' => [$this->course->id],
                    'search' => [$this->search],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'id'],
                modify: function ($query): void {
                    $query->withCount([
                        'consumptionSessions as sessions_count' => function ($sessionQuery): void {
                            $sessionQuery->where('course_id', $this->course->id);
                        },
                    ]);
                },
            ),
        ]);
    }
}
