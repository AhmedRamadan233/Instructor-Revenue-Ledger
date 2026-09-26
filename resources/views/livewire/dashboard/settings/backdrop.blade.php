<div
    class="modal-backdrop fade"
    :class="{ show: open }"
    x-show="open"
    x-cloak
    x-transition.opacity
    wire:click="closeModal"
></div>
