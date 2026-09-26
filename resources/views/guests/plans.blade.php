@extends('layouts.guest')

@section('title', 'Plans')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">Plans to Subscribe</h1>
        <p class="text-muted mb-0">
            Each plan is one package. Choose a billing type once to update all plans below.
        </p>
    </div>

    @if ($plans->isEmpty())
        <div class="alert alert-secondary mb-0">No plans available.</div>
    @else
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="global-plan-type">Billing type</label>
                <select id="global-plan-type" class="form-select">
                    @foreach ($planTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($plans as $plan)
                @php
                    $options = $plan->options->map(fn ($option) => [
                        'type' => $option->type->value,
                        'label' => $option->type->label(),
                        'price' => number_format((float) $option->price, 2),
                        'currency' => $option->currency,
                        'duration' => $option->duration_months,
                    ])->values();
                @endphp

                <div class="col-12 col-md-6 col-lg-4">
                    <div
                        class="card h-100 shadow-sm plan-card"
                        data-options='@json($options)'
                    >
                        <div class="card-header">Subscribe</div>
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title">{{ $plan->name }}</h2>
                            <p class="card-text text-muted small mb-3">
                                {{ $plan->description ?: 'Platform subscription plan.' }}
                            </p>

                            <p class="mb-1 text-muted small plan-type-label">—</p>
                            <p class="mb-1 text-muted small plan-duration">—</p>

                            <p class="fs-4 fw-semibold mb-3 plan-price">—</p>

                            <span class="badge text-bg-primary align-self-start mt-auto plan-badge">
                                Available to subscribe
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        const globalSelect = document.getElementById('global-plan-type');
        const cards = document.querySelectorAll('.plan-card');

        if (globalSelect && cards.length) {
            const renderAll = (typeValue) => {
                cards.forEach((card) => {
                    const options = JSON.parse(card.dataset.options || '[]');
                    const option = options.find((item) => String(item.type) === String(typeValue));
                    const typeLabelEl = card.querySelector('.plan-type-label');
                    const durationEl = card.querySelector('.plan-duration');
                    const priceEl = card.querySelector('.plan-price');
                    const badgeEl = card.querySelector('.plan-badge');

                    if (!option) {
                        typeLabelEl.textContent = 'Type: not available';
                        durationEl.textContent = 'Duration: —';
                        priceEl.textContent = '—';
                        badgeEl.className = 'badge text-bg-secondary align-self-start mt-auto plan-badge';
                        badgeEl.textContent = 'Not available for this type';
                        return;
                    }

                    typeLabelEl.textContent = `Type: ${option.label}`;
                    durationEl.textContent = `Duration: ${option.duration} months`;
                    priceEl.innerHTML = `${option.price} <span class="fs-6 text-muted">${option.currency}</span>`;
                    badgeEl.className = 'badge text-bg-primary align-self-start mt-auto plan-badge';
                    badgeEl.textContent = 'Available to subscribe';
                });
            };

            globalSelect.addEventListener('change', (event) => {
                renderAll(event.target.value);
            });

            renderAll(globalSelect.value);
        }
    </script>
@endpush
