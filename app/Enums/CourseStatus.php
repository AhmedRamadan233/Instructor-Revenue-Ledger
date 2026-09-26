<?php

namespace App\Enums;

enum CourseStatus: int
{
    case Draft = 1;
    case Published = 2;
    case Archived = 3;
}
