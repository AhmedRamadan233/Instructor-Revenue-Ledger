<?php

use App\Http\Middleware\EnsureUserIs\EnsureUserIsGuest;
use App\Http\Middleware\EnsureUserIs\EnsureUserIsManager;
use App\Http\Middleware\EnsureUserIs\EnsureUserIsStudent;
use App\Http\Middleware\EnsureUserIs\EnsureUserIsTeacher;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware(['web', 'auth', 'manager'])
                ->prefix('dashboard')
                ->name('dashboard.')
                ->group(base_path('routes/dashboard.php'));

            Route::middleware(['web', 'auth', 'student'])
                ->prefix('student')
                ->name('student.')
                ->group(base_path('routes/students.php'));

            Route::middleware(['web', 'auth', 'teacher'])
                ->prefix('teacher')
                ->name('teacher.')
                ->group(base_path('routes/teachers.php'));

            Route::middleware(['web', 'role.guest'])
                ->name('guest.')
                ->group(base_path('routes/guests.php'));
        },
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('revenue:process')
            ->monthlyOn(1, '02:00')
            ->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'manager' => EnsureUserIsManager::class,
            'student' => EnsureUserIsStudent::class,
            'teacher' => EnsureUserIsTeacher::class,
            'role.guest' => EnsureUserIsGuest::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('guest.login'));
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            return $user
                ? route($user->dashboardRouteName())
                : route('guest.home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
