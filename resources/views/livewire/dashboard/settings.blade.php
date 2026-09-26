<div
    x-data="{ open: @entangle('showModal') }"
    @keydown.escape.window="if (open) $wire.closeModal()"
>
    <div class="mb-4">
        <h1 class="h3 mb-1">Settings</h1>
        <p class="text-muted mb-0">Update platform configuration, including revenue percentage.</p>
    </div>

    @if ($settings->isEmpty())
        <div class="alert alert-secondary mb-0">No settings found.</div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Key</th>
                            <th scope="col">Value</th>
                            <th scope="col">Type</th>
                            <th scope="col" class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($settings as $setting)
                            <tr wire:key="setting-{{ $setting->id }}">
                                <td>
                                    <code>{{ $setting->key }}</code>
                                    @if ($setting->key === 'platform_revenue_percentage')
                                        <span class="badge text-bg-info ms-1">Revenue %</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $setting->value }}@if ($setting->key === 'platform_revenue_percentage')%
                                    @endif
                                </td>
                                <td>{{ $setting->type->name }}</td>
                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="edit({{ $setting->id }})"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Edit modal (Alpine + Bootstrap markup) --}}
    <div
        class="modal fade"
        :class="{ show: open, 'd-block': open }"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
        x-show="open"
        x-cloak
        x-transition.opacity
    >
        <div class="modal-dialog modal-dialog-centered" @click.stop>
            <div class="modal-content" wire:key="edit-modal-{{ $editingId }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5">Edit Setting</h2>
                    <button
                        type="button"
                        class="btn-close"
                        aria-label="Close"
                        wire:click="closeModal"
                    ></button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Key</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $editingKey }}"
                                disabled
                                readonly
                            >
                        </div>

                        <div class="mb-0">
                            <label for="editingValue" class="form-label">Value</label>
                            <div class="input-group">
                                <input
                                    id="editingValue"
                                    type="text"
                                    class="form-control @error('editingValue') is-invalid @enderror"
                                    wire:model="editingValue"
                                    autofocus
                                >
                                @if ($editingKey === 'platform_revenue_percentage')
                                    <span class="input-group-text">%</span>
                                @endif
                            </div>
                            @error('editingValue')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if ($editingKey === 'platform_revenue_percentage')
                                <div class="form-text">
                                    Enter a number from 0 to 100. Snapshot is taken when a subscription is created.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            wire:click="closeModal"
                            wire:loading.attr="disabled"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="save">Save changes</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div
        class="modal-backdrop fade"
        :class="{ show: open }"
        x-show="open"
        x-cloak
        x-transition.opacity
        wire:click="closeModal"
    ></div>
</div>
