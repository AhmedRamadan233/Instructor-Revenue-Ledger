<?php

namespace App\Livewire\Requests\Teachers;

final class RequestPayoutRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
