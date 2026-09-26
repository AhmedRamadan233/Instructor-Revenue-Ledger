<?php

namespace App\Livewire\Requests\Dashboard;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class TeacherRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(?int $ignoreUserId = null, bool $passwordRequired = true): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreUserId),
            ],
            'password' => [
                $passwordRequired ? 'required' : 'nullable',
                'string',
                Password::defaults(),
            ],
        ];
    }
}
