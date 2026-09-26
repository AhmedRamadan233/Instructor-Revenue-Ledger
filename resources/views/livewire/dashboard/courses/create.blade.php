<x-dashboard.form-modal :title="$editingId ? 'Edit Course' : 'Add Course'">
    <div class="mb-3">
        <label class="form-label" for="course-teacher">Teacher</label>
        <select id="course-teacher" class="form-select @error('teacherId') is-invalid @enderror" wire:model="teacherId">
            <option value="">Select teacher</option>
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}">{{ $teacher->user?->name ?? 'Teacher #'.$teacher->id }}</option>
            @endforeach
        </select>
        @error('teacherId') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="course-title">Title</label>
        <input id="course-title" type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="course-description">Description</label>
        <textarea id="course-description" rows="3" class="form-control @error('description') is-invalid @enderror" wire:model="description"></textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-0">
        <label class="form-label" for="course-status">Status</label>
        <select id="course-status" class="form-select @error('courseStatus') is-invalid @enderror" wire:model="courseStatus">
            @foreach ($statuses as $item)
                <option value="{{ $item->value }}">{{ $item->name }}</option>
            @endforeach
        </select>
        @error('courseStatus') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</x-dashboard.form-modal>
