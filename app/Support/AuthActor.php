<?php

namespace App\Support;

use App\Models\User;

final class AuthActor
{
    public static function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    /**
     * Managers can see everything.
     * Artisan/seeders/queue workers without a logged-in user also bypass.
     * HTTP guests and PHPUnit requests without actingAs() do NOT bypass.
     */
    public static function shouldBypassTenantScopes(): bool
    {
        if (self::isManager()) {
            return true;
        }

        return self::user() === null
            && app()->runningInConsole()
            && ! app()->runningUnitTests();
    }

    public static function denyAll(): bool
    {
        return self::user() === null && ! self::shouldBypassTenantScopes();
    }

    public static function studentId(): ?int
    {
        $user = self::user();

        if ($user === null) {
            return null;
        }

        return $user->student()->withoutGlobalScopes()->value('id');
    }

    public static function teacherId(): ?int
    {
        $user = self::user();

        if ($user === null) {
            return null;
        }

        return $user->teacher()->withoutGlobalScopes()->value('id');
    }

    public static function isStudent(): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return $user->student()->withoutGlobalScopes()->exists();
    }

    public static function isTeacher(): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return $user->teacher()->withoutGlobalScopes()->exists();
    }

    public static function isManager(): bool
    {
        $user = self::user();

        if ($user === null) {
            return false;
        }

        return $user->manager()->withoutGlobalScopes()->exists();
    }
}
