<x-dashboard.form-modal :title="$editingId ? 'Edit Teacher' : 'Add Teacher'">
    <div class="mb-3">
        <label class="form-label" for="teacher-name">Name</label>
        <input id="teacher-name" type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="teacher-email">Email</label>
        <input id="teacher-email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-0">
        <label class="form-label" for="teacher-password">
            Password
            @if ($editingId)
                <span class="text-muted fw-normal">(leave blank to keep)</span>
            @endif
        </label>
        <input id="teacher-password" type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" autocomplete="new-password">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</x-dashboard.form-modal>
