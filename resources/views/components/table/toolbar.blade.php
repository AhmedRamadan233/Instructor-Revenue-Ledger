@props([
    'searchPlaceholder' => 'Search...',
    'hasActiveFilters' => false,
])

@php
    $hasFiltersSlot = $slot->isNotEmpty();
@endphp

<div
    class="card shadow-sm mb-3"
    x-data="{ filtersOpen: @js($hasActiveFilters) }"
>
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md">
                <label class="form-label small text-muted mb-1" for="table-search">Search</label>
                <div class="input-group">
                    <input
                        id="table-search"
                        type="search"
                        class="form-control"
                        placeholder="{{ $searchPlaceholder }}"
                        wire:model.live.debounce.300ms="search"
                    >
                    @if ($hasFiltersSlot)
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            x-show="!filtersOpen"
                            @click="filtersOpen = true"
                        >
                            Filters
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            x-show="filtersOpen"
                            x-cloak
                            @click="filtersOpen = false"
                        >
                            Hide filters
                        </button>
                    @endif
                </div>
            </div>

            @if ($hasFiltersSlot)
                <div class="col-12" x-show="filtersOpen" x-cloak x-transition>
                    <div class="row g-2 align-items-end pt-1">
                        {{ $slot }}
                        <div class="col-12 col-md-auto">
                            <button
                                type="button"
                                class="btn btn-outline-secondary w-100"
                                wire:click="clearFilters"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
