<?php

namespace App\Enums;

enum SubscriptionStatus: int
{
    case Pending = 1;
    case Active = 2;
    case Expired = 3;
    case Cancelled = 4;
    case Refunded = 5;
}
