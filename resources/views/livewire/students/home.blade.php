<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Student Dashboard</h1>
        <p class="text-muted mb-0">Overview of your subscriptions and courses.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <a href="{{ route('student.subscriptions') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Subscriptions</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $subscriptionsCount }}</div>
                        <div class="small text-primary">View all →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Active</div>
                    <div class="fs-3 fw-semibold">{{ $activeSubscriptionsCount }}</div>
                    <div class="small text-muted">Current plans</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <a href="{{ route('student.courses') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Available Courses</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $coursesCount }}</div>
                        <div class="small text-primary">Browse →</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Subscriptions</span>
                    <a href="{{ route('student.subscriptions') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentSubscriptions->isEmpty())
                        <p class="text-muted p-3 mb-0">No subscriptions yet.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($recentSubscriptions as $subscription)
                                <li class="list-group-item d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $subscription->planOption?->plan?->name ?? 'Plan' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $subscription->planOption?->type?->label() ?? '—' }}
                                            · {{ number_format((float) $subscription->amount, 2) }} {{ $subscription->currency }}
                                        </div>
                                    </div>
                                    <span class="badge text-bg-secondary align-self-start">{{ $subscription->status->name }}</span>
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
                    <span>Recent Courses</span>
                    <a href="{{ route('student.courses') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentCourses->isEmpty())
                        <p class="text-muted p-3 mb-0">No courses available.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($recentCourses as $course)
                                <li class="list-group-item">
                                    <div class="fw-semibold">{{ $course->title }}</div>
                                    <div class="small text-muted">
                                        Teacher: {{ $course->teacher?->user?->name ?? '—' }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
