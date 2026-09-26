<?php

namespace App\Enums;

enum PlanType: int
{
    case Monthly = 1;
    case Quarterly = 3;
    case HalfYearly = 6;
    case Yearly = 12;

    public function label(): string
    {
        return match ($this) {
            self::Monthly => 'Monthly',
            self::Quarterly => 'Quarterly',
            self::HalfYearly => 'Half Yearly',
            self::Yearly => 'Yearly',
        };
    }

    public function durationMonths(): int
    {
        return $this->value;
    }
}
