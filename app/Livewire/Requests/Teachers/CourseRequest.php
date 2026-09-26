<?php

namespace App\Livewire\Requests\Teachers;

use App\Enums\CourseStatus;
use Illuminate\Validation\Rule;

final class CourseRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'courseStatus' => ['required', Rule::enum(CourseStatus::class)],
        ];
    }
}
