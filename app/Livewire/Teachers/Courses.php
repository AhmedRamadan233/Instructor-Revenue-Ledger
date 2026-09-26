<?php

namespace App\Livewire\Teachers;

use App\Enums\CourseStatus;
use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Teachers\CourseRequest;
use App\Repo\InterFace\CourseRepositoryInterface;
use App\Support\AuthActor;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Title('My Courses')]
class Courses extends __AbstractTeacherComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    private CourseRepositoryInterface $courses;

    #[Url(except: '')]
    public string $status = '';

    public string $title = '';

    public string $description = '';

    public string $courseStatus = '';

    public function boot(CourseRepositoryInterface $courses): void
    {
        $this->courses = $courses;
    }

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
        $course = $this->courses->getById($courseId);

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

        $validated = $this->validate(CourseRequest::rules());

        $isEditing = $this->editingId !== null;

        $payload = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'status' => CourseStatus::from((int) $validated['courseStatus']),
        ];

        if ($this->editingId) {
            $this->courses->update($this->editingId, $payload);
        } else {
            $this->courses->create([
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
            $this->courses->delete($this->deletingId);
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
        return view('livewire.teachers.courses.index', [
            'courses' => $this->courses->forTable(
                scopes: [
                    'search' => [$this->search],
                    'status' => [$this->status],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'title', 'status', 'updated_at'],
                defaultSort: 'title',
                modify: fn ($query) => $query->withCount('students'),
            ),
            'statuses' => CourseStatus::cases(),
        ]);
    }
}
