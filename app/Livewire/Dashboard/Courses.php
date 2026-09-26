<?php

namespace App\Livewire\Dashboard;

use App\Enums\CourseStatus;
use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Dashboard\CourseRequest;
use App\Models\Course;
use App\Models\Teacher;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Title('Courses')]
class Courses extends __AbstractManagerComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    #[Url(except: '')]
    public string $status = '';

    public ?int $teacherId = null;

    public string $title = '';

    public string $description = '';

    public string $courseStatus = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->courseStatus = (string) CourseStatus::Draft->value;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function edit(int $courseId): void
    {
        $course = Course::query()->findOrFail($courseId);

        $this->editingId = $course->id;
        $this->teacherId = $course->teacher_id;
        $this->title = $course->title;
        $this->description = (string) $course->description;
        $this->courseStatus = (string) $course->status->value;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate(CourseRequest::rules());

        $isEditing = $this->editingId !== null;

        $payload = [
            'teacher_id' => $validated['teacherId'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'status' => CourseStatus::from((int) $validated['courseStatus']),
        ];

        if ($this->editingId) {
            Course::query()->findOrFail($this->editingId)->update($payload);
        } else {
            Course::query()->create($payload);
        }

        $this->closeModal();
        session()->flash('success', $isEditing ? 'Course updated.' : 'Course created.');
    }

    public function delete(): void
    {
        try {
            Course::query()->findOrFail($this->deletingId)->delete();
            $this->closeDeleteModal();
            session()->flash('success', 'Course deleted.');
        } catch (Throwable) {
            $this->closeDeleteModal();
            session()->flash('error', 'Cannot delete this course because related records exist.');
        }
    }

    protected function resetForm(): void
    {
        $this->reset('editingId', 'teacherId', 'title', 'description', 'courseStatus');
    }

    public function render()
    {
        $query = Course::query()
            ->with(['teacher.user'])
            ->search($this->search)
            ->status($this->status);

        $this->applySorting($query, [
            'created_at',
            'title',
            'status',
            'updated_at',
        ], 'title');

        return view('livewire.dashboard.courses.index', [
            'courses' => $query->paginate(10),
            'statuses' => CourseStatus::cases(),
            'teachers' => Teacher::query()
                ->with('user')
                ->orderBy('id')
                ->get(),
        ]);
    }
}
