<?php

namespace App\Livewire\Dashboard;

use App\Enums\CourseStatus;
use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Dashboard\CourseRequest;
use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Title('Courses')]
class Courses extends __AbstractManagerComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    private CourseRepositoryInterface $courses;

    private TeacherRepositoryInterface $teachers;

    #[Url(except: '')]
    public string $status = '';

    public ?int $teacherId = null;

    public string $title = '';

    public string $description = '';

    public string $courseStatus = '';

    public function boot(
        CourseRepositoryInterface $courses,
        TeacherRepositoryInterface $teachers,
    ): void {
        $this->courses = $courses;
        $this->teachers = $teachers;
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
            $this->courses->update($this->editingId, $payload);
        } else {
            $this->courses->create($payload);
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
        $this->reset('editingId', 'teacherId', 'title', 'description', 'courseStatus');
    }

    public function render()
    {
        return view('livewire.dashboard.courses.index', [
            'courses' => $this->courses->forTable(
                relations: ['teacher.user'],
                scopes: [
                    'search' => [$this->search],
                    'status' => [$this->status],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'title', 'status', 'updated_at'],
                defaultSort: 'title',
            ),
            'statuses' => CourseStatus::cases(),
            'teachers' => $this->teachers->getWith(
                relations: ['user'],
                orderBy: 'id',
            ),
        ]);
    }
}
