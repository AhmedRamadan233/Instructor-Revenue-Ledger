<div
    x-data="{
        watching: @entangle('hasOpenSession'),
        timer: null,
        start() {
            if (this.timer) return;
            this.watching = true;
            this.timer = setInterval(() => $wire.heartbeat(), 30000);
            $wire.heartbeat();
        },
        stop() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
            this.watching = false;
            $wire.stopWatching();
        }
    }"
    x-on:livewire:navigating.window="if (timer) clearInterval(timer)"
>
    <div class="mb-4">
        <a href="{{ route('student.courses') }}" class="small text-decoration-none text-muted">← Back to courses</a>
        <h1 class="h3 mb-1 mt-1">Course details</h1>
        <p class="text-muted mb-0">Full course info and your watch progress on this course.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold">About this course</div>
                <div class="card-body">
                    <h2 class="h4 mb-2">{{ $course->title }}</h2>

                    <dl class="row mb-0 small">
                        <dt class="col-sm-4 text-muted">Teacher</dt>
                        <dd class="col-sm-8">{{ $course->teacher?->user?->name ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Teacher email</dt>
                        <dd class="col-sm-8">{{ $course->teacher?->user?->email ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge text-bg-secondary">{{ $course->status->name }}</span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Created</dt>
                        <dd class="col-sm-8">{{ optional($course->created_at)->format('Y-m-d') ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Last updated</dt>
                        <dd class="col-sm-8">{{ optional($course->updated_at)->format('Y-m-d') ?? '—' }}</dd>
                    </dl>

                    <hr>

                    <h3 class="h6 text-muted">Description</h3>
                    <p class="mb-0">{{ $course->description ?: 'No description provided for this course.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold">Your watch progress</div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="text-muted small">Total watched</div>
                            <div class="fs-4 fw-semibold">{{ $this->totalWatchLabel }}</div>
                            <div class="text-muted small">{{ $totalWatchSeconds }} seconds</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Current session</div>
                            <div class="fs-4 fw-semibold">{{ $this->currentSessionLabel }}</div>
                            <div class="text-muted small">
                                @if ($hasOpenSession)
                                    Open now
                                @else
                                    No open session
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Sessions count</div>
                            <div class="fs-5 fw-semibold">{{ $sessionsCount }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Last activity</div>
                            <div class="fw-semibold">{{ $lastActivityAt ?? '—' }}</div>
                        </div>
                    </div>

                    @error('course')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @error('seconds')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="alert alert-light border small mb-3">
                        While watching is on, the app records <strong>+{{ $seconds }} seconds</strong> every 30 seconds.
                        This time is what teachers get paid from later.
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="btn btn-primary"
                            x-show="!watching"
                            @click="start()"
                            wire:loading.attr="disabled"
                        >
                            Start watching
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            x-show="watching"
                            x-cloak
                            @click="stop()"
                        >
                            Stop watching
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            wire:click="heartbeat"
                            wire:loading.attr="disabled"
                            x-show="watching"
                            x-cloak
                        >
                            Record +{{ $seconds }}s now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-semibold">Recent watch sessions on this course</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Started</th>
                        <th>Last activity</th>
                        <th>Ended</th>
                        <th>Watched</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr wire:key="session-{{ $session->id }}">
                            <td>{{ $session->id }}</td>
                            <td>{{ optional($session->started_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ optional($session->last_activity_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ optional($session->ended_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>
                                <span class="fw-semibold">{{ $session->watch_seconds }}s</span>
                            </td>
                            <td>
                                @if ($session->ended_at === null)
                                    <span class="badge text-bg-success">Open</span>
                                @else
                                    <span class="badge text-bg-secondary">Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">
                                You have not watched this course yet. Press <strong>Start watching</strong> to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
