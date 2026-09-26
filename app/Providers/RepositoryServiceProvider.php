<?php

namespace App\Providers;

use App\Repo\Elequent\CourseConsumptionSessionRepository;
use App\Repo\Elequent\CourseRepository;
use App\Repo\Elequent\ManagerRepository;
use App\Repo\Elequent\PayoutRepository;
use App\Repo\Elequent\PlanOptionRepository;
use App\Repo\Elequent\PlanRepository;
use App\Repo\Elequent\RevenueAllocationRepository;
use App\Repo\Elequent\RevenuePeriodRepository;
use App\Repo\Elequent\SettingRepository;
use App\Repo\Elequent\StudentRepository;
use App\Repo\Elequent\SubscriptionPaymentRepository;
use App\Repo\Elequent\SubscriptionRepository;
use App\Repo\Elequent\TeacherLedgerEntryRepository;
use App\Repo\Elequent\TeacherRepository;
use App\Repo\Elequent\UserRepository;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\ManagerRepositoryInterface;
use App\Repo\InterFace\PayoutRepositoryInterface;
use App\Repo\InterFace\PlanOptionRepositoryInterface;
use App\Repo\InterFace\PlanRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\SettingRepositoryInterface;
use App\Repo\InterFace\StudentRepositoryInterface;
use App\Repo\InterFace\SubscriptionPaymentRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use App\Repo\InterFace\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->singleton(CourseConsumptionSessionRepositoryInterface::class, CourseConsumptionSessionRepository::class);
        $this->app->singleton(ManagerRepositoryInterface::class, ManagerRepository::class);
        $this->app->singleton(PayoutRepositoryInterface::class, PayoutRepository::class);
        $this->app->singleton(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->singleton(PlanOptionRepositoryInterface::class, PlanOptionRepository::class);
        $this->app->singleton(RevenueAllocationRepositoryInterface::class, RevenueAllocationRepository::class);
        $this->app->singleton(RevenuePeriodRepositoryInterface::class, RevenuePeriodRepository::class);
        $this->app->singleton(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->singleton(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->singleton(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->singleton(SubscriptionPaymentRepositoryInterface::class, SubscriptionPaymentRepository::class);
        $this->app->singleton(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->singleton(TeacherLedgerEntryRepositoryInterface::class, TeacherLedgerEntryRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
