<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <a href="{{ route('teacher.courses') }}" class="small text-decoration-none text-muted">← Back to courses</a>
            <h1 class="h3 mb-1 mt-1">{{ $course->title }}</h1>
            <p class="text-muted mb-0">Students who watched this course.</p>
        </div>
        <span class="badge text-bg-secondary align-self-start">{{ $course->status->name }}</span>
    </div>

    <x-table.toolbar search-placeholder="Search name or email..." />

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Sessions</th>
                        <th>
                            <x-table.sort-button column="created_at" label="Joined" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr wire:key="course-student-{{ $student->id }}">
                            <td class="fw-semibold">{{ $student->user?->name ?? '—' }}</td>
                            <td>{{ $student->user?->email ?? '—' }}</td>
                            <td>{{ $student->sessions_count }}</td>
                            <td>{{ optional($student->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center py-4">
                                No students for this course yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->hasPages())
            <div class="card-footer">{{ $students->links() }}</div>
        @endif
    </div>
</div>
