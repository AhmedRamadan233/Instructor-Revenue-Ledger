<div
    x-data="{
        open: @entangle('showModal'),
        confirmOpen: @entangle('showDeleteModal'),
    }"
    @keydown.escape.window="if (open) $wire.closeModal(); if (confirmOpen) $wire.closeDeleteModal()"
>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Teachers</h1>
            <p class="text-muted mb-0">Create and manage teacher accounts.</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="create">Add Teacher</button>
    </div>

    <x-table.toolbar search-placeholder="Search name or email..." />

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Courses</th>
                        <th>
                            <x-table.sort-button column="created_at" label="Created" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th class="text-end" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr wire:key="teacher-{{ $teacher->id }}">
                            <td class="fw-semibold">{{ $teacher->user?->name ?? '—' }}</td>
                            <td>{{ $teacher->user?->email ?? '—' }}</td>
                            <td>{{ $teacher->courses_count }}</td>
                            <td>{{ optional($teacher->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $teacher->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $teacher->id }})">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No teachers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($teachers->hasPages())
            <div class="card-footer">{{ $teachers->links() }}</div>
        @endif
    </div>

    @include('livewire.dashboard.teachers.create')
    @include('livewire.dashboard.teachers.delete')
    @include('livewire.dashboard.teachers.backdrop')
</div>
