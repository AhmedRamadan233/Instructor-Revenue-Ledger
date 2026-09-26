@props([
    'message' => 'Are you sure you want to delete this record?',
])

<div
    class="modal fade"
    :class="{ show: confirmOpen, 'd-block': confirmOpen }"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
    x-show="confirmOpen"
    x-cloak
    x-transition.opacity
>
    <div class="modal-dialog modal-dialog-centered modal-sm" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5">Confirm delete</h2>
                <button type="button" class="btn-close" wire:click="closeDeleteModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ $message }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" wire:click="closeDeleteModal">Cancel</button>
                <button
                    type="button"
                    class="btn btn-danger"
                    wire:click="delete"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="delete">Delete</span>
                    <span wire:loading wire:target="delete">Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
