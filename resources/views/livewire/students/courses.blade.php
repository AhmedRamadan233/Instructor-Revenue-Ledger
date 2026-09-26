<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Courses</h1>
            <p class="text-muted mb-0">Published courses available with your subscription access.</p>
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
                        <th>
                            <x-table.sort-button column="created_at" label="Created" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="updated_at" label="Updated" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
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
                            <td>{{ optional($course->created_at)->format('Y-m-d') }}</td>
                            <td>{{ optional($course->updated_at)->format('Y-m-d') }}</td>
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
            <div class="card-footer">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>
