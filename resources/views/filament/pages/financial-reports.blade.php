<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
            @foreach ([
                'Subscription revenue' => $subscriptionRevenue,
                'Platform revenue' => $platformRevenue,
                'Teacher pool' => $teacherPool,
                'Allocated' => $allocatedTotal,
                'Ledger earnings' => $ledgerEarnings,
                'Ledger payouts' => $ledgerPayouts,
                'Pending payouts' => $pendingPayouts,
                'Paid payouts' => $paidPayouts,
            ] as $label => $value)
                <x-filament::section>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem; padding: 0.25rem 0;">
                        <span style="font-size: 0.875rem; opacity: 0.7;">{{ $label }}</span>
                        <span style="font-size: 1.5rem; font-weight: 600; letter-spacing: -0.02em;">
                            {{ number_format((float) $value, 2) }}
                            <span style="font-size: 0.875rem; font-weight: 500; opacity: 0.7;">EGP</span>
                        </span>
                    </div>
                </x-filament::section>
            @endforeach
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach ([
                'Active subscriptions' => $activeSubscriptions,
                'Cancelled' => $cancelledSubscriptions,
                'Expired' => $expiredSubscriptions,
                'Processed periods' => $processedPeriods,
            ] as $label => $value)
                <x-filament::section>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem; padding: 0.25rem 0;">
                        <span style="font-size: 0.875rem; opacity: 0.7;">{{ $label }}</span>
                        <span style="font-size: 1.5rem; font-weight: 600;">{{ number_format((int) $value) }}</span>
                    </div>
                </x-filament::section>
            @endforeach
        </div>

        <x-filament::section>
            <x-slot name="heading">Teacher balances (sample)</x-slot>

            <div style="overflow-x: auto; margin-top: 0.5rem;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Teacher</th>
                            <th style="text-align: right; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Earnings</th>
                            <th style="text-align: right; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Payouts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teacherBalances as $teacher)
                            <tr wire:key="teacher-balance-{{ $teacher->id }}">
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15);">
                                    {{ $teacher->user?->name ?? '—' }}
                                </td>
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15); text-align: right; font-variant-numeric: tabular-nums;">
                                    {{ number_format((float) ($teacher->earnings_sum ?? 0), 2) }}
                                </td>
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15); text-align: right; font-variant-numeric: tabular-nums;">
                                    {{ number_format((float) ($teacher->payouts_sum ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 1.25rem 1rem; opacity: 0.6;">No teachers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
