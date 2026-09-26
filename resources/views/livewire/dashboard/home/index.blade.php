<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Manager Dashboard</h1>
        <p class="text-muted mb-0">Platform overview and configuration.</p>
    </div>

    <div class="card shadow-sm border-dark mb-4">
        <div class="card-header bg-dark text-white">
            <strong>دليل المدير — إزاي بيشتغل نظام الفلوس؟</strong>
            <div class="small text-white-50 fw-normal">اقرا القواعد دي مرة عشان تبقى فاهم كل شاشة بتعمل إيه.</div>
        </div>
        <div class="card-body">
            <ol class="mb-3 ps-3">
                <li class="mb-2">
                    لما الطالب يشترك، المنصة بتاخد
                    <strong>{{ number_format($platformPercentage, 0) }}%</strong>
                    (من
                    <a href="{{ route('dashboard.settings') }}">Settings</a>)
                    والباقي
                    <strong>{{ number_format($teacherPoolPercentage, 0) }}%</strong>
                    حصة المدرّسين. النسبة بتتعمل <em>snapshot</em> وقت الاشتراك — تغيير الإعداد بعد كده مش بيأثر على الاشتراكات القديمة.
                </li>
                <li class="mb-2">
                    حصة المدرّسين بتتوزّع على شهور الخطة، وجوّه كل شهر بتتقسّم
                    <strong>حسب وقت المشاهدة</strong>
                    بين المدرّسين اللي الطالب اتفرّج على كورساتهم.
                </li>
                <li class="mb-2">
                    لو مفيش مشاهدة في الشهر، الحصة
                    <strong>بتترحّل</strong>
                    للشهر اللي بعده ومبتضيعش.
                </li>
                <li class="mb-2">
                    قفل الشهر بيتم تلقائي يوم <strong>1 الساعة 02:00</strong>
                    عن الشهر اللي فات، أو يدوي من
                    <a href="{{ route('dashboard.revenue') }}">Revenue</a>.
                    الشهر الحالي/المستقبلي مش بيتقفل، والفترة المتقفلة مش بتتكرر.
                </li>
                <li class="mb-2">
                    إلغاء الاشتراك بيقفل وصول الطالب فورًا ومفيش استرجاع.
                    مشاهدة الشهر لحد الإلغاء بتتحسب؛ الشهور اللي بعده لأ. التوزيعات اللي اتقفلت قبل كده مبتتمسحش.
                </li>
                <li class="mb-2">
                    الاشتراكات المنتهية بتتعلّم
                    <strong>Expired</strong>
                    تلقائي يوميًا الساعة 01:00 (`subscriptions:expire`).
                </li>
                <li class="mb-2">
                    المدرّس بيطلب صرف من رصيده → أنت بتراجع من
                    <a href="{{ route('dashboard.payouts') }}">Payouts</a>:
                    <strong>Mark paid</strong> (بيتسجّل في الدفتر) أو <strong>Reject</strong> (بيرجع الرصيد متاح).
                </li>
                <li class="mb-0">
                    ملخص مالي شامل للمنصة في
                    <a href="{{ route('dashboard.reports') }}">Reports</a>
                    (إيراد الاشتراكات، حصة المنصة، التوزيع، الصرف، حالة الاشتراكات).
                </li>
            </ol>

            <div class="alert alert-light border mb-0 small">
                <strong>مسار سريع:</strong>
                الطالب يشترك ويتفرّج → تقفل الشهر من Revenue → المدرّس يطلب صرف → توافق من Payouts → تتابع الأرقام من Reports.
            </div>
        </div>
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
                        {{ number_format($platformPercentage, 0) }}%
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
