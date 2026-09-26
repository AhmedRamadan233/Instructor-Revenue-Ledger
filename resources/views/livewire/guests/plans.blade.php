<div>
    <div class="mb-4">
        <h1 class="h3 mb-1">Plans to Subscribe</h1>
        <p class="text-muted mb-0">
            Each plan is one package. Choose a billing type once — Livewire updates every plan card.
        </p>
    </div>

    @if ($plans->isEmpty())
        <div class="alert alert-secondary mb-0">No plans available.</div>
    @else
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="global-plan-type">Billing type</label>
                <select
                    id="global-plan-type"
                    class="form-select"
                    wire:model.live="selectedType"
                >
                    @foreach ($planTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($plans as $plan)
                @php
                    $option = $plan->options->first(
                        fn ($item) => $item->type->value === $selectedType
                    );
                @endphp

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">Subscribe</div>
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title">{{ $plan->name }}</h2>
                            <p class="card-text text-muted small mb-3">
                                {{ $plan->description ?: 'Platform subscription plan.' }}
                            </p>

                            @if ($option)
                                <p class="mb-1 text-muted small">
                                    Type: {{ $option->type->label() }}
                                </p>
                                <p class="mb-1 text-muted small">
                                    Duration: {{ $option->duration_months }} months
                                </p>
                                <p class="fs-4 fw-semibold mb-3">
                                    {{ number_format((float) $option->price, 2) }}
                                    <span class="fs-6 text-muted">{{ $option->currency }}</span>
                                </p>
                                @auth
                                    @if (auth()->user()->is_student)
                                        <a href="{{ route('student.plans') }}" class="btn btn-primary mt-auto align-self-start">
                                            Subscribe as student
                                        </a>
                                    @else
                                        <span class="badge text-bg-secondary align-self-start mt-auto">
                                            Login as student to subscribe
                                        </span>
                                    @endif
                                @else
                                    <a href="{{ route('guest.login') }}" class="btn btn-outline-primary mt-auto align-self-start">
                                        Login to subscribe
                                    </a>
                                @endauth
                            @else
                                <p class="mb-1 text-muted small">Type: not available</p>
                                <p class="mb-1 text-muted small">Duration: —</p>
                                <p class="fs-4 fw-semibold mb-3">—</p>
                                <span class="badge text-bg-secondary align-self-start mt-auto">
                                    Not available for this type
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
