<?php

namespace App\Livewire\Requests\Dashboard;

final class SettingRequest
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(string $key): array
    {
        return [
            'editingValue' => $key === 'platform_revenue_percentage'
                ? ['required', 'numeric', 'min:0', 'max:100']
                : ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(string $key): array
    {
        if ($key !== 'platform_revenue_percentage') {
            return [];
        }

        return [
            'editingValue.numeric' => 'Platform revenue percentage must be a number between 0 and 100.',
            'editingValue.min' => 'Platform revenue percentage must be a number between 0 and 100.',
            'editingValue.max' => 'Platform revenue percentage must be a number between 0 and 100.',
        ];
    }
}
