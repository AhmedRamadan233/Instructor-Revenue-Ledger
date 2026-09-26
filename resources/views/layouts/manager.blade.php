<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @hasSection('title')
            @yield('title')
        @else
            {{ $title ?? config('app.name') }}
        @endif
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>[x-cloak] { display: none !important; }</style>
    @livewireStyles
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" x-data="{ open: false }">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard.home') }}">Manager Dashboard</a>

            <button class="navbar-toggler" type="button" @click="open = !open" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse" :class="{ show: open, collapse: !open }">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a
                            class="nav-link @if (request()->routeIs('dashboard.home')) active @endif"
                            href="{{ route('dashboard.home') }}"
                        >
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link @if (request()->routeIs('dashboard.settings')) active @endif"
                            href="{{ route('dashboard.settings') }}"
                        >
                            Settings
                        </a>
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

        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    @livewireScripts
</body>
</html>
