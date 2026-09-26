<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Student Dashboard</h1>
        <p class="text-muted mb-0">Overview of your subscriptions and courses.</p>
    </div>

    <div class="card shadow-sm border-primary mb-4">
        <div class="card-header bg-primary-subtle">
            <strong>قواعد الاشتراك والإلغاء</strong>
            <div class="small text-muted fw-normal">مهم تعرفها قبل ما تشترك أو تلغي.</div>
        </div>
        <div class="card-body">
            <ol class="mb-3 ps-3">
                <li class="mb-2">
                    الاشتراك بيديلك وصول للكورسات المنشورة طول ما هو <strong>Active</strong>.
                    المشاهدة بتتسجّل بالثواني وبتدخل في حساب إيراد المدرّسين.
                </li>
                <li class="mb-2">
                    جزء من الدفع بيروح للمنصة، والباقي حصة المدرّسين — بتتقسّم شهريًا
                    حسب مين اتفرّجت على كورساته أكتر.
                </li>
                <li class="mb-2">
                    لو لغيت الاشتراك: الوصول بيتقفل <strong>من دلوقتي</strong>،
                    ومفيش استرجاع للمبلغ المدفوع.
                </li>
                <li class="mb-2">
                    المشاهدة اللي عملتها من أول الشهر لحد لحظة الإلغاء
                    <strong>بتتحسب</strong> في تسوية الشهر ده للمدرّسين.
                </li>
                <li class="mb-2">
                    الشهور اللي بعد تاريخ الإلغاء: الاشتراك ملغوش تأثير فيها، ومش هتقدر تتفرّج من غيره.
                </li>
                <li class="mb-0">
                    بعد الإلغاء تقدر تشترك في خطة جديدة من
                    <a href="{{ route('student.plans') }}">الخطط</a>.
                    إدارة الاشتراكات من
                    <a href="{{ route('student.subscriptions') }}">اشتراكاتي</a>.
                </li>
            </ol>

            <div class="alert alert-light border mb-0 small">
                <strong>باختصار:</strong>
                الإلغاء بيقفل الخدمة فورًا من غير ما يمسح سجل مشاهدتك أو التوزيعات اللي اتحسبت عن الفترة اللي كنت مشترك فيها.
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('student.subscriptions') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Subscriptions</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $subscriptionsCount }}</div>
                        <div class="small text-primary">Manage →</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Active</div>
                    <div class="fs-3 fw-semibold">{{ $activeSubscriptionsCount }}</div>
                    <div class="small text-muted">Current plans</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('student.plans') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Browse Plans</div>
                        <div class="fs-3 fw-semibold text-dark">→</div>
                        <div class="small text-primary">Subscribe</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('student.courses') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Courses</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $coursesCount }}</div>
                        <div class="small text-primary">Watch →</div>
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
                        <p class="text-muted p-3 mb-0">No subscriptions yet. <a href="{{ route('student.plans') }}">Browse plans</a></p>
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
                                <li class="list-group-item d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $course->title }}</div>
                                        <div class="small text-muted">
                                            Teacher: {{ $course->teacher?->user?->name ?? '—' }}
                                        </div>
                                    </div>
                                    <a href="{{ route('student.courses.watch', $course) }}" class="btn btn-sm btn-outline-primary align-self-center">Watch</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
