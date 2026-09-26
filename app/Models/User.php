<?php

namespace App\Models;

use App\Models\Attribute\UserAttributes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, UserAttributes, Notifiable;

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
