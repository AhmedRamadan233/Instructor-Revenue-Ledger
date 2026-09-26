<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Revenue Processing</h1>
        <p class="text-muted mb-0">
            Close a finished calendar month: split each subscription&rsquo;s monthly teacher pool by watch time.
            Months with no watching carry that pool into the next close. Current/open months cannot be processed.
            Auto-runs on the 1st at 02:00 via <code>revenue:process</code>.
        </p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Allocations</div>
                    <div class="fs-3 fw-semibold">{{ $allocationsCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Ledger entries</div>
                    <div class="fs-3 fw-semibold">{{ $ledgerCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">CLI</div>
                    <code class="small">php artisan revenue:process</code>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">Process a finished month</div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Manual close for catch-up only. Default is last month. Processing locks the period permanently.
            </p>
            <form wire:submit="process" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="revenue-year">Year</label>
                    <input id="revenue-year" type="number" class="form-control @error('year') is-invalid @enderror" wire:model="year">
                    @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="revenue-month">Month</label>
                    <input id="revenue-month" type="number" min="1" max="12" class="form-control @error('month') is-invalid @enderror" wire:model="month">
                    @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="process,processForDemo">
                        <span wire:loading.remove wire:target="process">Process period</span>
                        <span wire:loading wire:target="process">Processing...</span>
                    </button>

                    @if ($showDemoProcess)
                        <button
                            type="button"
                            class="btn btn-outline-warning"
                            wire:click="processForDemo"
                            wire:loading.attr="disabled"
                            wire:target="process,processForDemo"
                            title="Skips already-processed and unfinished-month checks. Rebuilds allocations for this month."
                        >
                            <span wire:loading.remove wire:target="processForDemo">Process (demo / re-run)</span>
                            <span wire:loading wire:target="processForDemo">Re-running...</span>
                        </button>
                    @endif
                </div>
            </form>

            @if ($showDemoProcess)
                <p class="text-warning small mt-2 mb-0">
                    <strong>Demo button:</strong> skips “already processed” and “month not finished yet” locks so you can re-run the same month while recording. Still rebuilds that month’s allocations/earnings from current watch data.
                </p>
            @endif

            @error('period')
                <div class="alert alert-danger mt-3 mb-0" role="alert">
                    <div class="fw-semibold mb-1">Could not process this period</div>
                    <div>{{ $message }}</div>
                </div>
            @enderror
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Recent periods</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Allocations</th>
                        <th>Processed at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                        <tr wire:key="period-{{ $period->id }}">
                            <td>
                                {{ optional($period->period_start)->format('Y-m-d') }}
                                →
                                {{ optional($period->period_end)->format('Y-m-d') }}
                            </td>
                            <td><span class="badge text-bg-secondary">{{ $period->status->name }}</span></td>
                            <td>{{ $period->allocations_count }}</td>
                            <td>{{ optional($period->processed_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center py-4">No periods processed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
