<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Teacher Dashboard</h1>
        <p class="text-muted mb-0">Your courses and revenue records.</p>
    </div>

    <section class="mb-4">
        <h2 class="h5 mb-3">My Courses</h2>

        @if ($courses->isEmpty())
            <div class="alert alert-secondary mb-0">You have no courses yet.</div>
        @else
            <div class="row g-3">
                @foreach ($courses as $course)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">{{ $course->status->name }}</div>
                            <div class="card-body">
                                <h3 class="h6">{{ $course->title }}</h3>
                                <p class="mb-0 text-muted small">{{ $course->description ?: 'No description.' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Revenue Allocations</div>
                <div class="card-body p-0">
                    @if ($allocations->isEmpty())
                        <p class="text-muted p-3 mb-0">No allocations yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Amount</th>
                                        <th>Consumption</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allocations as $allocation)
                                        <tr>
                                            <td>
                                                {{ optional($allocation->revenuePeriod?->period_start)->format('Y-m') ?? '—' }}
                                            </td>
                                            <td>
                                                {{ number_format((float) $allocation->allocated_amount, 2) }}
                                                {{ $allocation->currency }}
                                            </td>
                                            <td>{{ $allocation->consumption_seconds }}s</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Ledger Entries</div>
                <div class="card-body p-0">
                    @if ($ledgerEntries->isEmpty())
                        <p class="text-muted p-3 mb-0">No ledger entries yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ledgerEntries as $entry)
                                        <tr>
                                            <td>{{ $entry->type->name }}</td>
                                            <td>
                                                {{ number_format((float) $entry->amount, 2) }}
                                                {{ $entry->currency }}
                                            </td>
                                            <td>{{ optional($entry->created_at)->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
