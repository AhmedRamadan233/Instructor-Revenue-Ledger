<?php

namespace App\Livewire\Teachers;

use App\Enums\CourseStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Course;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('My Courses')]
class Courses extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    #[Url(except: '')]
    public string $status = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function render()
    {
        $query = Course::query()
            ->search($this->search)
            ->status($this->status);

        $this->applySorting($query, [
            'created_at',
            'title',
            'status',
            'updated_at',
        ], 'title');

        return view('livewire.teachers.courses', [
            'courses' => $query->paginate(10),
            'statuses' => CourseStatus::cases(),
        ]);
    }
}
