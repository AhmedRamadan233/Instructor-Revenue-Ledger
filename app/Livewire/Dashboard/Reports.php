<?php

namespace App\Livewire\Dashboard;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payout;
use App\Models\RevenueAllocation;
use App\Models\RevenuePeriod;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\TeacherLedgerEntry;
use Livewire\Attributes\Title;

#[Title('Financial Reports')]
class Reports extends __AbstractManagerComponent
{
    public function render()
    {
        $subscriptions = Subscription::query()->withoutGlobalScopes();

        $teacherBalances = Teacher::query()
            ->withoutGlobalScopes()
            ->with('user')
            ->withSum([
                'ledgerEntries as earnings_sum' => fn ($query) => $query
                    ->withoutGlobalScopes()
                    ->where('type', LedgerEntryType::Earning),
            ], 'amount')
            ->withSum([
                'ledgerEntries as payouts_sum' => fn ($query) => $query
                    ->withoutGlobalScopes()
                    ->where('type', LedgerEntryType::Payout),
            ], 'amount')
            ->orderBy('id')
            ->limit(20)
            ->get();

        return view('livewire.dashboard.reports.index', [
            'subscriptionRevenue' => (float) (clone $subscriptions)->sum('amount'),
            'platformRevenue' => (float) (clone $subscriptions)->sum('platform_amount'),
            'teacherPool' => (float) (clone $subscriptions)->sum('teacher_pool_amount'),
            'allocatedTotal' => (float) RevenueAllocation::query()->withoutGlobalScopes()->sum('allocated_amount'),
            'ledgerEarnings' => (float) TeacherLedgerEntry::query()->withoutGlobalScopes()
                ->where('type', LedgerEntryType::Earning)->sum('amount'),
            'ledgerPayouts' => (float) TeacherLedgerEntry::query()->withoutGlobalScopes()
                ->where('type', LedgerEntryType::Payout)->sum('amount'),
            'pendingPayouts' => (float) Payout::query()->withoutGlobalScopes()
                ->where('status', PayoutStatus::Pending)->sum('amount'),
            'paidPayouts' => (float) Payout::query()->withoutGlobalScopes()
                ->where('status', PayoutStatus::Paid)->sum('amount'),
            'activeSubscriptions' => (clone $subscriptions)->where('status', SubscriptionStatus::Active)->count(),
            'cancelledSubscriptions' => (clone $subscriptions)->where('status', SubscriptionStatus::Cancelled)->count(),
            'expiredSubscriptions' => (clone $subscriptions)->where('status', SubscriptionStatus::Expired)->count(),
            'processedPeriods' => RevenuePeriod::query()->withoutGlobalScopes()->count(),
            'teacherBalances' => $teacherBalances,
        ]);
    }
}
