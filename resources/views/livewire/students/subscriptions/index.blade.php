<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">My Subscriptions</h1>
            <p class="text-muted mb-0">Search, filter, and manage your subscriptions.</p>
        </div>
        <a href="{{ route('student.plans') }}" class="btn btn-primary btn-sm">Browse plans</a>
    </div>

    <div class="alert alert-warning border small mb-4">
        <strong>قبل ما تلغي:</strong>
        الإلغاء بيقفل الوصول فورًا ومفيش استرجاع.
        مشاهدة الشهر الحالي لحد لحظة الإلغاء بتتحسب للمدرّسين، والشهور اللي بعده مش هيدخل فيها الاشتراك.
        بعد الإلغاء تقدر تشترك من جديد من
        <a href="{{ route('student.plans') }}" class="alert-link">الخطط</a>.
    </div>

    <x-table.toolbar
        search-placeholder="Search plan, amount, currency..."
        :has-active-filters="$status !== ''"
    >
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-1" for="status-filter">Status</label>
            <select id="status-filter" class="form-select" wire:model.live="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $item)
                    <option value="{{ $item->value }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </x-table.toolbar>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Plan</th>
                        <th>
                            <x-table.sort-button column="status" label="Status" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="amount" label="Amount" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Platform %</th>
                        <th>
                            <x-table.sort-button column="starts_at" label="Starts" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="ends_at" label="Ends" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $subscription)
                        <tr wire:key="subscription-{{ $subscription->id }}">
                            <td>
                                <div class="fw-semibold">{{ $subscription->planOption?->plan?->name ?? 'Plan' }}</div>
                                <div class="small text-muted">{{ $subscription->planOption?->type?->label() ?? '—' }}</div>
                            </td>
                            <td><span class="badge text-bg-secondary">{{ $subscription->status->name }}</span></td>
                            <td>{{ number_format((float) $subscription->amount, 2) }} {{ $subscription->currency }}</td>
                            <td>{{ number_format((float) $subscription->platform_percentage, 2) }}%</td>
                            <td>{{ optional($subscription->starts_at)->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ optional($subscription->ends_at)->format('Y-m-d') ?? '—' }}</td>
                            <td class="text-end">
                                @if ($subscription->status->value === \App\Enums\SubscriptionStatus::Active->value)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="cancel({{ $subscription->id }})"
                                        wire:confirm="الإلغاء بيقفل الوصول فورًا ومفيش استرجاع. مشاهدة الشهر الحالي لحد دلوقتي هتتحسب، والشهور الجاية مش هتدخل. هل تريد الإلغاء؟"
                                    >
                                        Cancel
                                    </button>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center py-4">No subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subscriptions->hasPages())
            <div class="card-footer">{{ $subscriptions->links() }}</div>
        @endif
    </div>
</div>
