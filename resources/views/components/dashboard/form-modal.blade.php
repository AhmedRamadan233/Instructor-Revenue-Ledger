@props([
    'title',
    'openProperty' => 'showModal',
    'closeMethod' => 'closeModal',
    'submitMethod' => 'save',
    'size' => '',
])

@php
    $dialogClass = trim('modal-dialog modal-dialog-centered '.$size);
@endphp

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
    <div class="{{ $dialogClass }}" @click.stop>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5">{{ $title }}</h2>
                <button
                    type="button"
                    class="btn-close"
                    aria-label="Close"
                    wire:click="{{ $closeMethod }}"
                ></button>
            </div>

            <form wire:submit="{{ $submitMethod }}">
                <div class="modal-body">
                    {{ $slot }}
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        wire:click="{{ $closeMethod }}"
                        wire:loading.attr="disabled"
                    >
                        Cancel
                    </button>
                    {{ $footer ?? '' }}
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="{{ $submitMethod }}"
                    >
                        <span wire:loading.remove wire:target="{{ $submitMethod }}">Save</span>
                        <span wire:loading wire:target="{{ $submitMethod }}">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
