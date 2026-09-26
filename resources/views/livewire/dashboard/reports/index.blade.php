<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Financial Reports</h1>
        <p class="text-muted mb-0">Platform-wide snapshot of subscriptions, allocations, and payouts.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Subscription revenue</div>
                    <div class="fs-4 fw-semibold">{{ number_format($subscriptionRevenue, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Platform cut</div>
                    <div class="fs-4 fw-semibold">{{ number_format($platformRevenue, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Teacher pool</div>
                    <div class="fs-4 fw-semibold">{{ number_format($teacherPool, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Allocated to teachers</div>
                    <div class="fs-4 fw-semibold">{{ number_format($allocatedTotal, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Ledger earnings</div>
                    <div class="fs-4 fw-semibold">{{ number_format($ledgerEarnings, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Ledger payouts</div>
                    <div class="fs-4 fw-semibold">{{ number_format($ledgerPayouts, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pending payouts</div>
                    <div class="fs-4 fw-semibold">{{ number_format($pendingPayouts, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Paid payouts</div>
                    <div class="fs-4 fw-semibold">{{ number_format($paidPayouts, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Active subscriptions</div>
                    <div class="fs-4 fw-semibold">{{ $activeSubscriptions }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Cancelled</div>
                    <div class="fs-4 fw-semibold">{{ $cancelledSubscriptions }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Expired</div>
                    <div class="fs-4 fw-semibold">{{ $expiredSubscriptions }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Revenue periods</div>
                    <div class="fs-4 fw-semibold">{{ $processedPeriods }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Teacher earnings vs payouts</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Teacher</th>
                        <th>Earnings</th>
                        <th>Paid out</th>
                        <th>Net (earnings − paid)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teacherBalances as $teacher)
                        @php
                            $earnings = (float) ($teacher->earnings_sum ?? 0);
                            $paid = (float) ($teacher->payouts_sum ?? 0);
                        @endphp
                        <tr wire:key="report-teacher-{{ $teacher->id }}">
                            <td>{{ $teacher->user?->name ?? 'Teacher #'.$teacher->id }}</td>
                            <td>{{ number_format($earnings, 2) }}</td>
                            <td>{{ number_format($paid, 2) }}</td>
                            <td class="fw-semibold">{{ number_format($earnings - $paid, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center py-4">No teachers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
