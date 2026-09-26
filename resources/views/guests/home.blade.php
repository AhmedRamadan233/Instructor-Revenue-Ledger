@extends('layouts.guest')

@section('title', 'Home')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-2">Welcome</h1>
        <p class="text-muted">
            Choose a subscription plan, or browse the courses teachers publish for students.
        </p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Plans to Subscribe</h2>
                    <p class="card-text text-muted">
                        Packages you can subscribe to. One plan unlocks courses from all teachers.
                    </p>
                    <a href="{{ route('guest.plans') }}" class="btn btn-primary">View Plans</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Courses for Students</h2>
                    <p class="card-text text-muted">
                        Courses published by teachers. Students access them after an active subscription.
                    </p>
                    <a href="{{ route('guest.courses') }}" class="btn btn-outline-primary">View Courses</a>
                </div>
            </div>
        </div>
    </div>
@endsection
