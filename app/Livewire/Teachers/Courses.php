<?php

namespace App\Livewire\Teachers;

use App\Enums\CourseStatus;
use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Course;
use App\Support\AuthActor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Title('My Courses')]
class Courses extends __AbstractTeacherComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    #[Url(except: '')]
    public string $status = '';

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
        $this->title = $course->title;
        $this->description = (string) $course->description;
        $this->courseStatus = (string) $course->status->value;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $teacherId = AuthActor::teacherId();

        abort_if($teacherId === null, 403);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'courseStatus' => ['required', Rule::enum(CourseStatus::class)],
        ]);

        $isEditing = $this->editingId !== null;

        $payload = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'status' => CourseStatus::from((int) $validated['courseStatus']),
        ];

        if ($this->editingId) {
            Course::query()->findOrFail($this->editingId)->update($payload);
        } else {
            Course::query()->create([
                ...$payload,
                'teacher_id' => $teacherId,
            ]);
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
        $this->reset('editingId', 'title', 'description', 'courseStatus');
    }

    public function render()
    {
        $query = Course::query()
            ->withCount('students')
            ->search($this->search)
            ->status($this->status);

        $this->applySorting($query, [
            'created_at',
            'title',
            'status',
            'updated_at',
        ], 'title');

        return view('livewire.teachers.courses.index', [
            'courses' => $query->paginate(10),
            'statuses' => CourseStatus::cases(),
        ]);
    }
}
