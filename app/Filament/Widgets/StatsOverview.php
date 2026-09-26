<?php

namespace App\Filament\Widgets;

use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\PlanRepositoryInterface;
use App\Repo\InterFace\SettingRepositoryInterface;
use App\Repo\InterFace\StudentRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Platform overview';

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $platformPercentage = (float) (app(SettingRepositoryInterface::class)
            ->first('key', 'platform_revenue_percentage')?->value ?? 20);

        return [
            Stat::make('Students', (string) app(StudentRepositoryInterface::class)->count(withoutGlobalScopes: true)),
            Stat::make('Teachers', (string) app(TeacherRepositoryInterface::class)->count(withoutGlobalScopes: true)),
            Stat::make('Active plans', (string) app(PlanRepositoryInterface::class)->count(['is_active' => true])),
            Stat::make('Courses', (string) app(CourseRepositoryInterface::class)->count(withoutGlobalScopes: true)),
            Stat::make('Platform cut', number_format($platformPercentage, 2).'%'),
            Stat::make('Teacher pool', number_format(max(0, 100 - $platformPercentage), 2).'%'),
        ];
    }
}
