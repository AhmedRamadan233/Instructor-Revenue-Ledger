<div
    x-data="{
        open: @entangle('showModal'),
        confirmOpen: @entangle('showDeleteModal'),
    }"
    @keydown.escape.window="if (open) $wire.closeModal(); if (confirmOpen) $wire.closeDeleteModal()"
>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Courses</h1>
            <p class="text-muted mb-0">Assign courses to teachers and manage their status.</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="create">Add Course</button>
    </div>

    <x-table.toolbar
        search-placeholder="Search title or description..."
        :has-active-filters="$status !== ''"
    >
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-1" for="status-filter">Status</label>
            <select id="status-filter" class="form-select" wire:model.live="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $item)
                    <option value="{{ $item->value }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </x-table.toolbar>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <x-table.sort-button column="title" label="Title" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Teacher</th>
                        <th>
                            <x-table.sort-button column="status" label="Status" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="created_at" label="Created" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th class="text-end" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr wire:key="course-{{ $course->id }}">
                            <td>
                                <div class="fw-semibold">{{ $course->title }}</div>
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($course->description ?: '—', 60) }}</div>
                            </td>
                            <td>{{ $course->teacher?->user?->name ?? '—' }}</td>
                            <td><span class="badge text-bg-secondary">{{ $course->status->name }}</span></td>
                            <td>{{ optional($course->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $course->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $course->id }})">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($courses->hasPages())
            <div class="card-footer">{{ $courses->links() }}</div>
        @endif
    </div>

    @include('livewire.dashboard.courses.create')
    @include('livewire.dashboard.courses.delete')
    @include('livewire.dashboard.courses.backdrop')
</div>
