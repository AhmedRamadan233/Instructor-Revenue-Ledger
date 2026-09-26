<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Courses</h1>
            <p class="text-muted mb-0">Published courses available with your active subscription. Open a course to see full details and your watch time.</p>
        </div>
    </div>

    <x-table.toolbar search-placeholder="Search title or description..." />

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <x-table.sort-button column="title" label="Title" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Teacher</th>
                        <th>Description</th>
                        <th>You watched</th>
                        <th class="text-end" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr wire:key="course-{{ $course->id }}">
                            <td class="fw-semibold">{{ $course->title }}</td>
                            <td>{{ $course->teacher?->user?->name ?? '—' }}</td>
                            <td class="text-muted small" style="max-width: 280px;">
                                {{ \Illuminate\Support\Str::limit($course->description ?: 'No description.', 80) }}
                            </td>
                            <td>
                                @php $watched = (int) ($course->watched_seconds ?? 0); @endphp
                                @if ($watched > 0)
                                    <span class="fw-semibold">{{ $watched }}s</span>
                                @else
                                    <span class="text-muted">Not watched</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('student.courses.watch', $course) }}" class="btn btn-sm btn-primary">
                                    Details / Watch
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">
                                No courses found. An active subscription may be required.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($courses->hasPages())
            <div class="card-footer">{{ $courses->links() }}</div>
        @endif
    </div>
</div>
