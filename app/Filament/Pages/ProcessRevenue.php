<?php

namespace App\Filament\Pages;

use App\Actions\Revenue\ProcessRevenuePeriod;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class ProcessRevenue extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|UnitEnum|null $navigationGroup = 'Money';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Revenue';

    protected static ?string $title = 'Process revenue';

    protected string $view = 'filament.pages.process-revenue';

    public int $year;

    public int $month;

    public function mount(): void
    {
        $previous = now()->subMonthNoOverflow();
        $this->year = $previous->year;
        $this->month = $previous->month;
    }

    public function getHeading(): string|Htmlable
    {
        return 'Revenue periods';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('process')
                ->label('Process month')
                ->icon(Heroicon::OutlinedLockClosed)
                ->requiresConfirmation()
                ->action(fn () => $this->runProcess(force: false)),
            Action::make('processDemo')
                ->label('Process (demo / re-run)')
                ->icon(Heroicon::OutlinedPlay)
                ->color('warning')
                ->visible(fn (): bool => ! app()->isProduction())
                ->requiresConfirmation()
                ->action(fn () => $this->processForDemo()),
        ];
    }

    public function processForDemo(): void
    {
        $demoMonth = $this->resolveMonthWithMostWatchTime();

        if ($demoMonth === null) {
            Notification::make()
                ->title('No watch sessions yet')
                ->body('Log in as a student, watch courses, then try again.')
                ->danger()
                ->send();

            return;
        }

        $this->year = (int) $demoMonth->year;
        $this->month = (int) $demoMonth->month;

        $this->runProcess(force: true, autoPickedForDemo: true);
    }

    protected function runProcess(bool $force, bool $autoPickedForDemo = false): void
    {
        $periodStart = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        try {
            $result = app(ProcessRevenuePeriod::class)->handle($periodStart, $periodEnd, force: $force);
        } catch (ValidationException $exception) {
            Notification::make()
                ->title('Cannot process period')
                ->body(collect($exception->errors())->flatten()->first() ?? $exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        $label = $periodStart->format('F Y');
        $prefix = $force
            ? ($autoPickedForDemo ? "[Demo] Auto-selected {$label}. " : '[Demo re-run] ')
            : '';

        if ($result['allocations'] === 0 && $result['ledger_entries'] === 0) {
            $body = $result['subscriptions_considered'] === 0
                ? "{$prefix}{$label} closed with 0 allocations: no overlapping subscription."
                : ($result['carried_forward'] > 0
                    ? "{$prefix}{$label} closed: pool carried forward for {$result['carried_forward']} subscription(s)."
                    : "{$prefix}{$label} closed with 0 allocations.");

            Notification::make()
                ->title($result['carried_forward'] > 0 ? 'Period carried forward' : 'No allocations')
                ->body($body)
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title("Processed {$label}")
            ->body(sprintf(
                '%s%d allocations, %d ledger earnings, %d carried forward.',
                $prefix,
                $result['allocations'],
                $result['ledger_entries'],
                $result['carried_forward'],
            ))
            ->success()
            ->send();
    }

    protected function resolveMonthWithMostWatchTime(): ?Carbon
    {
        $sessions = app(CourseConsumptionSessionRepositoryInterface::class)->query(true)
            ->whereNotNull('started_at')
            ->where('watch_seconds', '>', 0)
            ->get(['started_at', 'watch_seconds']);

        if ($sessions->isEmpty()) {
            return null;
        }

        $bestKey = $sessions
            ->groupBy(fn ($session): string => $session->started_at->format('Y-m'))
            ->map(fn ($group): int => (int) $group->sum('watch_seconds'))
            ->sortDesc()
            ->keys()
            ->first();

        if (! is_string($bestKey) || $bestKey === '') {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $bestKey.'-01')->startOfMonth();
    }

    /**
     * @return array{periods: mixed, allocationsCount: int, ledgerCount: int}
     */
    protected function getViewData(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'periods' => app(RevenuePeriodRepositoryInterface::class)->getWith(
                modify: fn ($query) => $query->withCount('allocations')->latest('period_start'),
                limit: 12,
            ),
            'allocationsCount' => app(RevenueAllocationRepositoryInterface::class)->count(withoutGlobalScopes: true),
            'ledgerCount' => app(TeacherLedgerEntryRepositoryInterface::class)->count(withoutGlobalScopes: true),
        ];
    }
}
