<?php

namespace OSSTools\Recurring\Traits;

use Carbon\Carbon;
use OSSTools\Recurring\Exceptions\UnknownFrequencyException;
use OSSTools\Recurring\Models\Recurring;

/**
 * Trait RecurringTrait.
 */
trait RecurringTrait
{
    /**
     * @var string
     */
    protected $startDate = 'start_date';

    /**
     * @var string
     */
    protected $endDate = 'end_date';

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
     * @param  string|null  $end
     * @param  string|null  $until
     * @param  string  $frequency
     * @return mixed
     *
     * @throws UnknownFrequencyException
     */
    public function recur(string $start, string $end = null, string $until = null, string $frequency = 'weekly')
    {
        if (! in_array($frequency, ['daily', 'weekly', 'monthly', 'yearly'])) {
            throw new UnknownFrequencyException('The chosen frequency is unknown', 422);
        }

        if (method_exists($this, 'getRecurringOptions')) {
            $options = $this->getRecurringOptions();

            $this->startDate = $options['start_date'];
            $this->endDate = $options['end_date'];
        } elseif (($configStartDate = config('laravel-recurring.default_start_date')) || ($configEndDate = config('laravel-recurring.default_end_date'))) {
            $this->startDate = $configStartDate;
            $this->endDate = $configEndDate;
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

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start.$this->start_date->format('H:i:s'));
        $endDate = $this->endDate && $until ? Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $end.$this->end_date->format('H:i:s')
        ) : null;
        $untilDate = $until ? Carbon::createFromFormat('Y-m-d H:i:s', $until.$this->end_date->format('H:i:s'))
            : Carbon::createFromFormat('Y-m-d H:i:s', $end.$this->end_date->format('H:i:s'))->format('Y-m-d H:i:s');

        $initialDates = $endDate ? [
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s'),
        ] : $initialDates = [$startDate->format('Y-m-d H:i:s')];

        $datesBetween = collect([$initialDates]);

        $currentStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate);
        $current = Carbon::createFromFormat('Y-m-d H:i:s', $endDate);

        while ($untilDate->greaterThan(Carbon::createFromFormat('Y-m-d H:i:s', $currentStartDate))) {
            $this->endDate && $until ? $datesBetween->add([
                $currentStartDate = $currentStartDate->{$method}(),
                $current = $current->{$method}(),
            ]) : $datesBetween->add([
                $currentStartDate = $currentStartDate->{$method}(),
            ]);
        }

        $differenceToEnd = $startDate->diffInDays($until);
        if ($differenceToEnd) {
            $this->endDate && $until ? $datesBetween->add([
                $startDate->addDays($differenceToEnd)->format('Y-m-d H:i:s'),
                $endDate->addDays($differenceToEnd)->format('Y-m-d H:i:s'),
            ]) : $datesBetween->add([$startDate->addDays($differenceToEnd)->format('Y-m-d H:i:s')]);
        }

        $dates = $datesBetween->map(function ($date) use ($until) {
            return $this->endDate && $until ? [
                $this->startDate => $date[0],
                $this->endDate => $date[1],
            ] : [$this->startDate => $date[0]];
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

    /**
     * Delete all recurrences of the current model.
     */
    public function deleteRecurringModels()
    {
        $this->recurring()->each(function ($r) {
            $r->delete();
        });
    }

    /**
     * @param  bool  $asParent
     * @return mixed
     */
    public function next($asParent = false)
    {
        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $method = $asParent ? 'first' : 'asParent';

        return $this->recurring()->whereDate('start_date', '>=', $currentDate)->orderBy(
            'start_date',
            'asc'
        )->{$method}();
    }

    /**
     * @param  bool  $asParent
     * @return mixed
     */
    public function previous($asParent = false)
    {
        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $method = $asParent ? 'first' : 'asParent';

        return $this->recurring()->whereDate('start_date', '<=', $currentDate)->orderBy(
            'start_date',
            'desc'
        )->{$method}();
    }
}
