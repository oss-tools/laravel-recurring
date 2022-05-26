<?php

namespace OSSTools\Recurring\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
     * @var bool
     */
    protected $cascadeOnDelete = false;

    /**
     * @var string
     */
    protected static $recurringDateFormat = 'Y-m-d H:i:s';

    /**
     * @return void
     */
    protected static function bootRecurringTrait(): void
    {
        static::deleting(function ($model) {
            if ($this->cascadeOnDelete) {
                if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                    $this->recurring()->forceDelete();

                    return;
                }

                $this->recurring()->delete();
            }
        });
    }

    /**
     * @return bool
     */
    public function getIsRecurringAttribute(): bool
    {
        return $this->recurring()->count() > 0;
    }

    /**
     * @return mixed
     */
    public function recurring(): MorphMany
    {
        return $this->morphMany(Recurring::class, 'recurring');
    }

    /**
     * @param  string  $start
     * @param  string|null  $end
     * @param  string|null  $until
     * @param  string  $frequency
     * @return Collection
     * @throws UnknownFrequencyException
     */
    public function recur(string $start, string $end = null, string $until = null, string $frequency = 'weekly'): Collection
    {
        if (! in_array($frequency, ['daily', 'weekly', 'monthly', 'yearly'])) {
            throw new UnknownFrequencyException('The chosen frequency is unknown', 422);
        }

        if (method_exists($this, 'getRecurringOptions')) {
            $options = $this->getRecurringOptions();

            $this->startDate = $options['start_date'];
            $this->endDate = $options['end_date'];
        } else {
            $configStartDate = config('laravel-recurring.default_start_date');
            $configEndDate = config('laravel-recurring.default_end_date');
            if ($configStartDate || $configEndDate) {
                $this->startDate = $configStartDate;
                $this->endDate = $configEndDate;
            }
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

        $timeFormat = 'H:i:s';

        $startDate = Carbon::createFromFormat(self::$recurringDateFormat, $start.$this->start_date->format($timeFormat));
        $endDate = $this->endDate && $until ? Carbon::createFromFormat(
            self::$recurringDateFormat,
            $end.$this->end_date->format($timeFormat)
        ) : null;
        $untilDate = $until ? Carbon::createFromFormat(self::$recurringDateFormat, $until.$this->end_date->format($timeFormat))
            : Carbon::createFromFormat(self::$recurringDateFormat, $end.$this->end_date->format($timeFormat))->format(self::$recurringDateFormat);

        $initialDates = $endDate ? [
            $startDate->format(self::$recurringDateFormat),
            $endDate->format(self::$recurringDateFormat),
        ] : [$startDate->format(self::$recurringDateFormat)];

        $datesBetween = collect([$initialDates]);

        $currentStartDate = Carbon::createFromFormat(self::$recurringDateFormat, $startDate);
        $current = Carbon::createFromFormat(self::$recurringDateFormat, $endDate);

        while ($untilDate->greaterThan(Carbon::createFromFormat(self::$recurringDateFormat, $currentStartDate))) {
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
                $startDate->addDays($differenceToEnd)->format(self::$recurringDateFormat),
                $endDate->addDays($differenceToEnd)->format(self::$recurringDateFormat),
            ]) : $datesBetween->add([$startDate->addDays($differenceToEnd)->format(self::$recurringDateFormat)]);
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
     * @return Collection
     */
    public function getRecurringAttribute(): Collection
    {
        return $this->recurring()->get();
    }

    /**
     * Delete all recurrences of the current model.
     * @param bool $forceDelete
     * @return bool
     */
    public function deleteRecurringModels(bool $forceDelete = false): bool
    {
        $method = $forceDelete ? 'forceDelete' : 'delete';
        if ($recurringModels = $this->recurring()->withTrashed()->get()) {
            $failures = false;
            $recurringModels->each(function ($model) use ($method, &$failures) {
                $success = $model->{$method};
                if (! $success) {
                    $failures = true;
                }
            });

            return $failures === false;
        }

        return true;
    }

    /**
     * @param  bool  $asParent
     * @return mixed
     */
    public function next(bool $asParent = false)
    {
        $currentDate = Carbon::now()->format(self::$recurringDateFormat);

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
    public function previous(bool $asParent = false)
    {
        $currentDate = Carbon::now()->format(self::$recurringDateFormat);

        $method = $asParent ? 'first' : 'asParent';

        return $this->recurring()->whereDate('start_date', '<=', $currentDate)->orderBy(
            'start_date',
            'desc'
        )->{$method}();
    }
}
