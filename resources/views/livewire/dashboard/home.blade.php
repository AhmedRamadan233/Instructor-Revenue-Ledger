<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Manager Dashboard</h1>
        <p class="text-muted mb-0">Platform overview and configuration.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Students</div>
                    <div class="fs-3 fw-semibold">{{ $studentsCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Teachers</div>
                    <div class="fs-3 fw-semibold">{{ $teachersCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Active Plans</div>
                    <div class="fs-3 fw-semibold">{{ $plansCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Courses</div>
                    <div class="fs-3 fw-semibold">{{ $coursesCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header">Settings</div>
                <div class="card-body p-0">
                    @if ($settings->isEmpty())
                        <p class="text-muted p-3 mb-0">No settings found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($settings as $setting)
                                        <tr>
                                            <td>{{ $setting->key }}</td>
                                            <td>{{ $setting->value }}</td>
                                            <td>{{ $setting->type->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header">Recent Courses</div>
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
