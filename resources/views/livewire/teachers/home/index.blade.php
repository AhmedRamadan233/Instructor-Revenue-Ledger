<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Teacher Dashboard</h1>
        <p class="text-muted mb-0">Overview of your courses, students, and revenue.</p>
    </div>

    <div class="card shadow-sm border-success mb-4">
        <div class="card-header bg-success-subtle">
            <strong>إزاي بيتحسب دخلك؟</strong>
            <div class="small text-muted fw-normal">اقرا القواعد دي مرة — ده نظام التسوية كل شهر.</div>
        </div>
        <div class="card-body">
            <ol class="mb-3 ps-3">
                <li class="mb-2">
                    لما الطالب يدفع اشتراك، المنصة بتاخد
                    <strong>{{ number_format($platformPercentage, 0) }}%</strong>
                    والباقي
                    <strong>{{ number_format($teacherPoolPercentage, 0) }}%</strong>
                    بيتحط في <em>حصة المدرّسين</em> للاشتراك ده.
                </li>
                <li class="mb-2">
                    حصة المدرّسين بتتوزّع بالتساوي على شهور الخطة
                    (مثال: خطة 3 شهور → تلت الحصة لكل شهر).
                </li>
                <li class="mb-2">
                    في نهاية كل شهر مكتمل، حصة الشهر بتتقسّم
                    <strong>حسب وقت المشاهدة فقط</strong>
                    بين المدرّسين اللي الطالب اتفرّج على كورساتهم فعليًا في الشهر ده.
                </li>
                <li class="mb-2">
                    نصيبك =
                    <code>(ثواني مشاهدتك ÷ إجمالي ثواني المشاهدة في الشهر) × حصة الشهر</code>.
                    كل ما اتفرّجوا على كورساتك أكتر، نصيبك يزيد.
                </li>
                <li class="mb-2">
                    لو الطالب مااتفرّجش خالص في الشهر، الفلوس
                    <strong>بتترحّل</strong> للشهر اللي بعده — مبتضيعش ومبتتوزّعش على حد لسه.
                </li>
                <li class="mb-2">
                    الشهور بتتقفل تلقائي يوم <strong>1 الساعة 02:00</strong>
                    عن الشهر اللي فات. الشهر الحالي بيفضل مفتوح لحد ما يخلص.
                </li>
                <li class="mb-2">
                    لو الطالب <strong>ألغى الاشتراك</strong>: الوصول بيتقفل من لحظة الإلغاء،
                    ومفيش استرجاع للمبلغ. المشاهدة اللي حصلت من أول الشهر لحد وقت الإلغاء
                    لسه بتتحسب في تسوية الشهر ده. الشهور اللي بعد تاريخ الإلغاء مش بيدخل فيها الاشتراك.
                    أي إيرادات اتوزّعت أو اترحّلت عن فترات كان الاشتراك فيها شغال
                    <strong>مبتتمسحش</strong>.
                </li>
                <li class="mb-0">
                    نتائج الأرباح بتظهر في
                    <a href="{{ route('teacher.allocations') }}">التوزيعات</a>
                    و
                    <a href="{{ route('teacher.ledger') }}">دفتر الحساب</a>.
                    طلب صرف الرصيد من
                    <a href="{{ route('teacher.payouts') }}">طلبات الصرف</a>.
                </li>
            </ol>

            <div class="alert alert-light border mb-0 small">
                <strong>مثال سريع:</strong>
                الطالب دفع 100. المنصة بتاخد {{ number_format($platformPercentage, 0) }}
                → حصة المدرّسين {{ number_format($teacherPoolPercentage, 0) }}.
                في خطة شهر واحد، الحصة كلها لنفس الشهر.
                لو اتفرّجوا عليك 70 ثانية وعلى مدرّس تاني 30 ثانية،
                إنت بتاخد 70% من الحصة والتاني بياخد 30%.
            </div>
        </div>
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
            <a href="{{ route('teacher.students') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small">Students</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $studentsCount }}</div>
                        <div class="small text-success">View →</div>
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
                        <div class="text-muted small">Ledger</div>
                        <div class="fs-3 fw-semibold text-dark">{{ $ledgerCount }}</div>
                        <div class="small text-muted">{{ number_format($totalAllocated, 2) }} allocated</div>
                    </div>
                </div>
            </a>
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
                                        <div class="small text-muted">{{ $course->students_count }} students</div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="badge text-bg-secondary">{{ $course->status->name }}</span>
                                        <a href="{{ route('teacher.courses.students', $course) }}" class="small">Students</a>
                                    </div>
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
