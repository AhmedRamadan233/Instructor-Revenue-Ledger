@props([
    'column',
    'label',
    'sortBy',
    'sortDirection' => 'desc',
])

<button
    type="button"
    class="btn btn-link btn-sm text-decoration-none text-dark p-0 fw-semibold"
    wire:click="sort('{{ $column }}')"
>
    {{ $label }}
    @if ($sortBy === $column)
        <span class="text-primary" aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
    @endif
</button>
