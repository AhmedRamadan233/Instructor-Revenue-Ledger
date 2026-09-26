<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Teacher Dashboard</h1>
        <p class="text-muted mb-0">Overview of your courses and revenue.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('teacher.courses') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Courses</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $coursesCount }}</div>
                        <div class="small text-success">Manage →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('teacher.allocations') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Allocations</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $allocationsCount }}</div>
                        <div class="small text-success">View →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('teacher.ledger') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Ledger Entries</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $ledgerCount }}</div>
                        <div class="small text-success">View →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Total Allocated</div>
                    <div class="fs-3 fw-semibold">{{ number_format($totalAllocated, 2) }}</div>
                    <div class="small text-muted">All periods</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Courses</span>
                    <a href="{{ route('teacher.courses') }}" class="btn btn-sm btn-outline-success">Manage</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentCourses->isEmpty())
                        <p class="text-muted p-3 mb-0">No courses yet.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($recentCourses as $course)
                                <li class="list-group-item d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $course->title }}</div>
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($course->description ?: 'No description.', 60) }}</div>
                                    </div>
                                    <span class="badge text-bg-secondary align-self-start">{{ $course->status->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Allocations</span>
                    <a href="{{ route('teacher.allocations') }}" class="btn btn-sm btn-outline-success">View all</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentAllocations->isEmpty())
                        <p class="text-muted p-3 mb-0">No allocations yet.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($recentAllocations as $allocation)
                                <li class="list-group-item d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ optional($allocation->revenuePeriod?->period_start)->format('Y-m') ?? 'Period' }}
                                        </div>
                                        <div class="small text-muted">{{ $allocation->consumption_seconds }}s consumption</div>
                                    </div>
                                    <span class="fw-semibold">
                                        {{ number_format((float) $allocation->allocated_amount, 2) }}
                                        {{ $allocation->currency }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
