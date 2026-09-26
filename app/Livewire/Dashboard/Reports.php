<?php

namespace App\Livewire\Dashboard;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Enums\SubscriptionStatus;
use App\Repo\InterFace\PayoutRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use Livewire\Attributes\Title;

#[Title('Financial Reports')]
class Reports extends __AbstractManagerComponent
{
    private SubscriptionRepositoryInterface $subscriptions;

    private TeacherRepositoryInterface $teachers;

    private RevenueAllocationRepositoryInterface $allocations;

    private TeacherLedgerEntryRepositoryInterface $ledgerEntries;

    private PayoutRepositoryInterface $payouts;

    private RevenuePeriodRepositoryInterface $periods;

    public function boot(
        SubscriptionRepositoryInterface $subscriptions,
        TeacherRepositoryInterface $teachers,
        RevenueAllocationRepositoryInterface $allocations,
        TeacherLedgerEntryRepositoryInterface $ledgerEntries,
        PayoutRepositoryInterface $payouts,
        RevenuePeriodRepositoryInterface $periods,
    ): void {
        $this->subscriptions = $subscriptions;
        $this->teachers = $teachers;
        $this->allocations = $allocations;
        $this->ledgerEntries = $ledgerEntries;
        $this->payouts = $payouts;
        $this->periods = $periods;
    }

    public function render()
    {
        $teacherBalances = $this->teachers->getWith(
            relations: ['user'],
            withoutGlobalScopes: true,
            orderBy: 'id',
            limit: 20,
            modify: function ($query): void {
                $query->withSum([
                    'ledgerEntries as earnings_sum' => fn ($ledgerQuery) => $ledgerQuery
                        ->withoutGlobalScopes()
                        ->where('type', LedgerEntryType::Earning),
                ], 'amount')
                    ->withSum([
                        'ledgerEntries as payouts_sum' => fn ($ledgerQuery) => $ledgerQuery
                            ->withoutGlobalScopes()
                            ->where('type', LedgerEntryType::Payout),
                    ], 'amount');
            },
        );

        return view('livewire.dashboard.reports.index', [
            'subscriptionRevenue' => $this->subscriptions->sum('amount', withoutGlobalScopes: true),
            'platformRevenue' => $this->subscriptions->sum('platform_amount', withoutGlobalScopes: true),
            'teacherPool' => $this->subscriptions->sum('teacher_pool_amount', withoutGlobalScopes: true),
            'allocatedTotal' => $this->allocations->sum('allocated_amount', withoutGlobalScopes: true),
            'ledgerEarnings' => $this->ledgerEntries->sum(
                'amount',
                ['type' => LedgerEntryType::Earning],
                withoutGlobalScopes: true,
            ),
            'ledgerPayouts' => $this->ledgerEntries->sum(
                'amount',
                ['type' => LedgerEntryType::Payout],
                withoutGlobalScopes: true,
            ),
            'pendingPayouts' => $this->payouts->sum(
                'amount',
                ['status' => PayoutStatus::Pending],
                withoutGlobalScopes: true,
            ),
            'paidPayouts' => $this->payouts->sum(
                'amount',
                ['status' => PayoutStatus::Paid],
                withoutGlobalScopes: true,
            ),
            'activeSubscriptions' => $this->subscriptions->count(
                ['status' => SubscriptionStatus::Active],
                withoutGlobalScopes: true,
            ),
            'cancelledSubscriptions' => $this->subscriptions->count(
                ['status' => SubscriptionStatus::Cancelled],
                withoutGlobalScopes: true,
            ),
            'expiredSubscriptions' => $this->subscriptions->count(
                ['status' => SubscriptionStatus::Expired],
                withoutGlobalScopes: true,
            ),
            'processedPeriods' => $this->periods->count(withoutGlobalScopes: true),
            'teacherBalances' => $teacherBalances,
        ]);
    }
}
