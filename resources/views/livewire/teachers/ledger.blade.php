<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Ledger</h1>
            <p class="text-muted mb-0">Earnings, refunds, and adjustments for your account.</p>
        </div>
    </div>

    <x-table.toolbar
        search-placeholder="Search amount or currency..."
        :has-active-filters="$type !== ''"
    >
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-1" for="type-filter">Type</label>
            <select id="type-filter" class="form-select" wire:model.live="type">
                <option value="">All types</option>
                @foreach ($types as $item)
                    <option value="{{ $item->value }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </x-table.toolbar>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <x-table.sort-button column="type" label="Type" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="amount" label="Amount" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="currency" label="Currency" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Period</th>
                        <th>
                            <x-table.sort-button column="created_at" label="Date" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        <tr wire:key="ledger-{{ $entry->id }}">
                            <td><span class="badge text-bg-secondary">{{ $entry->type->name }}</span></td>
                            <td class="fw-semibold">{{ number_format((float) $entry->amount, 2) }}</td>
                            <td>{{ $entry->currency }}</td>
                            <td>{{ optional($entry->revenuePeriod?->period_start)->format('Y-m') ?? '—' }}</td>
                            <td>{{ optional($entry->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No ledger entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            <div class="card-footer">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
