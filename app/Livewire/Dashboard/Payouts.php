<?php

namespace App\Livewire\Dashboard;

use App\Actions\Payouts\MarkPayoutPaid;
use App\Actions\Payouts\RejectPayout;
use App\Enums\PayoutStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Repo\InterFace\ManagerRepositoryInterface;
use App\Repo\InterFace\PayoutRepositoryInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Payouts')]
class Payouts extends __AbstractManagerComponent
{
    use InteractsWithTable;

    private PayoutRepositoryInterface $payouts;

    private ManagerRepositoryInterface $managers;

    #[Url(except: '')]
    public string $status = '';

    public function boot(
        PayoutRepositoryInterface $payouts,
        ManagerRepositoryInterface $managers,
    ): void {
        $this->payouts = $payouts;
        $this->managers = $managers;
    }

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
        $payout = $this->payouts->getById($payoutId, withoutGlobalScopes: true);
        $manager = $this->managers->first('user_id', auth()->id(), withoutGlobalScopes: true);

        abort_if($manager === null, 403);

        $action->handle($payout, $manager);

        session()->flash('success', 'Payout marked as paid and posted to the teacher ledger.');
    }

    public function reject(int $payoutId, RejectPayout $action): void
    {
        $payout = $this->payouts->getById($payoutId, withoutGlobalScopes: true);
        $manager = $this->managers->first('user_id', auth()->id(), withoutGlobalScopes: true);

        abort_if($manager === null, 403);

        $action->handle($payout, $manager);

        session()->flash('success', 'Payout rejected. Balance is available again for the teacher.');
    }

    public function render()
    {
        return view('livewire.dashboard.payouts.index', [
            'payouts' => $this->payouts->forTable(
                relations: [
                    'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
                ],
                scopes: [
                    'search' => [$this->search],
                    'status' => [$this->status],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: ['created_at', 'requested_at', 'amount', 'status'],
                defaultSort: 'requested_at',
                withoutGlobalScopes: true,
            ),
            'statuses' => PayoutStatus::cases(),
            'pendingCount' => $this->payouts->count(
                ['status' => PayoutStatus::Pending],
                withoutGlobalScopes: true,
            ),
            'paidTotal' => $this->payouts->sum(
                'amount',
                ['status' => PayoutStatus::Paid],
                withoutGlobalScopes: true,
            ),
        ]);
    }
}
