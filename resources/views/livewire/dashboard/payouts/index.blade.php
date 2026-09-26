<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Teacher Payouts</h1>
        <p class="text-muted mb-0">Review payout requests and mark them paid or rejected.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pending requests</div>
                    <div class="fs-3 fw-semibold">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total paid</div>
                    <div class="fs-3 fw-semibold">{{ number_format($paidTotal, 2) }} EGP</div>
                </div>
            </div>
        </div>
    </div>

    <x-table.toolbar
        search-placeholder="Search amount, currency, note..."
        :has-active-filters="$status !== ''"
    >
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-1" for="status-filter">Status</label>
            <select id="status-filter" class="form-select" wire:model.live="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $item)
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
                        <th>Teacher</th>
                        <th>
                            <x-table.sort-button column="requested_at" label="Requested" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="amount" label="Amount" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="status" label="Status" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Note</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payouts as $payout)
                        <tr wire:key="manager-payout-{{ $payout->id }}">
                            <td>
                                <div class="fw-semibold">{{ $payout->teacher?->user?->name ?? '—' }}</div>
                                <div class="small text-muted">#{{ $payout->teacher_id }}</div>
                            </td>
                            <td>{{ optional($payout->requested_at)->format('Y-m-d H:i') }}</td>
                            <td class="fw-semibold">{{ number_format((float) $payout->amount, 2) }} {{ $payout->currency }}</td>
                            <td><span class="badge text-bg-secondary">{{ $payout->status->name }}</span></td>
                            <td class="small text-muted">{{ $payout->note ?: '—' }}</td>
                            <td class="text-end">
                                @if ($payout->status === \App\Enums\PayoutStatus::Pending)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-success"
                                        wire:click="markPaid({{ $payout->id }})"
                                        wire:confirm="Mark this payout as paid and post it to the ledger?"
                                    >
                                        Mark paid
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="reject({{ $payout->id }})"
                                        wire:confirm="Reject this payout request?"
                                    >
                                        Reject
                                    </button>
                                @else
                                    <span class="text-muted small">
                                        {{ optional($payout->processed_at)->format('Y-m-d H:i') ?? '—' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">No payout requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payouts->hasPages())
            <div class="card-footer">{{ $payouts->links() }}</div>
        @endif
    </div>
</div>
