<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\CourseConsumptionSession;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class ExpireSubscriptions
{
    /**
     * @return array{expired: int, sessions_closed: int}
     */
    public function handle(): array
    {
        return DB::transaction(function (): array {
            $subscriptionIds = Subscription::query()
                ->withoutGlobalScopes()
                ->where('status', SubscriptionStatus::Active)
                ->whereNotNull('ends_at')
                ->where('ends_at', '<', now())
                ->lockForUpdate()
                ->pluck('id');

            if ($subscriptionIds->isEmpty()) {
                return ['expired' => 0, 'sessions_closed' => 0];
            }

            $expired = Subscription::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $subscriptionIds)
                ->update([
                    'status' => SubscriptionStatus::Expired,
                ]);

            $sessionsClosed = CourseConsumptionSession::query()
                ->withoutGlobalScopes()
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
