<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-light">
    <nav
        class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top"
        x-data="{ open: false }"
    >
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('guest.home') }}">
                {{ config('app.name') }}
            </a>

            <button
                class="navbar-toggler"
                type="button"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div
                class="navbar-collapse"
                :class="{ show: open, collapse: !open }"
                id="guestNavbar"
            >
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a
                            class="nav-link @if (request()->routeIs('guest.plans')) active @endif"
                            href="{{ route('guest.plans') }}"
                        >
                            Plans to Subscribe
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link @if (request()->routeIs('guest.courses')) active @endif"
                            href="{{ route('guest.courses') }}"
                        >
                            Courses for Students
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
