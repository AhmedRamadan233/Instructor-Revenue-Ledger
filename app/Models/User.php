<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function manager(): HasOne
    {
        return $this->hasOne(Manager::class);
    }

    protected function isStudent(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->student()->withoutGlobalScopes()->exists()
        )->shouldCache();
    }

    protected function isTeacher(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->teacher()->withoutGlobalScopes()->exists()
        )->shouldCache();
    }

    protected function isManager(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->manager()->withoutGlobalScopes()->exists()
        )->shouldCache();
    }

    public function dashboardRouteName(): string
    {
        return match (true) {
            $this->is_manager => 'dashboard.home',
            $this->is_teacher => 'teacher.home',
            $this->is_student => 'student.home',
            default => 'guest.home',
        };
    }
}
