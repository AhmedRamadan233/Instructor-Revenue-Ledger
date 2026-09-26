<div
    x-data="{ open: @entangle('showModal') }"
    @keydown.escape.window="if (open) $wire.closeModal()"
>
    <div class="mb-4">
        <h1 class="h3 mb-1">Settings</h1>
        <p class="text-muted mb-0">Update platform configuration, including revenue percentage.</p>
    </div>

    @if ($settings->isEmpty())
        <div class="alert alert-secondary mb-0">No settings found.</div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Key</th>
                            <th scope="col">Value</th>
                            <th scope="col">Type</th>
                            <th scope="col" class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($settings as $setting)
                            <tr wire:key="setting-{{ $setting->id }}">
                                <td>
                                    <code>{{ $setting->key }}</code>
                                    @if ($setting->key === 'platform_revenue_percentage')
                                        <span class="badge text-bg-info ms-1">Revenue %</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $setting->value }}@if ($setting->key === 'platform_revenue_percentage')%
                                    @endif
                                </td>
                                <td>{{ $setting->type->name }}</td>
                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click="edit({{ $setting->id }})"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @include('livewire.dashboard.settings.edit')
    @include('livewire.dashboard.settings.backdrop')
</div>
