<?php

namespace App\Livewire\Requests\Students;

final class WatchRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'seconds' => ['required', 'integer', 'min:1', 'max:300'],
        ];
    }
}
