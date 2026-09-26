<div>
    <div class="mb-4 d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="h3 mb-1">Browse Plans</h1>
            <p class="text-muted mb-0">Choose a billing type and subscribe. Platform % is snapshotted at purchase.</p>
        </div>
        <a href="{{ route('student.subscriptions') }}" class="btn btn-outline-primary btn-sm">My subscriptions</a>
    </div>

    @if ($hasActive)
        <div class="alert alert-info">
            You already have an active subscription. Cancel it first if you want to switch plans.
        </div>
    @endif

    @if ($plans->isEmpty())
        <div class="alert alert-secondary mb-0">No plans available.</div>
    @else
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="global-plan-type">Billing type</label>
                <select id="global-plan-type" class="form-select" wire:model.live="selectedType">
                    @foreach ($planTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($plans as $plan)
                @php
                    $option = $plan->options->first(fn ($item) => $item->type->value === $selectedType);
                @endphp

                <div class="col-12 col-md-6 col-lg-4" wire:key="plan-{{ $plan->id }}">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">{{ $plan->name }}</div>
                        <div class="card-body d-flex flex-column">
                            <p class="card-text text-muted small mb-3">
                                {{ $plan->description ?: 'Platform subscription plan.' }}
                            </p>

                            @if ($option)
                                <p class="mb-1 text-muted small">Type: {{ $option->type->label() }}</p>
                                <p class="mb-1 text-muted small">Duration: {{ $option->duration_months }} months</p>
                                <p class="fs-4 fw-semibold mb-3">
                                    {{ number_format((float) $option->price, 2) }}
                                    <span class="fs-6 text-muted">{{ $option->currency }}</span>
                                </p>
                                <button
                                    type="button"
                                    class="btn btn-primary mt-auto"
                                    wire:click="subscribe({{ $option->id }})"
                                    wire:loading.attr="disabled"
                                    @disabled($hasActive)
                                >
                                    <span wire:loading.remove wire:target="subscribe({{ $option->id }})">Subscribe</span>
                                    <span wire:loading wire:target="subscribe({{ $option->id }})">Processing...</span>
                                </button>
                            @else
                                <p class="text-muted mb-0 mt-auto">Not available for this billing type.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @error('planOptionId')
        <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
    @enderror
</div>
