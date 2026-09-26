<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Student Dashboard</h1>
        <p class="text-muted mb-0">Your subscriptions and available courses.</p>
    </div>

    <section class="mb-4">
        <h2 class="h5 mb-3">My Subscriptions</h2>

        @if ($subscriptions->isEmpty())
            <div class="alert alert-secondary mb-0">You have no subscriptions yet.</div>
        @else
            <div class="row g-3">
                @foreach ($subscriptions as $subscription)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">{{ $subscription->status->name }}</div>
                            <div class="card-body">
                                <h3 class="h6">
                                    {{ $subscription->planOption?->plan?->name ?? 'Plan' }}
                                    · {{ $subscription->planOption?->type?->label() ?? '—' }}
                                </h3>
                                <p class="mb-1 text-muted small">
                                    Amount: {{ number_format((float) $subscription->amount, 2) }} {{ $subscription->currency }}
                                </p>
                                <p class="mb-0 text-muted small">
                                    {{ optional($subscription->starts_at)->format('Y-m-d') ?? '—' }}
                                    →
                                    {{ optional($subscription->ends_at)->format('Y-m-d') ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <h2 class="h5 mb-3">Courses Available To Me</h2>

        @if ($courses->isEmpty())
            <div class="alert alert-secondary mb-0">
                No courses available. An active subscription is required to unlock published courses.
            </div>
        @else
            <div class="row g-3">
                @foreach ($courses as $course)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">Course</div>
                            <div class="card-body">
                                <h3 class="h6">{{ $course->title }}</h3>
                                <p class="text-muted small mb-2">
                                    Teacher: {{ $course->teacher?->user?->name ?? '—' }}
                                </p>
                                <p class="mb-0">{{ $course->description ?: 'No description.' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
