<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionStatus;
use App\Models\CourseConsumptionSession;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelSubscription
{
    public function handle(Subscription $subscription): Subscription
    {
        if ($subscription->status !== SubscriptionStatus::Active) {
            throw ValidationException::withMessages([
                'subscription' => 'Only active subscriptions can be cancelled.',
            ]);
        }

        return DB::transaction(function () use ($subscription): Subscription {
            $subscription->update([
                'status' => SubscriptionStatus::Cancelled,
                'ends_at' => now(),
            ]);

            CourseConsumptionSession::query()
                ->withoutGlobalScopes()
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
