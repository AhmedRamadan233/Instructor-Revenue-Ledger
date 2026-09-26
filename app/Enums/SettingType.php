<?php

namespace App\Enums;

enum SettingType: int
{
    case String = 1;
    case Integer = 2;
    case Decimal = 3;
    case Boolean = 4;
    case Json = 5;
}
