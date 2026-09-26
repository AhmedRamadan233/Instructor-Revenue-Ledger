@extends('layouts.guest')

@section('title', 'Courses')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Courses for Students</h1>
        <p class="text-muted mb-0">
            These courses are published by teachers. Students can access them after an active subscription.
        </p>
    </div>

    @if ($courses->isEmpty())
        <div class="alert alert-secondary mb-0">No published courses available.</div>
    @else
        <div class="row g-3">
            @foreach ($courses as $course)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">Teacher course</div>
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title">{{ $course->title }}</h2>
                            <p class="card-text text-muted small mb-2">
                                Teacher: {{ $course->teacher?->user?->name ?? '—' }}
                            </p>
                            <p class="card-text flex-grow-1">
                                {{ $course->description ?: 'No description.' }}
                            </p>
                            <span class="badge text-bg-success align-self-start">Visible to subscribed students</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
