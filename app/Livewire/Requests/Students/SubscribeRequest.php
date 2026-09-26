<?php

namespace App\Livewire\Requests\Students;

final class SubscribeRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'planOptionId' => ['required', 'integer', 'exists:plan_options,id'],
        ];
    }
}
