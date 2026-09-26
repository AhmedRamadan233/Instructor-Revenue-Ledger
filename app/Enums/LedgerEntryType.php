<?php

namespace App\Enums;

enum LedgerEntryType: int
{
    case Earning = 1;
    case Refund = 2;
    case Adjustment = 3;
}
