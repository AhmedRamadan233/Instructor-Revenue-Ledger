<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\InteractsWithCrudModal;
use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Throwable;

#[Title('Teachers')]
class Teachers extends __AbstractManagerComponent
{
    use InteractsWithCrudModal;
    use InteractsWithTable;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->resetValidation();
    }

    public function edit(int $teacherId): void
    {
        $teacher = Teacher::query()->with('user')->findOrFail($teacherId);

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
            ? Teacher::query()->findOrFail($this->editingId)->user_id
            : null;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $this->editingId ? 'nullable' : 'required',
                'string',
                Password::defaults(),
            ],
        ]);

        $isEditing = $this->editingId !== null;

        DB::transaction(function () use ($validated): void {
            if ($this->editingId) {
                $teacher = Teacher::query()->findOrFail($this->editingId);
                $user = User::query()->findOrFail($teacher->user_id);

                $payload = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];

                if (! empty($validated['password'])) {
                    $payload['password'] = $validated['password'];
                }

                $user->update($payload);
            } else {
                $user = User::query()->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);

                Teacher::query()->create([
                    'user_id' => $user->id,
                ]);
            }
        });

        $this->closeModal();
        session()->flash('success', $isEditing ? 'Teacher updated.' : 'Teacher created.');
    }

    public function delete(): void
    {
        $teacher = Teacher::query()->with('user')->findOrFail($this->deletingId);

        try {
            DB::transaction(function () use ($teacher): void {
                $user = $teacher->user;
                $teacher->delete();
                $user?->delete();
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
        $query = Teacher::query()
            ->with('user')
            ->withCount('courses')
            ->search($this->search);

        $this->applySorting($query, ['created_at', 'id']);

        return view('livewire.dashboard.teachers.index', [
            'teachers' => $query->paginate(10),
        ]);
    }
}
