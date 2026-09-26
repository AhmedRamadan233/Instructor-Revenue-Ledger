<x-dashboard.form-modal :title="$editingId ? 'Edit Plan' : 'Add Plan'" size="modal-lg">
    <div class="mb-3">
        <label class="form-label" for="plan-name">Name</label>
        <input id="plan-name" type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="plan-description">Description</label>
        <textarea id="plan-description" rows="2" class="form-control @error('description') is-invalid @enderror" wire:model="description"></textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-check mb-4">
        <input id="plan-active" type="checkbox" class="form-check-input" wire:model="isActive">
        <label class="form-check-label" for="plan-active">Plan is active</label>
    </div>

    <h3 class="h6 mb-3">Billing options (EGP)</h3>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Months</th>
                    <th>Price</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($planTypes as $type)
                    <tr wire:key="option-{{ $type->value }}">
                        <td>{{ $type->label() }}</td>
                        <td>{{ $type->durationMonths() }}</td>
                        <td style="min-width: 140px;">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm @error('options.'.$type->value.'.price') is-invalid @enderror"
                                wire:model="options.{{ $type->value }}.price"
                            >
                            @error('options.'.$type->value.'.price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                wire:model="options.{{ $type->value }}.is_active"
                            >
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-dashboard.form-modal>
