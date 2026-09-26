<?php

namespace App\Livewire\Dashboard;

use App\Actions\Payouts\MarkPayoutPaid;
use App\Actions\Payouts\RejectPayout;
use App\Enums\PayoutStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Manager;
use App\Models\Payout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Payouts')]
class Payouts extends __AbstractManagerComponent
{
    use InteractsWithTable;

    #[Url(except: '')]
    public string $status = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function markPaid(int $payoutId, MarkPayoutPaid $action): void
    {
        $payout = Payout::query()->withoutGlobalScopes()->findOrFail($payoutId);
        $manager = Manager::query()->withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $action->handle($payout, $manager);

        session()->flash('success', 'Payout marked as paid and posted to the teacher ledger.');
    }

    public function reject(int $payoutId, RejectPayout $action): void
    {
        $payout = Payout::query()->withoutGlobalScopes()->findOrFail($payoutId);
        $manager = Manager::query()->withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $action->handle($payout, $manager);

        session()->flash('success', 'Payout rejected. Balance is available again for the teacher.');
    }

    public function render()
    {
        $query = Payout::query()
            ->withoutGlobalScopes()
            ->with([
                'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
            ])
            ->search($this->search)
            ->status($this->status);

        $this->applySorting($query, [
            'created_at',
            'requested_at',
            'amount',
            'status',
        ], 'requested_at');

        return view('livewire.dashboard.payouts.index', [
            'payouts' => $query->paginate(10),
            'statuses' => PayoutStatus::cases(),
            'pendingCount' => Payout::query()->withoutGlobalScopes()->where('status', PayoutStatus::Pending)->count(),
            'paidTotal' => (float) Payout::query()->withoutGlobalScopes()->where('status', PayoutStatus::Paid)->sum('amount'),
        ]);
    }
}
