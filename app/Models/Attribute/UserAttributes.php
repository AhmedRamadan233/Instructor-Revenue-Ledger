<?php

namespace App\Models\Attribute;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait UserAttributes
{
    use __AppliesAttributes;

    protected function isStudent(): Attribute
    {
        return $this->relationExistsAttribute('student');
    }

    protected function isTeacher(): Attribute
    {
        return $this->relationExistsAttribute('teacher');
    }

    protected function isManager(): Attribute
    {
        return $this->relationExistsAttribute('manager');
    }
}
