<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>[x-cloak] { display: none !important; }</style>
    @livewireStyles
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top" x-data="{ open: false }">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('student.home') }}">Student Dashboard</a>

            <button class="navbar-toggler" type="button" @click="open = !open" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse" :class="{ show: open, collapse: !open }">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('student.home')) active @endif" href="{{ route('student.home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('student.subscriptions')) active @endif" href="{{ route('student.subscriptions') }}">Subscriptions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('student.courses')) active @endif" href="{{ route('student.courses') }}">Courses</a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <span class="navbar-text text-white-50">{{ auth()->user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
