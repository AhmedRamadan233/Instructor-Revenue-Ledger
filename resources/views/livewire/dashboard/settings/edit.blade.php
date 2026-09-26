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
