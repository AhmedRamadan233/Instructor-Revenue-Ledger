<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <x-filament::section>
            <x-slot name="heading">Close a calendar month</x-slot>
            <x-slot name="description">
                Pick year/month then use <strong>Process month</strong>, or use the demo button to auto-pick the month with the most watch time.
            </x-slot>

            <form wire:submit.prevent style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 0.75rem;">
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <label for="year" style="font-size: 0.875rem; font-weight: 500;">Year</label>
                    <select
                        id="year"
                        wire:model.live="year"
                        class="fi-input fi-select-input"
                        style="width: 100%; min-height: 2.5rem; border-radius: 0.5rem; padding: 0.5rem 0.75rem;"
                    >
                        @foreach (range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <label for="month" style="font-size: 0.875rem; font-weight: 500;">Month</label>
                    <select
                        id="month"
                        wire:model.live="month"
                        class="fi-input fi-select-input"
                        style="width: 100%; min-height: 2.5rem; border-radius: 0.5rem; padding: 0.5rem 0.75rem;"
                    >
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 1rem; font-size: 0.875rem; opacity: 0.7;">
                <span>Allocations total: <strong style="opacity: 1;">{{ number_format($allocationsCount) }}</strong></span>
                <span>Ledger rows: <strong style="opacity: 1;">{{ number_format($ledgerCount) }}</strong></span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Recent periods</x-slot>

            <div style="overflow-x: auto; margin-top: 0.5rem;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Period</th>
                            <th style="text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Status</th>
                            <th style="text-align: right; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Allocations</th>
                            <th style="text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.25); opacity: 0.7; font-weight: 500;">Processed at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periods as $period)
                            <tr wire:key="period-{{ $period->id }}">
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15);">
                                    {{ $period->period_start?->format('Y-m-d') }} → {{ $period->period_end?->format('Y-m-d') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15);">
                                    {{ $period->status->name ?? $period->status }}
                                </td>
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15); text-align: right; font-variant-numeric: tabular-nums;">
                                    {{ $period->allocations_count }}
                                </td>
                                <td style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(128,128,128,0.15);">
                                    {{ optional($period->processed_at)->format('Y-m-d H:i') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 1.25rem 1rem; opacity: 0.6;">No periods processed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
