<?php

namespace App\Services;

use App\Models\settings;
use Carbon\Carbon;

class FineCalculator
{
    /**
     * Calculate fine for an overdue return using calendar days.
     * Uses startOfDay() so "due by end of day X" = 1 day overdue on the next calendar day.
     *
     * @param Carbon|string $returnDate Due/return date
     * @param Carbon|string|null $asOfDate Date to calculate as of (default: now)
     * @param settings|null $settings Library settings (fine_per_day, fine_grace_period_days). If null, amount is 0.
     * @return array{days_overdue: int, chargeable_days: int, amount: float}
     */
    public static function calculate($returnDate, $asOfDate = null, $settings = null): array
    {
        $returnDate = Carbon::parse($returnDate)->startOfDay();
        $asOf = $asOfDate ? Carbon::parse($asOfDate)->startOfDay() : Carbon::now()->startOfDay();

        $daysOverdue = 0;
        if ($asOf->gt($returnDate)) {
            $daysOverdue = $asOf->diffInDays($returnDate);
        }

        $chargeableDays = 0;
        $amount = 0.0;

        if ($settings && $daysOverdue > 0) {
            $graceDays = (int) ($settings->fine_grace_period_days ?? 0);
            $perDay = (float) ($settings->fine_per_day ?? 0);
            if ($daysOverdue > $graceDays) {
                $chargeableDays = $daysOverdue - $graceDays;
                $amount = $perDay * $chargeableDays;
            }
        }

        return [
            'days_overdue' => $daysOverdue,
            'chargeable_days' => $chargeableDays,
            'amount' => round($amount, 2),
        ];
    }
}
