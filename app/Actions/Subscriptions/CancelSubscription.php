<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelSubscription
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private CourseConsumptionSessionRepositoryInterface $sessions,
    ) {}

    public function handle(Subscription $subscription): Subscription
    {
        if ($subscription->status !== SubscriptionStatus::Active) {
            throw ValidationException::withMessages([
                'subscription' => 'Only active subscriptions can be cancelled.',
            ]);
        }

        return DB::transaction(function () use ($subscription): Subscription {
            $this->subscriptions->update($subscription->id, [
                'status' => SubscriptionStatus::Cancelled,
                'ends_at' => now(),
            ], withoutGlobalScopes: true);

            $this->sessions->query(true)
                ->where('subscription_id', $subscription->id)
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => now(),
                    'last_activity_at' => now(),
                ]);

            return $subscription->refresh();
        });
    }
}
