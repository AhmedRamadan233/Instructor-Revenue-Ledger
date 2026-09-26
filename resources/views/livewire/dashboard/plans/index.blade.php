<div
    x-data="{
        open: @entangle('showModal'),
        confirmOpen: @entangle('showDeleteModal'),
    }"
    @keydown.escape.window="if (open) $wire.closeModal(); if (confirmOpen) $wire.closeDeleteModal()"
>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Plans</h1>
            <p class="text-muted mb-0">Manage subscription plans and billing options.</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="create">Add Plan</button>
    </div>

    <x-table.toolbar
        search-placeholder="Search plan name or description..."
        :has-active-filters="$active !== ''"
    >
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-1" for="active-filter">Active</label>
            <select id="active-filter" class="form-select" wire:model.live="active">
                <option value="">All</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    </x-table.toolbar>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <x-table.sort-button column="name" label="Name" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Description</th>
                        <th>Options</th>
                        <th>
                            <x-table.sort-button column="is_active" label="Active" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th class="text-end" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr wire:key="plan-{{ $plan->id }}">
                            <td class="fw-semibold">{{ $plan->name }}</td>
                            <td class="text-muted small">{{ \Illuminate\Support\Str::limit($plan->description ?: '—', 70) }}</td>
                            <td>{{ $plan->options_count }}</td>
                            <td>
                                <span class="badge {{ $plan->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $plan->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $plan->id }})">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($plans->hasPages())
            <div class="card-footer">{{ $plans->links() }}</div>
        @endif
    </div>

    @include('livewire.dashboard.plans.create')
    @include('livewire.dashboard.plans.delete')
    @include('livewire.dashboard.plans.backdrop')
</div>
