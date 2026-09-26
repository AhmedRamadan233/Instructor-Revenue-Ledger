<?php

namespace App\Filament\Pages;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Enums\SubscriptionStatus;
use App\Repo\InterFace\PayoutRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FinancialReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Money';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Financial reports';

    protected string $view = 'filament.pages.financial-reports';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $subscriptions = app(SubscriptionRepositoryInterface::class);
        $allocations = app(RevenueAllocationRepositoryInterface::class);
        $ledger = app(TeacherLedgerEntryRepositoryInterface::class);
        $payouts = app(PayoutRepositoryInterface::class);
        $periods = app(RevenuePeriodRepositoryInterface::class);
        $teachers = app(TeacherRepositoryInterface::class);

        $teacherBalances = $teachers->getWith(
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

        return [
            'subscriptionRevenue' => $subscriptions->sum('amount', withoutGlobalScopes: true),
            'platformRevenue' => $subscriptions->sum('platform_amount', withoutGlobalScopes: true),
            'teacherPool' => $subscriptions->sum('teacher_pool_amount', withoutGlobalScopes: true),
            'allocatedTotal' => $allocations->sum('allocated_amount', withoutGlobalScopes: true),
            'ledgerEarnings' => $ledger->sum('amount', ['type' => LedgerEntryType::Earning], withoutGlobalScopes: true),
            'ledgerPayouts' => $ledger->sum('amount', ['type' => LedgerEntryType::Payout], withoutGlobalScopes: true),
            'pendingPayouts' => $payouts->sum('amount', ['status' => PayoutStatus::Pending], withoutGlobalScopes: true),
            'paidPayouts' => $payouts->sum('amount', ['status' => PayoutStatus::Paid], withoutGlobalScopes: true),
            'activeSubscriptions' => $subscriptions->count(['status' => SubscriptionStatus::Active], withoutGlobalScopes: true),
            'cancelledSubscriptions' => $subscriptions->count(['status' => SubscriptionStatus::Cancelled], withoutGlobalScopes: true),
            'expiredSubscriptions' => $subscriptions->count(['status' => SubscriptionStatus::Expired], withoutGlobalScopes: true),
            'processedPeriods' => $periods->count(withoutGlobalScopes: true),
            'teacherBalances' => $teacherBalances,
        ];
    }
}
