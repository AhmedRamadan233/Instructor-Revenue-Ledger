<?php

namespace App\Livewire\Dashboard;

use App\Models\Setting;
use Livewire\Attributes\Title;

#[Title('Settings')]
class Settings extends __AbstractManagerComponent
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $editingKey = '';

    public string $editingValue = '';

    public function edit(int $settingId): void
    {
        $setting = Setting::query()->findOrFail($settingId);

        $this->editingId = $setting->id;
        $this->editingKey = $setting->key;
        $this->editingValue = $setting->value;
        $this->showModal = true;

        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset('editingId', 'editingKey', 'editingValue');
        $this->resetValidation();
    }

    public function save(): void
    {
        $setting = Setting::query()->findOrFail($this->editingId);

        $this->validate([
            'editingValue' => $setting->key === 'platform_revenue_percentage'
                ? ['required', 'numeric', 'min:0', 'max:100']
                : ['required', 'string', 'max:255'],
        ], [
            'editingValue.numeric' => 'Platform revenue percentage must be a number between 0 and 100.',
            'editingValue.min' => 'Platform revenue percentage must be a number between 0 and 100.',
            'editingValue.max' => 'Platform revenue percentage must be a number between 0 and 100.',
        ]);

        $setting->update([
            'value' => (string) $this->editingValue,
        ]);

        $this->closeModal();

        session()->flash('success', 'Setting updated successfully.');
    }

    public function render()
    {
        return view('livewire.dashboard.settings.index', [
            'settings' => Setting::query()->orderBy('key')->get(),
        ]);
    }
}
