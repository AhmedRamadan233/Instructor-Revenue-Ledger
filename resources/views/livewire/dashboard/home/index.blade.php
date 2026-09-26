<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Manager Dashboard</h1>
        <p class="text-muted mb-0">Platform overview and configuration.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Students</div>
                    <div class="fs-3 fw-semibold">{{ $studentsCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('dashboard.teachers') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Teachers</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $teachersCount }}</div>
                        <div class="small text-primary">Manage →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('dashboard.plans') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Active Plans</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $plansCount }}</div>
                        <div class="small text-primary">Manage →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('dashboard.courses') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Courses</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $coursesCount }}</div>
                        <div class="small text-primary">Manage →</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Platform Revenue %</span>
                    <a href="{{ route('dashboard.settings') }}" class="btn btn-sm btn-outline-primary">Edit Settings</a>
                </div>
                <div class="card-body">
                    <p class="fs-2 fw-semibold mb-1">
                        {{ $platformPercentage !== null ? $platformPercentage.'%' : '—' }}
                    </p>
                    <p class="text-muted small mb-0">
                        Edit this value from Settings. Snapshot is taken when a subscription is created.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Courses</span>
                    <a href="{{ route('dashboard.courses') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentCourses->isEmpty())
                        <p class="text-muted p-3 mb-0">No courses found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Teacher</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentCourses as $course)
                                        <tr>
                                            <td>{{ $course->title }}</td>
                                            <td>{{ $course->teacher?->user?->name ?? '—' }}</td>
                                            <td>{{ $course->status->name }}</td>
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
