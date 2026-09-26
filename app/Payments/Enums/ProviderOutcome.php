<?php

namespace App\Payments\Enums;

enum ProviderOutcome: string
{
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case TimeoutAfterSuccess = 'timeout_after_success';
}
