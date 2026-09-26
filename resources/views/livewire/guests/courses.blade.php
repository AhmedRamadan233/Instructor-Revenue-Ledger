<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Courses for Students</h1>
        <p class="text-muted mb-0">
            These courses are published by teachers. Students can access them after an active subscription.
        </p>
    </div>

    <div class="row mb-4" x-data="{ focused: false }">
        <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label" for="course-search">Search courses</label>
            <input
                id="course-search"
                type="search"
                class="form-control"
                placeholder="Search by title or description..."
                wire:model.live.debounce.300ms="search"
                @focus="focused = true"
                @blur="focused = false"
                :class="{ 'border-primary': focused }"
            >
            <div wire:loading wire:target="search" class="form-text">Searching...</div>
        </div>
    </div>

    @if ($courses->isEmpty())
        <div class="alert alert-secondary mb-0">
            @if (filled($search))
                No courses match your search.
            @else
                No published courses available.
            @endif
        </div>
    @else
        <div class="row g-3">
            @foreach ($courses as $course)
                <div
                    class="col-12 col-md-6 col-lg-4"
                    x-data="{ open: false }"
                >
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">Teacher course</div>
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title">{{ $course->title }}</h2>
                            <p class="card-text text-muted small mb-2">
                                Teacher: {{ $course->teacher?->user?->name ?? '—' }}
                            </p>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary align-self-start mb-2"
                                @click="open = !open"
                            >
                                <span x-text="open ? 'Hide description' : 'Show description'"></span>
                            </button>

                            <p class="card-text flex-grow-1" x-show="open" x-transition>
                                {{ $course->description ?: 'No description.' }}
                            </p>

                            <span class="badge text-bg-success align-self-start mt-auto">
                                Visible to subscribed students
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
