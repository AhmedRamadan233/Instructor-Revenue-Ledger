<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Revenue Allocations</h1>
            <p class="text-muted mb-0">Allocated amounts from subscription consumption periods.</p>
        </div>
    </div>

    <x-table.toolbar search-placeholder="Search amount, currency, consumption..." />

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Period</th>
                        <th>
                            <x-table.sort-button column="allocated_amount" label="Amount" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="currency" label="Currency" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="consumption_seconds" label="Consumption" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="created_at" label="Created" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allocations as $allocation)
                        <tr wire:key="allocation-{{ $allocation->id }}">
                            <td>
                                {{ optional($allocation->revenuePeriod?->period_start)->format('Y-m-d') ?? '—' }}
                                →
                                {{ optional($allocation->revenuePeriod?->period_end)->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="fw-semibold">{{ number_format((float) $allocation->allocated_amount, 2) }}</td>
                            <td>{{ $allocation->currency }}</td>
                            <td>{{ number_format($allocation->consumption_seconds) }}s</td>
                            <td>{{ optional($allocation->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No allocations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($allocations->hasPages())
            <div class="card-footer">
                {{ $allocations->links() }}
            </div>
        @endif
    </div>
</div>
