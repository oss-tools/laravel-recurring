<?php

namespace BlessingDube\Recurring\Traits;

use BlessingDube\Recurring\Models\Recurring;
use Carbon\Carbon;
use BlessingDube\Recurring\Exceptions\UnknownFrequencyException;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait RecurringTrait
 * @package BlessingDube\Recurring\Traits
 */
trait RecurringTrait
{
    /**
     * @return bool
     */
    public function getIsRecurringAttribute()
    {
        return $this->recurring()->count() > 0;
    }

    /**
     * @return mixed
     */
    public function recurring()
    {
        return $this->morphMany(Recurring::class, 'recurring');
    }

    /**
     * @param  string  $start
     * @param  string  $finish
     * @param  string  $until
     * @param  string  $frequency
     * @return mixed
     * @throws UnknownFrequencyException
     */
    public function recur(string $start, string $finish, string $until, string $frequency = 'weekly')
    {
        if (!in_array($frequency, ['daily', 'weekly', 'monthly', 'yearly'])) {
            throw new UnknownFrequencyException('The chosen frequency is unknown', 422);
        }

        $method = 'addWeek';

        if (strtolower($frequency) === 'daily') {
            $method = 'addDay';
        }

        if (strtolower($frequency) === 'monthly') {
            $method = 'addMonth';
        }

        if (strtolower($frequency) === 'yearly') {
            $method = 'addYear';
        }

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $finishDate = Carbon::createFromFormat('Y-m-d H:i:s', $finish);
        $untilDate = Carbon::createFromFormat('Y-m-d H:i:s', $until);

        $datesBetween = collect([
            [$startDate->format('Y-m-d H:i:s'), $finishDate->format('Y-m-d H:i:s')]
        ]);

        $currentStartDate = $startDate;
        $currentEndDate = $finishDate;

        while ($currentStartDate->greaterThan($untilDate)) {
            $datesBetween->add([
                $currentStartDate = $currentStartDate->{$method},
                $currentEndDate = $currentEndDate->{$method}
            ]);
        }

        $differenceToEnd = $startDate->diffInDays($until);
        if ($differenceToEnd) {
            $datesBetween->add([$startDate->addDays($differenceToEnd), $finishDate->addDays($differenceToEnd)]);
        }

        $dates = $datesBetween->map(function ($date) {
            return [
                'start_date' => $date[0],
                'end_date' => $date[1]
            ];
        })->toArray();

        return $this->recurring()->createMany($dates);
    }

    /**
     * @return mixed
     */
    public function getRecurringAttribute()
    {
        return $this->recurring()->get();
    }
}
