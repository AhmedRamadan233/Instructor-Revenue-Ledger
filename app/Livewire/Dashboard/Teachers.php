<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Dashboard\TeacherRequest;
use App\Repo\InterFace\TeacherRepositoryInterface;
use App\Repo\InterFace\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Throwable;

#[Title('Teachers')]
class Teachers extends __AbstractManagerComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    private TeacherRepositoryInterface $teachers;

    private UserRepositoryInterface $users;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public function boot(
        TeacherRepositoryInterface $teachers,
        UserRepositoryInterface $users,
    ): void {
        $this->teachers = $teachers;
        $this->users = $users;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->resetValidation();
    }

    public function edit(int $teacherId): void
    {
        $teacher = $this->teachers->getById($teacherId, relations: ['user']);

        $this->editingId = $teacher->id;
        $this->name = $teacher->user?->name ?? '';
        $this->email = $teacher->user?->email ?? '';
        $this->password = '';
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $userId = $this->editingId
            ? $this->teachers->getById($this->editingId)->user_id
            : null;

        $validated = $this->validate(TeacherRequest::rules(
            ignoreUserId: $userId,
            passwordRequired: $this->editingId === null,
        ));

        $isEditing = $this->editingId !== null;

        DB::transaction(function () use ($validated): void {
            if ($this->editingId) {
                $teacher = $this->teachers->getById($this->editingId);

                $payload = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];

                if (! empty($validated['password'])) {
                    $payload['password'] = $validated['password'];
                }

                $this->users->update($teacher->user_id, $payload);
            } else {
                $user = $this->users->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);

                $this->teachers->create([
                    'user_id' => $user->id,
                ]);
            }
        });

        $this->closeModal();
        session()->flash('success', $isEditing ? 'Teacher updated.' : 'Teacher created.');
    }

    public function delete(): void
    {
        $teacher = $this->teachers->getById($this->deletingId, relations: ['user']);

        try {
            DB::transaction(function () use ($teacher): void {
                $userId = $teacher->user_id;
                $this->teachers->delete($teacher->id);
                if ($userId) {
                    $this->users->delete($userId);
                }
            });

            $this->closeDeleteModal();
            session()->flash('success', 'Teacher deleted.');
        } catch (Throwable) {
            $this->closeDeleteModal();
            session()->flash('error', 'Cannot delete this teacher because related revenue records exist.');
        }
    }

    protected function resetForm(): void
    {
        $this->reset('editingId', 'name', 'email', 'password');
    }

    public function render()
    {
        return view('livewire.dashboard.teachers.index', [
            'teachers' => $this->teachers->forTable(
                relations: ['user'],
                scopes: [
                    'search' => [$this->search],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'id'],
                modify: fn ($query) => $query->withCount('courses'),
            ),
        ]);
    }
}
