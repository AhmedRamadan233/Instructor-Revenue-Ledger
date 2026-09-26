<?php

namespace App\Console\Commands\Subscriptions;

use App\Actions\Subscriptions\ExpireSubscriptions;
use Illuminate\Console\Command;

class ExpireSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark active subscriptions past their end date as expired';

    public function handle(ExpireSubscriptions $action): int
    {
        $result = $action->handle();

        $this->info(sprintf(
            'Expired %d subscription(s), closed %d open watch session(s).',
            $result['expired'],
            $result['sessions_closed']
        ));

        return self::SUCCESS;
    }
}
