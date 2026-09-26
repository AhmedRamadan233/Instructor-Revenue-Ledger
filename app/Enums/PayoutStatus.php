<?php

namespace App\Enums;

enum PayoutStatus: int
{
    case Pending = 1;
    case Paid = 2;
    case Rejected = 3;
    case Processing = 4;
    case Failed = 5;

    public function reservesBalance(): bool
    {
        return match ($this) {
            self::Pending, self::Processing => true,
            default => false,
        };
    }
}
