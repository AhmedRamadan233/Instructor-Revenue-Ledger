<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">طلبات الصرف</h1>
            <p class="text-muted mb-0">اطلب صرف رصيدك المتاح من الأرباح المسجّلة في الدفتر.</p>
        </div>
        <div class="text-end">
            <div class="text-muted small">الرصيد المتاح</div>
            <div class="fs-4 fw-semibold">{{ number_format($availableBalance, 2) }} EGP</div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">طلب صرف جديد</div>
        <div class="card-body">
            <form wire:submit="requestPayout" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="payout-amount">المبلغ</label>
                    <input
                        id="payout-amount"
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="form-control @error('amount') is-invalid @enderror"
                        wire:model="amount"
                        placeholder="0.00"
                    >
                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="payout-note">ملاحظة (اختياري)</label>
                    <input id="payout-note" type="text" class="form-control @error('note') is-invalid @enderror" wire:model="note">
                    @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" @disabled($availableBalance <= 0)>
                        إرسال الطلب
                    </button>
                </div>
            </form>
            <p class="small text-muted mt-3 mb-0">
                الرصيد = الأرباح − الاستردادات + التعديلات − المصروف المدفوع − الطلبات المعلّقة.
                الطلبات المعلّقة بتحجز المبلغ لحد ما الإدارة توافق أو ترفض.
            </p>
        </div>
    </div>

    <x-table.toolbar
        search-placeholder="Search amount, currency, note..."
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
                        <th>
                            <x-table.sort-button column="requested_at" label="Requested" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="amount" label="Amount" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>
                            <x-table.sort-button column="status" label="Status" :sort-by="$sortBy" :sort-direction="$sortDirection" />
                        </th>
                        <th>Note</th>
                        <th>Processed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payouts as $payout)
                        <tr wire:key="payout-{{ $payout->id }}">
                            <td>{{ optional($payout->requested_at)->format('Y-m-d H:i') }}</td>
                            <td class="fw-semibold">{{ number_format((float) $payout->amount, 2) }} {{ $payout->currency }}</td>
                            <td><span class="badge text-bg-secondary">{{ $payout->status->name }}</span></td>
                            <td class="small text-muted">{{ $payout->note ?: '—' }}</td>
                            <td>{{ optional($payout->processed_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No payout requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payouts->hasPages())
            <div class="card-footer">{{ $payouts->links() }}</div>
        @endif
    </div>
</div>
