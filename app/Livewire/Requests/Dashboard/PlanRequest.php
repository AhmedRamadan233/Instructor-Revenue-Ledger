<?php

namespace App\Livewire\Requests\Dashboard;

final class PlanRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isActive' => ['boolean'],
            'options' => ['required', 'array'],
            'options.*.price' => ['required', 'numeric', 'min:0'],
            'options.*.is_active' => ['boolean'],
        ];
    }
}
