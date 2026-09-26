<?php

namespace App\Enums;

enum SubscriptionPaymentStatus: int
{
    case Pending = 1;
    case Paid = 2;
    case Failed = 3;
    case Refunded = 4;
}
