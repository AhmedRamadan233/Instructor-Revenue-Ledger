<div
    class="modal-backdrop fade"
    :class="{ show: open || confirmOpen }"
    x-show="open || confirmOpen"
    x-cloak
    x-transition.opacity
    @click="open ? $wire.closeModal() : $wire.closeDeleteModal()"
></div>
