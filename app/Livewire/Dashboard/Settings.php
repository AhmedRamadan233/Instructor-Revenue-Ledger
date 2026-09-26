<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Requests\Dashboard\SettingRequest;
use App\Repo\InterFace\SettingRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Settings')]
class Settings extends __AbstractManagerComponent
{
    private SettingRepositoryInterface $settings;

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $editingKey = '';

    public string $editingValue = '';

    public function boot(SettingRepositoryInterface $settings): void
    {
        $this->settings = $settings;
    }

    public function edit(int $settingId): void
    {
        $setting = $this->settings->getById($settingId);

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
        $setting = $this->settings->getById($this->editingId);

        $this->validate(
            SettingRequest::rules($setting->key),
            SettingRequest::messages($setting->key),
        );

        $this->settings->update($setting->id, [
            'value' => (string) $this->editingValue,
        ]);

        $this->closeModal();

        session()->flash('success', 'Setting updated successfully.');
    }

    public function render()
    {
        return view('livewire.dashboard.settings.index', [
            'settings' => $this->settings->getWith(orderBy: 'key'),
        ]);
    }
}
