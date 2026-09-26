<?php

namespace App\Enums;

enum PayoutStatus: int
{
    case Pending = 1;
    case Paid = 2;
    case Rejected = 3;
}
