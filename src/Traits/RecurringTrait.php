<?php

namespace BlessingDube\Recurring\Traits;

use BlessingDube\Recurring\Models\Recurring;
use Carbon\Carbon;
use BlessingDube\Recurring\Exceptions\UnknownFrequencyException;

trait RecurringTrait
{
    public function isRecurring()
    {
        return $this->recurring()->count() > 0;
    }

    public function recurring()
    {
        return $this->morphMany(Recurring::class, 'recurring_id');
    }

    public function recur(string $start, string $end, string $until, string $frequency = 'weekly')
    {
        if (!in_array($frequency, ['daily', 'weekly', 'monthly', 'yearly'])) {
            throw new UnknownFrequencyException('The chosen frequency is unknown', 422);
        }
        $method = 'addWeek';

        if (strtolower($frequency) === 'monthly') {
            $method = 'addMonth';
        }

        if (strtolower($frequency) === 'yearly') {
            $method = 'addYear';
        }

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $untilDate = Carbon::createFromFormat('Y-m-d H:i:s', $until);

        $datesBetween = collect([
            [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')]
        ]);

        $currentStartDate = $startDate;
        $currentEndDate = $endDate;

        while ($currentStartDate->greaterThan($untilDate)) {
            $datesBetween->add([$currentStartDate = $currentStartDate->{$method}, $currentEndDate = $currentEndDate->{$method}]);
        }

        $differenceToEnd = $startDate->diffInDays($until);
        if ($differenceToEnd) {
            $datesBetween->add([$startDate->addDays($differenceToEnd), $endDate->addDays($differenceToEnd)]);
        }

        $dates = $datesBetween->map(function ($date) {
            return [
                'start_date' => $date[0],
                'end_date' => $date[1]
            ];
        })->toArray();

        die($dates);

        return $this->recurring()->createMany($dates);
    }
}