<?php

namespace App\Http\Controllers\Guests;

use App\Enums\PlanType;
use App\Http\Controllers\__AbstractGuestController;
use App\Models\Plan;
use Illuminate\View\View;

class PlanController extends __AbstractGuestController
{
    public function __invoke(): View
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->with(['options' => fn ($query) => $query->where('is_active', true)->orderBy('duration_months')])
            ->orderBy('name')
            ->get();

        return view('guests.plans', [
            'plans' => $plans,
            'planTypes' => PlanType::cases(),
        ]);
    }
}
