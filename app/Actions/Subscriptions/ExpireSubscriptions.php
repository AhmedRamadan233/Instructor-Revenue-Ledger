<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ExpireSubscriptions
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private CourseConsumptionSessionRepositoryInterface $sessions,
    ) {}

    /**
     * @return array{expired: int, sessions_closed: int}
     */
    public function handle(): array
    {
        return DB::transaction(function (): array {
            $subscriptionIds = $this->subscriptions->query(true)
                ->where('status', SubscriptionStatus::Active)
                ->whereNotNull('ends_at')
                ->where('ends_at', '<', now())
                ->lockForUpdate()
                ->pluck('id');

            if ($subscriptionIds->isEmpty()) {
                return ['expired' => 0, 'sessions_closed' => 0];
            }

            $expired = $this->subscriptions->query(true)
                ->whereIn('id', $subscriptionIds)
                ->update([
                    'status' => SubscriptionStatus::Expired,
                ]);

            $sessionsClosed = $this->sessions->query(true)
                ->whereIn('subscription_id', $subscriptionIds)
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => now(),
                    'last_activity_at' => now(),
                ]);

            return [
                'expired' => $expired,
                'sessions_closed' => $sessionsClosed,
            ];
        });
    }
}
